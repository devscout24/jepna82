<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonthlySubscription;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;
use App\Models\PaymentMethod;
use Stripe\StripeClient;

class SubscribeController extends Controller
{
    /**
     * 📊 Get All Active Plans
     */
    public function plans()
    {
        $plans = MonthlySubscription::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $plans
        ]);
    }

   
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:monthly_subscriptions,id',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $plan = MonthlySubscription::findOrFail($request->plan_id);

        if (! $plan->stripe_price_id || ! str_starts_with($plan->stripe_price_id, 'price_')) {
            return response()->json([
                'status' => false,
                'message' => 'This monthly plan does not have a valid Stripe price ID.'
            ], 422);
        }

        try {
            $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');

            if (! is_string($stripeSecret) || $stripeSecret === '') {
                return response()->json([
                    'status' => false,
                    'message' => 'Stripe secret key is not configured. Set STRIPE_SECRET in your .env or services.stripe.secret in config/services.php'
                ], 500);
            }

            $stripe = new StripeClient($stripeSecret);

            // Ensure Stripe customer exists for the user
            if (! $user->stripe_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name ?? null,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);

                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Build success/cancel URLs (client should provide these in request if needed)
            $successUrl = $request->input('success_url', env('APP_URL') . '/payment-success?session_id={CHECKOUT_SESSION_ID}');
            $cancelUrl = $request->input('cancel_url', env('APP_URL') . '/payment-cancel');

            // Create Stripe Checkout Session for subscription
            $session = $stripe->checkout->sessions->create([
                'customer' => $user->stripe_id,
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Checkout session created',
                'data' => [
                    'plan' => $plan->title,
                    'price' => $plan->price,
                    'checkout_url' => $session->url ?? $session->getUrl(),
                    'session_id' => $session->id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

   
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:monthly_subscriptions,id',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $plan = MonthlySubscription::findOrFail($request->plan_id);

        try {
            $user->subscription('default')->swap($plan->stripe_price_id);

            return response()->json([
                'status' => true,
                'message' => 'Plan changed to ' . $plan->title
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

   
    public function cancel()
    {
        $user = request()->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        try {
            $user->subscription('default')->cancel();

            return response()->json([
                'status' => true,
                'message' => 'Subscription cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
    public function status()
    {
        $user = request()->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $subscription = $user->subscription('default');

        return response()->json([
            'status' => true,
            'data' => [
                'subscribed' => $user->subscribed('default'),
                'on_trial' => $user->onTrial('default'),
                'plan_price_id' => optional($subscription)->stripe_price,
                'ends_at' => optional($subscription)->ends_at,
            ]
        ]);
    }

    
}