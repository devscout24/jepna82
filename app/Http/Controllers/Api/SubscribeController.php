<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonthlySubscription;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;
use App\Models\PaymentMethod;

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

    /**
     * 🔥 Subscribe to Plan
     */
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

        $defaultPaymentMethod = $user->defaultPaymentMethod();
        $paymentMethodId = $defaultPaymentMethod?->id;

        if (! $paymentMethodId) {
            $paymentMethodId = 'pm_card_visa';
        }

        try {
            // Create Stripe Customer
            $user->createOrGetStripeCustomer();

            // Create Subscription using plan stripe_price_id
            $subscription = $user->newSubscription('default', $plan->stripe_price_id)
                ->create($paymentMethodId);

            return response()->json([
                'status' => true,
                'message' => 'Subscribed to ' . $plan->title,
                'data' => [
                    'plan' => $plan->title,
                    'price' => $plan->price,
                    'subscription' => $subscription
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔁 Change Plan (Upgrade/Downgrade)
     */
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

    /**
     * ❌ Cancel Subscription
     */
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

    /**
     * 📈 Subscription Status
     */
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