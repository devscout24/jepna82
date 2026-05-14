<?php

namespace App\Jobs;

use App\Ai\Agents\ContractAnalyzer;
use App\Models\Contract;
use App\Models\ContractKeyClause;
use App\Models\ContractRiskItem;
use App\Models\RiskOverview;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contract;
    protected $mode; // 'partial' or 'full'

    /**
     * Create a new job instance.
     */
    public function __construct(Contract $contract, $mode = 'full')
    {
        $this->contract = $contract;
        $this->mode = $mode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->contract->update([
                'status' => $this->mode . '_processing',
                'scan_type' => $this->mode
            ]);

            $agent = new ContractAnalyzer();
            $agent->setMode($this->mode);

            // Trigger AI - In current version prompt() might return the response directly
            $documentText = $this->contract->file_text ?? $this->contract->original_filename;
            $response = $agent->prompt("Analyze this document content: \n\n" . $documentText);

            // Aggressive JSON Extraction Logic
            $results = null;
            if (is_array($response)) {
                $results = $response;
            } elseif (is_object($response)) {
                $results = $response->data ?? $response->content ?? (method_exists($response, 'json') ? $response->json() : (array)$response);
            } else {
                $results = json_decode($response, true);
            }

            // Layer 1 Check (Value inside result)
            if (isset($results['value']) && is_string($results['value'])) {
                $results = json_decode($results['value'], true);
            }
            // Layer 2 Check (Value inside Text)
            elseif (isset($results['text']) && is_string($results['text'])) {
                $inner = json_decode($results['text'], true);
                if (isset($inner['value']) && is_string($inner['value'])) {
                    // Deepest Level
                    $results = json_decode($inner['value'], true);
                } else {
                    $results = $inner;
                }
            }

            // Final fallback: If results is still a string, decode it again
            if (is_string($results)) {
                $results = json_decode($results, true);
            }

            if (!$results || !is_array($results)) {
                Log::error("Failed to parse AI response structure. Raw: " . json_encode($response));
                throw new Exception("AI Analysis failed: Could not parse results structure.");
            }

            // Save results to database
            $this->saveResults($results);

            $this->contract->update([
                'status' => 'completed',
                'raw_ai_response' => $results
            ]);

            // Deduct credits ONLY after everything is successfully saved
            if (in_array($this->contract->billing_mode, ['subscription', 'bulk'])) {
                $this->deductCredits();
            }

        } catch (Exception $e) {
            Log::error("Contract Analysis Job Failed: " . $e->getMessage());
            $this->contract->update(['status' => 'failed']);
        }
    }

    protected function saveResults($results)
    {
        if ($this->mode === 'partial') {
            $this->contract->update([
                'short_summary' => $results['summary']['short_summary'] ?? '',
                'risk_score' => $results['risk_score']['score'] ?? 0,
                'risk_level' => $results['risk_score']['level'] ?? 'Low',
                'preview_risks' => $results['risks'] ?? [],
                'status' => 'preview_ready',
            ]);
        } else {
            // Full Scan Save
            $this->contract->update([
                'contract_title' => $results['contract_details']['contract_title'] ?? $this->contract->original_filename,
                'document_type' => $results['contract_details']['document_type'] ?? '',
                'short_summary' => $results['summary']['short_summary'] ?? '',
                'summary' => $results['summary'] ?? null,
                'risk_score' => $results['risk_score']['score'] ?? 0,
                'risk_level' => $results['risk_score']['level'] ?? 'Low',
                'risk_overview' => $results['risk_overview'] ?? null,
                'risks' => $results['risks'] ?? [],
                'recommendations' => $results['recommendations'] ?? [],
                'action_items' => $results['action_items'] ?? [],
                'contract_details' => $results['contract_details'] ?? null,
                'key_clauses' => $results['key_clauses'] ?? null,
                'fairness' => $results['fairness'] ?? null,
                'signature_details' => $results['signature_details'] ?? null,
                'status' => 'completed',
            ]);

            // Normalized Risks
            if (isset($results['risks'])) {
                foreach ($results['risks'] as $index => $risk) {
                    ContractRiskItem::create([
                        'contract_id' => $this->contract->id,
                        'title' => $risk['title'] ?? '',
                        'severity' => $risk['severity'] ?? 'Low',
                        'category' => $risk['category'] ?? 'other',
                        'explanation' => $risk['explanation'] ?? '',
                        'impact' => $risk['impact'] ?? '',
                        'suggestion' => $risk['suggestion'] ?? '',
                        'sort_order' => $index
                    ]);
                }
            }

            // Risk Overview
            if (isset($results['risk_overview'])) {
                RiskOverview::create([
                    'contract_id' => $this->contract->id,
                    'total_risks' => $results['risk_overview']['total_risks'] ?? 0,
                    'high_risk_count' => $results['risk_overview']['high_risk_count'] ?? 0,
                    'medium_risk_count' => $results['risk_overview']['medium_risk_count'] ?? 0,
                    'low_risk_count' => $results['risk_overview']['low_risk_count'] ?? 0,
                ]);
            }

            // Key Clauses
            if (isset($results['key_clauses'])) {
                foreach ($results['key_clauses'] as $type => $clause) {
                    ContractKeyClause::create([
                        'contract_id' => $this->contract->id,
                        'clause_type' => $type,
                        'clause_text' => $clause['text'] ?? '',
                        'explanation' => $clause['explanation'] ?? '',
                    ]);
                }
            }
        }
    }

    protected function deductCredits()
    {
        $user = $this->contract->user;
        $wallet = $user->wallets()->latest()->first();
        $previousBalance = $wallet ? $wallet->current_balance : 0;
        $creditsToDeduct = $this->contract->page_count;

        // Transaction record
        WalletTransaction::create([
            'user_id' => $user->id,
            'contract_id' => $this->contract->id,
            'type' => 'debit',
            'source' => 'scan_usage',
            'credits' => $creditsToDeduct,
            'previous_balance' => $previousBalance,
            'current_balance' => $previousBalance - $creditsToDeduct,
            'note' => "Analysis of: " . $this->contract->original_filename
        ]);

        // Update main wallet balance
        $user->updateBalance(-$creditsToDeduct, 'scan_usage');
    }
}
