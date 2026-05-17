<?php

namespace App\Jobs;

use App\Ai\Agents\ContractAnalyzer;
use App\Models\Contract;
use App\Models\ContractKeyClause;
use App\Models\ContractRiskItem;
use App\Models\RiskOverview;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Laravel\Ai\Files\StoredDocument;
use Laravel\Ai\Files\StoredImage;

class AnalyzeContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contract;
    protected $mode; // 'partial' or 'full'

    public function __construct(Contract $contract, $mode = 'full')
    {
        $this->contract = $contract;
        $this->mode = $mode;
    }

    public function handle(): void
    {
        try {
            $this->contract->update([
                'status' => $this->mode . '_processing',
                'scan_type' => $this->mode
            ]);

            $documentText = $this->contract->file_text ?? $this->contract->original_filename;
            $attachments = [];

            if ($this->contract->file) {
                if ($this->contract->file_type === 'image') {
                    $attachments[] = new StoredImage($this->contract->file, 'public');
                } elseif ($this->contract->file_type === 'pdf') {
                    $attachments[] = new StoredDocument($this->contract->file, 'public');
                }
            }

            // Parallel Processing for Large Documents
            if (strlen($documentText) > 150 && $this->mode === 'full' && $this->contract->file_type !== 'image') {
                $this->runParallelAnalysis($documentText);
                return;
            }

            $agent = new ContractAnalyzer();
            $agent->setMode($this->mode);

            $promptText = "Analyze this document content accurately: \n\n" . $documentText;

            if ($this->contract->file_type === 'image') {
                $promptText = "The user has uploaded a contract image. \n" .
                    "1. Read all the text from the attached image carefully.\n" .
                    "2. Perform a thorough contract analysis based on the extracted text.\n" .
                    "3. Provide the results in the required JSON format.";
            }

            $response = $agent->prompt($promptText, $attachments);
            $results = $this->parseAiResponse($response);

            if (!$results || !is_array($results)) {
                throw new Exception("AI Analysis failed: Could not parse results structure.");
            }

            $this->finalizeAnalysis($results);
        } catch (Exception $e) {
            Log::error("Contract Analysis Job Failed: " . $e->getMessage());
            $this->contract->update(['status' => 'failed']);
        }
    }

    protected function runParallelAnalysis($text)
    {
        $chunks = str_split($text, 10000);
        $totalChunks = count($chunks);
        $jobs = [];

        foreach ($chunks as $index => $chunk) {
            $jobs[] = new AnalyzeChunkJob($this->contract, $chunk, $index, $totalChunks);
        }

        $contractId = $this->contract->id;

        Bus::batch($jobs)
            ->then(function ($batch) use ($contractId, $totalChunks) {
                $mainJob = new AnalyzeContractJob(Contract::find($contractId), 'full');
                $mainJob->combineResults($contractId, $totalChunks);
            })
            ->catch(function ($batch, $e) use ($contractId) {
                Log::error("Batch failed for contract $contractId: " . $e->getMessage());
                Contract::find($contractId)->update(['status' => 'failed']);
            })
            ->dispatch();
    }

    public function combineResults($contractId, $totalChunks)
    {
        $this->contract = Contract::find($contractId);
        $allRisks = [];
        $allClauses = [];
        $finalResults = null;

        for ($i = 0; $i < $totalChunks; $i++) {
            $cacheKey = "contract_analysis_chunk_{$contractId}_{$i}";
            $response = Cache::get($cacheKey);
            $chunkResults = $this->parseAiResponse($response);
            Cache::forget($cacheKey);

            if ($i === 0) {
                $finalResults = $chunkResults;
                $allRisks = $chunkResults['risks'] ?? [];
                $allClauses = $chunkResults['key_clauses'] ?? [];
            } else {
                if (isset($chunkResults['risks'])) {
                    $allRisks = array_merge($allRisks, $chunkResults['risks']);
                }
                if (isset($chunkResults['key_clauses'])) {
                    foreach ($chunkResults['key_clauses'] as $key => $data) {
                        if (!empty($data['text']) && (empty($allClauses[$key]['text']))) {
                            $allClauses[$key] = $data;
                        }
                    }
                }
            }
        }

        if ($finalResults) {
            $finalResults['risks'] = $allRisks;
            $finalResults['key_clauses'] = $allClauses;

            $high = 0;
            $medium = 0;
            $low = 0;
            foreach ($allRisks as $r) {
                $severity = $r['severity'] ?? 'Low';
                if ($severity === 'High') $high++;
                elseif ($severity === 'Medium') $medium++;
                else $low++;
            }
            $finalResults['risk_overview'] = [
                'total_risks' => count($allRisks),
                'high_risk_count' => $high,
                'medium_risk_count' => $medium,
                'low_risk_count' => $low
            ];

            $this->finalizeAnalysis($finalResults);
        }
    }

    protected function finalizeAnalysis($results)
    {
        $this->saveResults($results);

        $this->contract->update([
            'status' => 'completed',
            'raw_ai_response' => $results
        ]);

        if (in_array($this->contract->billing_mode, ['subscription', 'bulk', 'credit', 'one_time', 'one_time_basic', 'one_time_pro'])) {
            $this->deductCredits();
        }
    }

    protected function parseAiResponse($response)
    {
        $results = null;
        if (is_array($response)) {
            $results = $response;
        } elseif (is_object($response)) {
            $results = $response->data ?? $response->content ?? (method_exists($response, 'json') ? $response->json() : (array)$response);
        } else {
            $results = json_decode($response, true);
        }

        if (isset($results['value']) && is_string($results['value'])) {
            $results = json_decode($results['value'], true);
        } elseif (isset($results['text']) && is_string($results['text'])) {
            $inner = json_decode($results['text'], true);
            $results = (isset($inner['value']) && is_string($inner['value'])) ? json_decode($inner['value'], true) : $inner;
        }

        if (is_string($results)) {
            $results = json_decode($results, true);
        }

        return $results;
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

            if (isset($results['risk_overview'])) {
                RiskOverview::create([
                    'contract_id' => $this->contract->id,
                    'total_risks' => $results['risk_overview']['total_risks'] ?? 0,
                    'high_risk_count' => $results['risk_overview']['high_risk_count'] ?? 0,
                    'medium_risk_count' => $results['risk_overview']['medium_risk_count'] ?? 0,
                    'low_risk_count' => $results['risk_overview']['low_risk_count'] ?? 0,
                ]);
            }

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

        WalletTransaction::create([
            'user_id' => $user->id,
            'contract_id' => $this->contract->id,
            'type' => 'debit',
            'source' => 'scan_usage',
            'credits' => $creditsToDeduct,
            'previous_balance' => $previousBalance,
            'current_balance' => $previousBalance - $creditsToDeduct,
            'note' => "Pages analyzed: " . $creditsToDeduct . " for " . $this->contract->original_filename
        ]);

        $user->updateBalance(-$creditsToDeduct, 'scan_usage');
    }
}
