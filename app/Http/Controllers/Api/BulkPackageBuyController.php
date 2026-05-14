<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\PackageAndSubscription;
use App\Models\User;
use App\Models\Payments;
use App\Models\CreditPackPurchase;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;
use Stripe\Webhook;

class BulkPackageBuyController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulk_package_id' => 'required|exists:package_and_subscriptions,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $bulkPackage = PackageAndSubscription::find($request->bulk_package_id);
            if (!$bulkPackage) {
                return $this->error([], 'Bulk package not found', 422);
            }

            $user = Auth::guard('api')->user();
            $stripe = new StripeClient(env("STRIPE_SECRET"));

            $session = $stripe->checkout->sessions->create([
                'customer_email' => $user->email,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($bulkPackage->currency ?? 'usd'),
                            'product_data' => [
                                'name' => $bulkPackage->title,
                            ],
                            'unit_amount' => (int)($bulkPackage->final_price * 100),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => env('APP_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('APP_URL') . '/payment/cancel',
                'metadata' => [
                    'user_id' => $user->id,
                    'bulk_package_id' => $bulkPackage->id,
                    'analysis_id' => $request->analysis_id ?? null, // Optional analysis to fulfill
                ],
            ]);

            return response()->json([
                'status' => true,
                'url' => $session->url,
            ]);

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }
    /**
     * One-time top-up for a specific contract analysis.
     */
    public function oneTimeTopUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'analysis_id' => 'required|exists:contract_analyses,id',
            'package_id' => 'required|exists:package_and_subscriptions,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $analysis = Contract::findOrFail($request->analysis_id);
            $package = PackageAndSubscription::findOrFail($request->package_id);
            $user = Auth::guard('api')->user();
            
            // Calculate rate based on the selected package
            $ratePerPage = $package->extra_page_rate > 0 ? $package->extra_page_rate : ($package->final_price / max(1, $package->page_limit));
            
            $wallet = $user->wallets()->latest()->first();
            $currentBalance = $wallet ? $wallet->current_balance : 0;
            $neededPages = $analysis->total_pages - $currentBalance;
            
            if ($neededPages <= 0) {
                return $this->error([], "You already have enough credits.", 422);
            }

            $totalCost = $neededPages * $ratePerPage;
            $stripe = new StripeClient(env("STRIPE_SECRET"));

            $session = $stripe->checkout->sessions->create([
                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($package->currency ?? 'usd'),
                        'product_data' => [
                            'name' => "One-time analysis top-up ({$package->title})",
                            'description' => "Processing {$neededPages} pages for: {$analysis->file_name}",
                        ],
                        'unit_amount' => (int)($totalCost * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => env('APP_URL') . "/payment/success?session_id={CHECKOUT_SESSION_ID}&analysis_id={$analysis->id}",
                'cancel_url' => env('APP_URL') . '/payment/cancel',
                'metadata' => [
                    'user_id' => $user->id,
                    'analysis_id' => $analysis->id,
                    'package_id' => $package->id,
                    'type' => 'one_time_topup',
                    'needed_pages' => $neededPages
                ],
            ]);

            return response()->json(['status' => true, 'url' => $session->url]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function handleBulkPackageWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env("STRIPE_WEBHOOK_SECRET");

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error("Stripe Webhook Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleBulkPackageCheckoutSessionCompleted($event->data->object);
                break;

            default:
                Log::info("Unhandled Stripe event type: " . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleBulkPackageCheckoutSessionCompleted($session)
    {
        $user = User::find($session->metadata->user_id);
        if (!$user) {
            Log::warning("Stripe Hook: User not found for session " . $session->id);
            return;
        }

        $type = $session->metadata->type ?? 'bulk';

        if ($type === 'bulk') {
            $bulkPackage = PackageAndSubscription::find($session->metadata->bulk_package_id);
            if (!$bulkPackage) {
                Log::warning("Stripe Hook: Package not found for session " . $session->id);
                return;
            }

            // 1. Credit Pack Purchase record create kora
            $creditPack = CreditPackPurchase::create([
                'user_id' => $user->id,
                'package_id' => $bulkPackage->id,
                'credits_purchased' => $bulkPackage->page_limit,
                'credits_remaining' => $bulkPackage->page_limit,
                'status' => 'active',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'expires_at' => null,
            ]);

            // 2. Payment Record Save kora
            Payments::create([
                'user_id' => $user->id,
                'package_id' => $bulkPackage->id,
                'credit_pack_id' => $creditPack->id,
                'amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency ?? "usd"),
                'credits_purchased' => $bulkPackage->page_limit,
                'payment_method' => 'stripe',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'stripe_customer_id' => $session->customer,
                'status' => 'success',
                'payment_type' => 'bulk',
                'raw_response' => json_encode($session),
            ]);

            // 3. Update User Wallet (Credits)
            $user->updateBalance($bulkPackage->page_limit, 'bulk_package_purchase', $bulkPackage->id);

        } elseif ($type === 'one_time_topup') {
            // Fulfill the specific analysis immediately
            $neededPages = $session->metadata->needed_pages ?? 0;
            
            // Grant temporary credits then they will be deducted during fulfillment
            $user->updateBalance($neededPages, 'one_time_topup');

            // Payment record for top-up
            Payments::create([
                'user_id' => $user->id,
                'package_id' => $session->metadata->analysis_id, // Reference analysis ID
                'amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency ?? "usd"),
                'credits_purchased' => $neededPages,
                'payment_method' => 'stripe',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'stripe_customer_id' => $session->customer,
                'status' => 'success',
                'payment_type' => 'bulk',
                'raw_response' => json_encode($session),
            ]);
        }

        // 4. Check if there's a pending analysis to fulfill (Works for both cases)
        if (isset($session->metadata->analysis_id)) {
            $this->fulfillPendingAnalysis($session->metadata->analysis_id);
        }

        Log::info("Payment fulfilled for user: " . $user->id . " Type: " . $type);
    }

    /**
     * Fulfill the analysis after payment.
     */
    protected function fulfillPendingAnalysis($analysisId)
    {
        $analysis = \App\Models\Contract::find($analysisId);
        if ($analysis) {
            // Find the last payment for this analysis to determine the package
            $payment = \App\Models\Payments::where('package_id', $analysisId)
                ->where('user_id', $analysis->user_id)
                ->latest()
                ->first();

            $mode = 'full';
            if ($payment) {
                $pkg = \App\Models\PackageAndSubscription::find($payment->credit_pack_id); // In my previous code, credit_pack_id was used for package reference in Payments
                if ($pkg) {
                    if ($pkg->package_type === 'one_time_basic') $mode = 'basic';
                    if ($pkg->package_type === 'one_time_pro') $mode = 'pro';
                }
            }

            // Dispatch the job for full/basic/pro analysis
            \App\Jobs\AnalyzeContractJob::dispatch($analysis, $mode);
            
            Log::info("Pending analysis {$analysisId} dispatched to job with mode {$mode} after payment.");
        }
    }
}
