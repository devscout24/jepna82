<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'summary' => 'array',
        'risks' => 'array',
        'risk_overview' => 'array',
        'recommendations' => 'array',
        'action_items' => 'array',
        'contract_details' => 'array',
        'key_clauses' => 'array',
        'fairness' => 'array',
        'signature_details' => 'array',
        'raw_ai_response' => 'array',
        'preview_risks' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function riskItems()
    {
        return $this->hasMany(ContractRiskItem::class);
    }

    public function riskOverview()
    {
        return $this->hasOne(RiskOverview::class);
    }

    public function keyClauses()
    {
        return $this->hasMany(ContractKeyClause::class);
    }
}
