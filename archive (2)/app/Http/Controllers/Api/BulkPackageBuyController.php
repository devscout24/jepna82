<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeContractJob;
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
use App\Models\SystemSetting;

class BulkPackageBuyController extends Controller
{
    use ApiResponse;

    // -----------------------------------------------------------------------
    // BULK CREDIT PACK PURCHASE
    // -----------------------------------------------------------------------

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

            if (!$bulkPackage || $bulkPackage->package_type !== 'bulk') {
                return $this->error([], 'Invalid bulk package.', 422);
            }

            $user   = Auth::guard('api')->user();
            $stripe = new StripeClient(env("STRIPE_SECRET"));

            $session = $stripe->checkout->sessions->create([
                'customer_email' => $user->email,
                'line_items'     => [[
                    'price_data' => [
                        'currency'     => strtolower($bulkPackage->currency ?? 'usd'),
                        'product_data' => [
                            'name' => $bulkPackage->title,
                        ],
                        'unit_amount' => (int)($bulkPackage->final_price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => env('APP_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => env('APP_URL') . '/payment/cancel',
                'metadata'    => [
                    'user_id'         => $user->id,
                    'bulk_package_id' => $bulkPackage->id,
                    'type'            => 'bulk',
                ],
            ]);

            return $this->success([
                'checkout_url' => $session->url,
                'session_id'   => $session->id,
            ], 'Checkout session created.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    // -----------------------------------------------------------------------
    // ONE-TIME PACKAGE PURCHASE (one_time_basic / one_time_pro)
    // User wallet 0 thakle, already uploaded contract er jonne payment
    // Re-upload lagbe na — payment er pore same contract analyze hobe
    // -----------------------------------------------------------------------

    public function buyOneTimePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'package_id'  => 'required|exists:package_and_subscriptions,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $contract = Contract::findOrFail($request->contract_id);
            $package  = PackageAndSubscription::findOrFail($request->package_id);
            $user     = Auth::guard('api')->user();

            // Validate package type
            if (!in_array($package->package_type, ['one_time_basic', 'one_time_pro'])) {
                return $this->error([], 'Invalid package type. Only one_time_basic or one_time_pro allowed.', 422);
            }

            // Contract must belong to logged-in user
            if ($contract->user_id !== $user->id) {
                return $this->error([], 'Unauthorized access to this contract.', 403);
            }

            // Already unlocked?
            if ($contract->is_full_unlocked == 1) {
                return $this->error([], 'This contract is already fully unlocked.', 422);
            }

            // Price Calculation:
            // If extra_page_rate is set → price = page_count × rate (dynamic per page)
            // Otherwise               → use flat final_price
            $pageCount = $contract->page_count ?? 1;

            if ($package->extra_page_rate > 0) {
                $totalPrice = $pageCount * $package->extra_page_rate;
                $priceType  = 'per_page';
            } else {
                $totalPrice = $package->final_price;
                $priceType  = 'flat';
            }

            $scanLabel = $package->package_type === 'one_time_pro' ? 'Pro' : 'Basic';

            $stripe  = new StripeClient(env("STRIPE_SECRET"));
            $session = $stripe->checkout->sessions->create([
                'customer_email' => $user->email,
                'line_items'     => [[
                    'price_data' => [
                        'currency'     => strtolower($package->currency ?? 'usd'),
                        'product_data' => [
                            'name'        => "{$package->title} — {$scanLabel} Contract Analysis",
                            'description' => "Full {$scanLabel} analysis of: {$contract->original_filename} ({$pageCount} pages)",
                        ],
                        'unit_amount' => (int)($totalPrice * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => env('APP_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => env('APP_URL') . '/payment/cancel',
                'metadata'    => [
                    'user_id'     => $user->id,
                    'contract_id' => $contract->id,
                    'package_id'  => $package->id,
                    'type'        => 'one_time',
                ],
            ]);

            return $this->success([
                'checkout_url'    => $session->url,
                'session_id'      => $session->id,
                'price_breakdown' => [
                    'page_count'  => $pageCount,
                    'rate'        => $package->extra_page_rate > 0 ? $package->extra_page_rate : null,
                    'total_price' => round($totalPrice, 2),
                    'currency'    => strtoupper($package->currency ?? 'USD'),
                    'price_type'  => $priceType,
                ],
            ], 'Checkout session created. Proceed to payment.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    // -----------------------------------------------------------------------
    // STRIPE WEBHOOK — handles both 'bulk' and 'one_time'
    // -----------------------------------------------------------------------

    public function handleBulkPackageWebhook(Request $request)
    {
        $payload         = $request->getContent();
        $sig_header      = $request->header('Stripe-Signature');

        $endpoint_secret = env("STRIPE_WEBHOOK_SECRET_BULK");

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

            case 'charge.succeeded':
                // Optional: Handle charge.succeeded if needed
                break;

            default:
                Log::info("Unhandled Stripe event type: " . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        $user = User::find($session->metadata->user_id);
        if (!$user) {
            Log::warning("Stripe Hook: User not found for session " . $session->id);
            return;
        }

        $type = $session->metadata->type ?? 'bulk';

        // ── BULK CREDIT PACK ──────────────────────────────────────────────
        if ($type === 'bulk') {
            $bulkPackage = PackageAndSubscription::find($session->metadata->bulk_package_id);
            if (!$bulkPackage) {
                Log::warning("Stripe Hook: Bulk package not found for session " . $session->id);
                return;
            }

            // 1. Credit Pack Purchase record
            $creditPack = CreditPackPurchase::create([
                'user_id'                  => $user->id,
                'package_id'               => $bulkPackage->id,
                'credits_purchased'        => $bulkPackage->page_limit,
                'credits_remaining'        => $bulkPackage->page_limit,
                'status'                   => 'active',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'expires_at'               => null,
            ]);

            // 2. Payment Record
            Payments::create([
                'user_id'                  => $user->id,
                'package_id'               => $bulkPackage->id,
                'credit_pack_id'           => $creditPack->id,
                'amount'                   => $session->amount_total / 100,
                'currency'                 => strtoupper($session->currency ?? "usd"),
                'credits_purchased'        => $bulkPackage->page_limit,
                'payment_method'           => 'stripe',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'stripe_customer_id'       => $session->customer,
                'status'                   => 'success',
                'payment_type'             => 'bulk',
                'raw_response'             => json_encode($session),
            ]);

            // 3. Add credits to user wallet
            $user->updateBalance($bulkPackage->page_limit, 'bulk_package_purchase', $bulkPackage->id);

            Log::info("Bulk credit pack purchased: user {$user->id}, credits: {$bulkPackage->page_limit}");

            // ── ONE-TIME PACKAGE (one_time_basic / one_time_pro) ─────────────
        } elseif ($type === 'one_time') {
            $package  = PackageAndSubscription::find($session->metadata->package_id);
            $contract = Contract::find($session->metadata->contract_id);

            if (!$package || !$contract) {
                Log::warning("Stripe Hook: Package or Contract not found for one_time session " . $session->id);
                return;
            }

            // Determine analysis mode
            $mode = $package->package_type === 'one_time_pro' ? 'pro' : 'basic';

            // 1. Payment Record
            Payments::create([
                'user_id'                  => $user->id,
                'package_id'               => $package->id,
                'credit_pack_id'           => null,
                'amount'                   => $session->amount_total / 100,
                'currency'                 => strtoupper($session->currency ?? "usd"),
                'credits_purchased'        => 0,
                'payment_method'           => 'stripe',
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'stripe_customer_id'       => $session->customer,
                'status'                   => 'success',
                'payment_type'             => 'one_time',
                'raw_response'             => json_encode($session),
            ]);

            // 2. Instantly unlock the contract
            $contract->update([
                'is_full_unlocked' => 1,
                'access_level'     => 'unlocked',
                'full_unlocked_at' => now(),
                'scan_type'        => $mode,
                'billing_mode'     => 'one_time',
            ]);

            // 3. Dispatch full AI analysis job (no re-upload needed!)
            AnalyzeContractJob::dispatch($contract, $mode);

            Log::info("One-time payment fulfilled: contract {$contract->id}, mode: {$mode}, user: {$user->id}");
        }
    }
}
