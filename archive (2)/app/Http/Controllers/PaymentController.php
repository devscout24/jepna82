<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    /**
     * Handle payment success callback.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $session = null;

        if ($sessionId) {
            try {
                $stripe = new StripeClient(env("STRIPE_SECRET"));
                $session = $stripe->checkout->sessions->retrieve($sessionId);
            } catch (\Exception $e) {
                // Silently fail or log
            }
        }

        return view('payment.success', compact('session'));
    }

    /**
     * Handle payment cancel callback.
     */
    public function cancel()
    {
        return view('payment.cancel');
    }
}
