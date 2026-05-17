<?php

namespace App\Jobs;

use App\Ai\Agents\ContractAnalyzer;
use App\Models\Contract;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contract;
    protected $chunk;
    protected $chunkIndex;
    protected $totalChunks;

    public function __construct(Contract $contract, $chunk, $chunkIndex, $totalChunks)
    {
        $this->contract = $contract;
        $this->chunk = $chunk;
        $this->chunkIndex = $chunkIndex;
        $this->totalChunks = $totalChunks;
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $agent = new ContractAnalyzer();
        $agent->setMode('full');

        if ($this->chunkIndex === 0) {
            $prompt = "Part 1 of a large document. Analyze and provide full JSON structure. Focus on risks/clauses here: \n\n" . $this->chunk;
        } else {
            $prompt = "Part " . ($this->chunkIndex + 1) . "/" . $this->totalChunks . " of the same document. Extract ONLY NEW 'risks' and 'key_clauses' found here. Return JSON for these keys only: \n\n" . $this->chunk;
        }

        $response = $agent->prompt($prompt);

        // Use a static-like way to store results temporarily in the contract model or a cache
        // For simplicity, we can append to a JSON file or use a temporary table/cache.
        // Better: Store in a temporary database column or a cache key
        $cacheKey = "contract_analysis_chunk_{$this->contract->id}_{$this->chunkIndex}";
        \Illuminate\Support\Facades\Cache::put($cacheKey, $response, 3600);
    }
}
