<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackageAndSubscription;
use App\Models\UserSubscription;
use App\Models\UserWalet;
use App\Models\User;
use App\Models\Payments;
use App\Models\CreditPackPurchase;
use App\Models\Contract;
use App\Jobs\AnalyzeContractJob;
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
        if (!$user) {
            Log::warning("Stripe Hook: User not found for session " . $session->id);
            return;
        }

        $type = $session->metadata->type;

        // 1. Recurring Subscription OR Normal Plan (page_limit or scan_credits)
        if ($type === 'subscription') {
            $package = PackageAndSubscription::find($session->metadata->package_id);
            if (!$package) return;

            // page_limit (billing cycle limit) orthoba scan_credits (monthly renewal credits) theke credits nite hobe
            $initialCredits = $package->page_limit > 0 ? $package->page_limit : ($package->scan_credits ?? 0);

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
                'credits_allocated' => $initialCredits,
                'credits_remaining' => $initialCredits,
                'payment_status' => 'paid',
                'subscription_status' => 'active',
            ]);

            $user->updateBalance($initialCredits, 'subscription_purchase', $package->id, $subscription->id);
            Mail::to($user->email)->send(new SubscriptionWelcomeMail($user, $package));

            // 2. Bulk Credit Pack
        } elseif ($type === 'bulk') {
            $bulkPackageId = $session->metadata->bulk_package_id ?? $session->metadata->package_id;
            $bulkPackage = PackageAndSubscription::find($bulkPackageId);
            if (!$bulkPackage) {
                Log::warning("Stripe Hook: Bulk package not found for session " . $session->id);
                return;
            }

            $bulkCredits = $bulkPackage->page_limit > 0 ? $bulkPackage->page_limit : ($bulkPackage->scan_credits ?? 0);

            $creditPack = CreditPackPurchase::create([
                'user_id' => $user->id,
                'package_id' => $bulkPackage->id,
                'credits_purchased' => $bulkCredits,
                'credits_remaining' => $bulkCredits,
                'status' => 'active',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
            ]);

            Payments::create([
                'user_id' => $user->id,
                'package_id' => $bulkPackage->id,
                'credit_pack_id' => $creditPack->id,
                'amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency ?? "usd"),
                'credits_purchased' => $bulkCredits,
                'payment_method' => 'stripe',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'stripe_customer_id' => $session->customer,
                'status' => 'success',
                'payment_type' => 'bulk',
                'raw_response' => json_encode($session),
            ]);

            $user->updateBalance($bulkCredits, 'bulk_purchase', $bulkPackage->id);
            $user->refresh(); // Refresh user model to get the updated wallet balance from DB

            Mail::to($user->email)->send(new CreditPackPurchaseMail($user, $bulkPackage));

            // 3. One-time Analysis Unlock
        } elseif ($type === 'one_time') {
            $package = PackageAndSubscription::find($session->metadata->package_id);
            $contract = Contract::find($session->metadata->contract_id);
            if (!$package || !$contract) return;

            $mode = $package->package_type === 'one_time_pro' ? 'pro' : 'basic';

            Payments::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency ?? "usd"),
                'payment_method' => 'stripe',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'stripe_customer_id' => $session->customer,
                'status' => 'success',
                'payment_type' => 'one_time',
                'raw_response' => json_encode($session),
            ]);

            $contract->update([
                'is_full_unlocked' => 1,
                'access_level' => 'unlocked',
                'full_unlocked_at' => now(),
                'scan_type' => $mode,
                'billing_mode' => 'one_time',
            ]);

            AnalyzeContractJob::dispatch($contract, $mode);
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
                    'credits_remaining' => $newPackage->page_limit,
                    'next_billing_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_end)->toDateTimeString(),
                ]);

                // user relationship load kora thakte hobe ba direct user model call korte hobe
                $user = User::find($subscription->user_id);
                if ($user) {
                    $user->updateBalance($newPackage->page_limit, 'subscription_upgrade', $newPackage->id, $subscription->id);
                }

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
