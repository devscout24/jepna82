<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackageAndSubscription;
use App\Models\UserSubscription;
use App\Models\UserWalet;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionWelcomeMail;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\CreditPackPurchaseMail;
use App\Mail\PaymentFailedMail;
use Stripe\StripeClient;
use Stripe\Webhook;
use Carbon\Carbon;
use App\Models\SystemSetting;

class UserSubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * Create a Stripe Checkout Session or Upgrade existing subscription.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:package_and_subscriptions,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $user = Auth::guard('api')->user();
            $package = PackageAndSubscription::findOrFail($request->package_id);

            if (!$package->stripe_price_id) {
                return $this->error([], "This package does not have a valid Stripe Price ID.", 422);
            }

            $stripe = new StripeClient(env("STRIPE_SECRET"));

            // Check if user has an existing active subscription
            $existingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_status', 'active')
                ->whereNotNull('stripe_subscription_id')
                ->first();

            if ($existingSubscription && $package->package_type === 'subscription') {
                // Perform Upgrade/Swap
                $stripeSub = $stripe->subscriptions->retrieve($existingSubscription->stripe_subscription_id);

                $stripe->subscriptions->update($existingSubscription->stripe_subscription_id, [
                    'cancel_at_period_end' => false,
                    'proration_behavior' => 'always_invoice',
                    'items' => [
                        [
                            'id' => $stripeSub->items->data[0]->id,
                            'price' => $package->stripe_price_id,
                        ],
                    ],
                ]);

                return $this->success([], "Subscription upgrade request processed. You will be charged the difference shortly.");
            }

            $session = $stripe->checkout->sessions->create([
                'customer_email' => $user->email,
                'line_items' => [[
                    'price' => $package->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => $package->package_type === 'subscription' ? 'subscription' : 'payment',
                'success_url' => env('APP_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('APP_URL') . '/payment/cancel',
                'metadata' => [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'type' => $package->package_type,
                ],
            ]);

            return $this->success([
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ], "Checkout session created successfully.");
        } catch (\Exception $e) {
            return $this->error("Something went wrong", $e->getMessage(), 500);
        }
    }

    /**
     * Handle Stripe Webhooks
     */
    public function handlesubscription(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        $endpoint_secret = env("STRIPE_WEBHOOK_SECRET_SUBSCRIPTION");

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error("Stripe Webhook Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'invoice.paid':
                $this->handleInvoicePaid($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            default:
                Log::info("Unhandled Stripe event type: " . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle initial payment/subscription completion.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $user = User::find($session->metadata->user_id);
        $package = PackageAndSubscription::find($session->metadata->package_id);
        $type = $session->metadata->type;

        if (!$user || !$package) {
            Log::warning("Stripe Hook: User or Package not found for session " . $session->id);
            return;
        }

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'stripe_subscription_id' => $session->subscription ?? null,
            'stripe_customer_id' => $session->customer,
            'stripe_invoice_id' => $session->invoice ?? null,
            'amount' => $session->amount_total / 100,
            'currency' => strtoupper($session->currency ?? "usd"),
            'start_date' => now(),
            'end_date' => $this->calculateNextBillingDate($session),
            'next_billing_date' => $this->calculateNextBillingDate($session),
            'credits_allocated' => $package->page_limit,
            'credits_remaining' => $package->page_limit,
            'payment_status' => 'paid',
            'subscription_status' => 'active',
        ]);

        $user->updateBalance($package->page_limit, 'subscription_purchase', $package->id, $subscription->id);

        if ($type === 'subscription') {
            Mail::to($user->email)->send(new SubscriptionWelcomeMail($user, $package));
        } else {
            Mail::to($user->email)->send(new CreditPackPurchaseMail($user, $package));
        }
    }

    /**
     * Handle subscription upgrade/downgrade.
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = UserSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $newPriceId = $stripeSubscription->items->data[0]->price->id;
            $newPackage = PackageAndSubscription::where('stripe_price_id', $newPriceId)->first();

            if ($newPackage && $subscription->package_id !== $newPackage->id) {
                $subscription->update([
                    'package_id' => $newPackage->id,
                    'credits_allocated' => $newPackage->page_limit,
                    'credits_remaining' => $subscription->credits_remaining + $newPackage->page_limit,
                    'next_billing_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_end)->toDateTimeString(),
                ]);

                $subscription->user->updateBalance($newPackage->page_limit, 'subscription_upgrade', $newPackage->id, $subscription->id);

                Log::info("Subscription upgraded for user: " . $subscription->user_id);
            }
        }
    }

    /**
     * Handle recurring subscription payment.
     */
    protected function handleInvoicePaid($invoice)
    {
        if (!isset($invoice->subscription)) return;

        $subscription = UserSubscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($subscription) {
            $package = $subscription->package;
            $user = $subscription->user;

            $subscription->update([
                'payment_status' => 'paid',
                'subscription_status' => 'active',
                'credits_remaining' => $subscription->credits_remaining + $package->scan_credits,
            ]);

            // Add credits for the new period
            $user->updateBalance($package->scan_credits, 'subscription_renewal', $package->id, $subscription->id);

            Log::info("Subscription renewed for user: " . $user->id);
        }
    }

    /**
     * Handle failed subscription payment.
     */
    protected function handleInvoicePaymentFailed($invoice)
    {
        $subscription = UserSubscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if ($subscription) {
            $subscription->update(['payment_status' => 'failed']);
            $user = $subscription->user;

            Mail::to($user->email)->send(new PaymentFailedMail($user));
            Log::warning("Subscription payment failed for user: " . $user->id);
        }
    }

    /**
     * Handle subscription cancellation or expiry.
     */
    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = UserSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'subscription_status' => 'expired',
                'cancelled_at' => now(),
            ]);

            $user = $subscription->user;
            $package = $subscription->package;

            Mail::to($user->email)->send(new SubscriptionExpiredMail($user, $package));
            Log::info("Subscription expired/deleted for user: " . $user->id);
        }
    }

    protected function calculateNextBillingDate($session)
    {
        // Check if subscription ID exists and is not null
        if (!isset($session->subscription) || empty($session->subscription)) {
            return now()->addMonth()->toDateTimeString();
        }

        try {
            $stripe = new StripeClient(env("STRIPE_SECRET"));
            $sub = $stripe->subscriptions->retrieve($session->subscription);

            // Safety check for current_period_end
            if (isset($sub->current_period_end) && !empty($sub->current_period_end)) {
                return Carbon::createFromTimestamp($sub->current_period_end)->toDateTimeString();
            }

            return now()->addMonth()->toDateTimeString();
        } catch (\Exception $e) {
            Log::error("Stripe Subscription Retrieve Error: " . $e->getMessage());
            return now()->addMonth()->toDateTimeString();
        }
    }
}
