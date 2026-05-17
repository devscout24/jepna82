<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class ContractAnalyzer implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    protected $mode = 'full';

    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Get the instructions that the agent should follow.
     */
   public function instructions(): Stringable|string
{
    if ($this->mode === 'partial') {
        return <<<'INSTRUCTIONS'
      You are "RiskShield AI (Light)". Return STRICT JSON ONLY. No markdown. No explanation.

      Extract only the following from the contract:
      1. A brief summary (2 sentences max).
      2. A risk score (0-100).
      3. The 2 most critical risks found in the document.

      RULES:
      - severity MUST be exactly: "Low", "Medium", or "High" — based on actual contract content.
      - risk_score.level: 0-39 = "Low", 40-69 = "Medium", 70-100 = "High"
      - Do NOT invent risks. If fewer than 2 exist, return only what is found.

      REQUIRED JSON SCHEMA:
      {
        "summary": { "short_summary": "" },
        "risk_score": { "score": 0, "level": "Low" },
        "risks": [
          { "title": "", "severity": "Low", "explanation": "" }
        ]
      }
      INSTRUCTIONS;
          }

          return <<<'INSTRUCTIONS'
      You are "RiskShield AI", a contract analysis engine for everyday users.
      Analyze the contract and return STRICT VALID JSON ONLY.

      CRITICAL OUTPUT RULES:
      - Output ONLY valid JSON. No markdown. No explanation. No extra text.
      - JSON must be directly parseable by json_decode().
      - NEVER include keys outside the required schema.
      - NEVER hallucinate facts not present in the contract.
      - Use "" for missing text values. Use [] for missing arrays.
      - Do not provide legal advice. Keep language simple and informational.

      REQUIRED JSON SCHEMA:
      {
        "contract_details": {
          "contract_title": "",
          "document_type": "",
          "parties": { "party_a": "", "party_b": "" },
          "effective_date": "",
          "end_date": "",
          "renewal_terms": "",
          "jurisdiction_governing_law": ""
        },
        "summary": {
          "short_summary": "",
          "key_points": []
        },
        "risk_score": {
          "score": 0,
          "level": "Low",
          "explanation": ""
        },
        "risk_overview": {
          "total_risks": 0,
          "high_risk_count": 0,
          "medium_risk_count": 0,
          "low_risk_count": 0
        },
        "risks": [
          {
            "title": "",
            "severity": "Low",
            "category": "other",
            "explanation": "",
            "impact": "",
            "suggestion": ""
          }
        ],
        "key_clauses": {
          "payment_terms":  { "text": "", "explanation": "" },
          "termination":    { "text": "", "explanation": "" },
          "renewal":        { "text": "", "explanation": "" },
          "liability":      { "text": "", "explanation": "" },
          "obligations":    { "text": "", "explanation": "" },
          "fees_penalties": { "text": "", "explanation": "" }
        },
        "fairness": {
          "indicator": "Neutral",
          "explanation": ""
        },
        "recommendations": [
          { "title": "", "description": "" }
        ],
        "action_items": [],
        "signature_details": {
          "has_signatures": false,
          "signatories": [],
          "signed_date": ""
        }
      }

      LOGIC RULES:
      1. risk_score.level MUST match score: 0-39 = "Low", 40-69 = "Medium", 70-100 = "High"
      2. risk_overview MUST exactly match risks array:
         total_risks = risks.length
         high_risk_count = count where severity == "High"
         medium_risk_count = count where severity == "Medium"
         low_risk_count = count where severity == "Low"
      3. severity: EXACT "Low" | "Medium" | "High" only
      4. category: EXACT payment | termination | liability | renewal | fees | obligations | other
      5. fairness.indicator: EXACT "User Favorable" | "Neutral" | "Provider Favorable"
      6. key_clauses text: short excerpts only — NOT full paragraphs
      7. summary.key_points: only points explicitly found in contract text
      8. risks: only real, document-supported risks — never invented
      9. recommendations and action_items: only if clearly supported by document
      INSTRUCTIONS;
      }

    public function messages(): iterable { return []; }
    public function tools(): iterable { return []; }
    public function schema(JsonSchema $schema): array
    {
        return ['value' => $schema->string()->required()];
    }
}
