<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = Auth::guard('api')->user();

        // 1. Stats Cards
        $totalScans = Contract::query()->where('user_id', $user->id)->count();
        $scansThisWeek = Contract::query()->where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        $wallet = $user->wallets()->latest()->first();
        $remainingCredits = $wallet ? $wallet->current_balance : 0;
        $totalUsedCredits = $wallet ? $wallet->total_used : 0;
        $totalPurchasedCredits = $wallet ? ($wallet->current_balance + $wallet->total_used) : 0;

        // Current Plan Logic
        $currentPlanName = "Free Plan";
        $isPro = false;
        $scanLimit = $totalPurchasedCredits > 0 ? $totalPurchasedCredits : 0;

        // 1. Check for Active Subscription
        $activeSub = $user->subscriptions()
            ->where('subscription_status', 'active')
            ->with('package')
            ->latest()
            ->first();

        if ($activeSub && $activeSub->package) {
            $currentPlanName = $activeSub->package->title;
            // For subscriptions, we can show the package limit or the wallet balance
            $scanLimit = $activeSub->package->page_limit;
            $isPro = true;
        } else {
            // 2. Check for One-Time Purchase / Bulk Credits
            $lastPurchase = \App\Models\WalletTransaction::query()
                ->where('user_id', $user->id)
                ->whereIn('source', ['one_time_purchase', 'bulk_purchase'])
                ->with('package')
                ->latest()
                ->first();

            if ($lastPurchase && $lastPurchase->package) {
                $currentPlanName = $lastPurchase->package->title;
                if (in_array($lastPurchase->package->package_type, ['one_time_pro', 'bulk'])) {
                    $isPro = true;
                }
            } else {
                // 3. Fallback to Free Preview Package Details
                $freePackage = \App\Models\PackageAndSubscription::where('package_type', 'free_preview')->first();
                $currentPlanName = $freePackage ? $freePackage->title : "Free Plan";
                if ($scanLimit == 0) {
                    $scanLimit = $freePackage ? $freePackage->page_limit : 2;
                }
            }
        }

        // 2. Scan History (List)
        $scanHistory = Contract::query()->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'filename' => $contract->original_filename,
                    'date' => $contract->created_at->format('M d, Y'),
                    'pages' => $contract->page_count,
                    'risk_level' => $contract->risk_level ?? 'N/A',
                    'status' => $contract->status,
                ];
            });

        // 3. Scan Usage Calculation using Wallet Data
        $totalAllowed = $scanLimit > 0 ? $scanLimit : 1; // Avoid divide by zero
        $usedPercentage = round(($totalUsedCredits / ($totalAllowed ?: 1)) * 100);
        // Ensure percentage doesn't exceed 100 if they used more than "limit" (though logic usually prevents it)
        $usedPercentage = min(100, $usedPercentage);

        return $this->success([
            'stats' => [
                'total_scans' => [
                    'value' => $totalScans,
                    'sub_label' => "+{$scansThisWeek} this week"
                ],
                'remaining_credits' => [
                    'value' => $remainingCredits,
                    'sub_label' => "Total Used: " . $totalUsedCredits
                ],
                'current_plan' => [
                    'value' => $currentPlanName,
                    'sub_label' => $isPro ? "Full Analysis Plan" : "Limited Access Plan"
                ]
            ],
            'scan_history' => $scanHistory,
            'usage' => [
                'total' => $totalAllowed,
                'used' => $totalUsedCredits,
                'left' => $remainingCredits,
                'percentage' => $usedPercentage,
                'plan_label' => strtoupper($currentPlanName)
            ]
        ], 'Dashboard data retrieved successfully.');
    }
}
