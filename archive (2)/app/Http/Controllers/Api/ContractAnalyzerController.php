<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeContractJob;
use App\Models\Contract;
use App\Models\PackageAndSubscription;
use App\Models\WalletTransaction;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContractAnalyzerController extends Controller
{
    use ApiResponse;

    /**
     * Initial upload and partial analysis.
     */
    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors(), 422);
        }

        try {
            $user = Auth::guard('api')->user();
            $file = $request->file('contract_file');

            // 1. First get total pages and decide scan type while file is in temp
            $totalPages = $this->countPages($file);

            $wallet = $user->wallets()->latest()->first();
            $currentBalance = $wallet ? $wallet->current_balance : 0;

            // Determine Scan Type and Billing Mode
            $scanType = 'partial'; // Default for free users
            $billingMode = 'free';

            if ($currentBalance >= $totalPages) {
                // If they have credits, identify if it's One Time Basic/Pro or general Credit (Bulk/Subscription)
                $scanType = 'pro';

                $lastTransaction = WalletTransaction::query()->where('user_id', $user->id)
                    ->whereIn('source', ['subscription_purchase', 'subscription_renewal', 'bulk_purchase', 'one_time_purchase', 'admin_grant'])
                    ->latest()
                    ->first();

                if ($lastTransaction) {
                    if ($lastTransaction->source === 'one_time_purchase') {
                        // Check the actual package type from the associated package
                        $package = PackageAndSubscription::query()->find($lastTransaction->package_id);
                        if ($package && in_array($package->package_type, ['one_time_basic', 'one_time_pro'])) {
                            $billingMode = $package->package_type; // Set to 'one_time_basic' or 'one_time_pro'
                        } else {
                            $billingMode = 'one_time';
                        }
                    } else {
                        // For bulk or subscription, we just use a general 'credit' mode as requested
                        $billingMode = 'credit';
                    }
                } else {
                    $billingMode = 'credit'; // Fallback
                }
            } else {
                // Fetch dynamic limit from 'free_preview' package
                $freePackage = PackageAndSubscription::query()->where('package_type', 'free_preview')->first();
                $freeLimit = $freePackage ? $freePackage->page_limit : 2; // Default to 2 if not found

                // Check lifetime free limit
                $completedScansCount = Contract::query()->where('user_id', $user->id)
                    ->whereIn('status', ['completed', 'preview_ready'])
                    ->count();

                if ($completedScansCount >= $freeLimit) {
                    return $this->returnPaywallOnly($user);
                }

                $billingMode = 'free'; // Set billing mode to free
            }

            // 2. NOW store the file
            $filePath = $file->store('contracts', 'public');
            $fullPath = storage_path('app/public/' . $filePath);

            $fileText = null;
            if ($this->getFileType($file) === 'pdf') {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($fullPath);
                    $fileText = $pdf->getText();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("PDF Text Extraction failed: " . $e->getMessage());
                }
            }

            $contract = Contract::create([
                'user_id' => $user->id,
                'original_filename' => $file->getClientOriginalName(),
                'file' => $filePath,
                'file_text' => $fileText,
                'page_count' => $totalPages,
                'file_type' => $this->getFileType($file),
                'scan_type' => $scanType,
                'billing_mode' => $billingMode,
                'status' => 'uploaded',
                'access_level' => $scanType === 'pro' ? 'unlocked' : 'preview',
                'is_full_unlocked' => $scanType === 'pro' ? 1 : 0,
            ]);

            // 3. Dispatch Background Job
            AnalyzeContractJob::dispatch($contract, 'pro'); // Always run the full scan in the background

            return $this->success([
                'contract_id' => $contract->id,
                'status' => $scanType === 'pro' ? 'Analyzing full document...' : 'Analyzing partially...',
                'mode' => $scanType,
                'message' => $scanType === 'pro' ? 'Your full pro analysis is being generated.' : 'Your partial preview is being generated.'
            ], 'Analysis started.');
        } catch (Exception $e) {
            Log::error("Analysis Error: " . $e->getMessage());
            return $this->error('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * When user exhausted free scans and has no credits.
     */
    protected function returnPaywallOnly($user)
    {
        return response()->json([
            'status' => 'insufficient_credits',
            'message' => 'You have exhausted your 2 free lifetime scans. Please upgrade to continue.',
            'data' => [
                'options' => [
                    'one_time' => PackageAndSubscription::query()->whereIn('package_type', ['one_time_basic', 'one_time_pro'])->get(),
                    'credit_packs' => PackageAndSubscription::query()->where('package_type', 'bulk')->get(),
                    'subscriptions' => PackageAndSubscription::query()->where('package_type', 'subscription')->get()
                ]
            ]
        ], 402);
    }

    /**
     * Get details for paywall (pricing, etc.) after partial result is ready.
     */
    public function contractResult(Request $request, $contractId)
    {
        $contract = Contract::query()->where('user_id', Auth::id())->findOrFail($contractId);

        // Ensure JSON columns are handled as arrays
        $details = is_array($contract->contract_details) ? $contract->contract_details : json_decode((string)$contract->contract_details, true);
        $risks = is_array($contract->risks) ? $contract->risks : json_decode((string)$contract->risks, true);
        $summary = is_array($contract->summary) ? $contract->summary : json_decode((string)$contract->summary, true);
        $fairness = is_array($contract->fairness) ? $contract->fairness : json_decode((string)$contract->fairness, true);
        $key_clauses = is_array($contract->key_clauses) ? $contract->key_clauses : json_decode((string)$contract->key_clauses, true);
        $risk_overview = is_array($contract->risk_overview) ? $contract->risk_overview : json_decode((string)$contract->risk_overview, true);

        $result = [
            'id' => $contract->id,
            'contract_details' => [
                'contract_title' => $contract->contract_title ?? $contract->original_filename,
                'document_type' => $contract->document_type,
                'parties_involved' => $details['parties'] ?? null,
                'effective_date' => $details['effective_date'] ?? null,
                'end_date' => $details['end_date'] ?? null,
                'jurisdiction' => $details['jurisdiction_governing_law'] ?? null,
                'renewal_terms' => $details['renewal_terms'] ?? null,
            ],
            'summary' => [
                'text' => $contract->short_summary,
                'key_points' => $summary['key_points'] ?? [],
            ],
            'risk_score' => [
                'score' => $contract->risk_score,
                'level' => $contract->risk_level,
                'explanation' => $risk_overview['explanation'] ?? ($contract->risk_score_explanation ?? ''),
            ],
            'fairness' => [
                'score' => $contract->fairness_score,
                'text' => $fairness['explanation'] ?? ($contract->fairness ?? null),
            ],
            'risks' => collect($risks)->map(function ($risk) use ($contract) {
                return [
                    'title' => $risk['title'] ?? '',
                    'severity' => $risk['severity'] ?? '',
                    'category' => $risk['category'] ?? '',
                    'explanation' => $risk['explanation'] ?? '',
                    'impact' => $risk['impact'] ?? '',
                    'suggestion' => $risk['suggestion'] ?? '',
                    'is_locked' => ($contract->is_full_unlocked == 0),
                ];
            }),
            'key_clauses' => collect($key_clauses)->map(function ($clause, $type) {
                return [
                    'type' => is_numeric($type) ? ($clause['type'] ?? 'General') : $type,
                    'text' => $clause['text'] ?? '',
                    'explanation' => $clause['explanation'] ?? '',
                ];
            }),
            'recommendations' => $contract->recommendations ?? [],
            'action_items' => $contract->action_items ?? [],
            'signature_details' => [
                'status' => $contract->signature_details['status'] ?? 'Not Signed',
                'signatories' => $contract->signature_details['signatories'] ?? [],
            ],
            'is_full_unlocked' => $contract->is_full_unlocked,
            'billing_mode' => $contract->billing_mode,
        ];

        return $this->success($result, 'Analysis detail retrieved successfully.');
    }

    /**
     * Fetch analysis results with blur/lock logic.
     */
    public function getAnalysisResult($id)
    {
        $contract = Contract::query()->where('user_id', Auth::id())->findOrFail($id);

        if ($contract->status !== 'completed' && $contract->status !== 'preview_ready') {
            return $this->success([
                'status' => $contract->status,
                'message' => 'Analysis is still in progress.'
            ]);
        }

        $data = [
            'id' => $contract->id,
            'title' => $contract->contract_title ?? $contract->original_filename,
            'risk_score' => $contract->risk_score,
            'risk_level' => $contract->risk_level,
            'summary' => $contract->short_summary,
            'scan_type' => $contract->scan_type, // partial, basic, pro
            'status' => $contract->status,
            'legal_disclaimer' => 'This analysis is generated by AI (RiskShield AI) for informational purposes only. It does not constitute legal advice. We recommend consulting with a legal professional before signing any contract.',
        ];

        if ($contract->is_full_unlocked == 0) {
            // Limited Data for Free/Partial view
            $fullRisks = is_array($contract->risks) ? $contract->risks : [];
            $data['risks'] = array_slice($fullRisks, 0, 2);
            $data['is_locked'] = true;
            $data['locked_message'] = "⚠️ " . max(0, $contract->risk_count - count($data['risks'])) . " hidden risks detected. Unlock full analysis to protect yourself.";

            $data['full_summary'] = null;
            $data['recommendations'] = [];
            $data['key_clauses'] = null;
        } else {
            // Unlocked view (Basic or Pro)
            $data['is_locked'] = false;
            $data['full_summary'] = $contract->summary;
            $data['recommendations'] = $contract->recommendations;
            $data['action_items'] = $contract->action_items;
            $data['fairness'] = $contract->fairness;

            // Handle Pro-only fields
            if ($contract->scan_type === 'pro') {
                $data['risks'] = $contract->risks;
                $data['key_clauses'] = $contract->key_clauses;
            } else {
                // Basic view - remove pro fields from risks/clauses if needed
                $data['risks'] = collect($contract->risks)->map(function ($risk) {
                    unset($risk['negotiation_tips'], $risk['what_to_ask']);
                    return $risk;
                });
                $data['key_clauses'] = collect($contract->key_clauses)->map(function ($clause) {
                    unset($clause['suggestion']);
                    return $clause;
                });
            }
        }

        return $this->success($data, 'Analysis retrieved.');
    }

    private function countPages($file)
    {
        if ($file->getMimeType() === 'application/pdf') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            if (preg_match_all("/\/Page\W/", $content, $matches)) {
                return count($matches[0]);
            }
        }
        return 1;
    }

    private function getFileType($file)
    {
        $mime = $file->getMimeType();
        if (str_contains($mime, 'pdf')) return 'pdf';
        if (str_contains($mime, 'image')) return 'image';
        return 'txt';
    }


    public function UserContract()
    {
        try {
            $contract = Contract::query()->where('user_id', Auth::id())->orderBy('id', 'desc')->get();
            return $this->success($contract, 'User contracts retrieved successfully.');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getMessage(), 500);
        }
    }

    /**
     * Instantly unlock a partial contract using 1 credit
     */
    public function unlockContract(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $contract = Contract::query()->where('user_id', $user->id)->findOrFail($id);

            if ($contract->is_full_unlocked == 1) {
                return $this->error('Already Unlocked', 'This contract is already fully unlocked.', 400);
            }

            $wallet = $user->wallets()->latest()->first();
            $currentBalance = $wallet ? $wallet->current_balance : 0;

            if ($currentBalance < 1) {
                return $this->error('Insufficient Credits', 'You do not have enough credits to unlock this report.', 402);
            }

            // Deduct 1 credit
            $wallet->decrement('current_balance', 1);
            $wallet->increment('total_used', 1);

            // Create transaction history
            $user->walletTransactions()->create([
                'type' => 'deduction',
                'amount' => 1,
                'description' => 'Unlocked Pro Contract Scan Report',
                'reference_id' => $contract->id,
            ]);

            // Instantly Unlock
            $contract->update([
                'is_full_unlocked' => 1,
                'access_level' => 'unlocked',
                'full_unlocked_at' => now(),
                'scan_type' => 'pro'
            ]);

            return $this->success([
                'contract_id' => $contract->id,
                'status' => 'unlocked'
            ], 'Report unlocked successfully! You can now view the full analysis.');
        } catch (Exception $e) {
            Log::error("Unlock Error: " . $e->getMessage());
            return $this->error('Something went wrong', $e->getMessage(), 500);
        }
    }
}
