<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $button_text
 * @property array<array-key, mixed>|null $icon
 * @property array<array-key, mixed>|null $icon_title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereIconTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereUpdatedAt($value)
 */
	class Banner extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $image
 * @property string|null $description
 * @property int $status 1 = Published, 0 = Unpublished
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUserId($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $contract_title
 * @property string|null $document_type
 * @property string|null $original_filename
 * @property string|null $file
 * @property string|null $file_text
 * @property int|null $file_size_kb
 * @property int $page_count
 * @property string|null $file_type
 * @property string|null $scan_type
 * @property string|null $billing_mode
 * @property numeric $amount_charged
 * @property int $credit_used
 * @property numeric|null $per_page_rate
 * @property int $extra_pages
 * @property numeric $extra_page_charge
 * @property int|null $payment_reference_id
 * @property int|null $subscription_id
 * @property int|null $credit_pack_id
 * @property string|null $preview_generated_at
 * @property string|null $short_summary
 * @property int|null $fairness_score
 * @property int|null $risk_score
 * @property string|null $risk_level
 * @property int $risk_count
 * @property array<array-key, mixed>|null $preview_risks
 * @property string|null $full_unlocked_at
 * @property array<array-key, mixed>|null $summary
 * @property array<array-key, mixed>|null $risks
 * @property array<array-key, mixed>|null $risk_overview
 * @property array<array-key, mixed>|null $recommendations
 * @property array<array-key, mixed>|null $action_items
 * @property array<array-key, mixed>|null $contract_details
 * @property array<array-key, mixed>|null $key_clauses
 * @property array<array-key, mixed>|null $fairness
 * @property array<array-key, mixed>|null $signature_details
 * @property array<array-key, mixed>|null $raw_ai_response
 * @property string $access_level
 * @property int $is_full_unlocked
 * @property string|null $ai_model_used
 * @property int $token_count_preview
 * @property int $token_count_full
 * @property string|null $file_deleted_at
 * @property int $is_file_deleted
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractKeyClause> $keyClauses
 * @property-read int|null $key_clauses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractRiskItem> $riskItems
 * @property-read int|null $risk_items_count
 * @property-read \App\Models\RiskOverview|null $riskOverview
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereAccessLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereActionItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereAiModelUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereAmountCharged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereBillingMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreditPackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreditUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereExtraPageCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereExtraPages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFairness($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFairnessScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFileDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFileSizeKb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFileText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereFullUnlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereIsFileDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereIsFullUnlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereKeyClauses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePaymentReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePerPageRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePreviewGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePreviewRisks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRawAiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRecommendations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRiskCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRiskOverview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRiskScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRisks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereScanType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereShortSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereSignatureDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereTokenCountFull($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereTokenCountPreview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereUserId($value)
 */
	class Contract extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property string $clause_type
 * @property string|null $clause_text
 * @property string|null $explanation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereClauseText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereClauseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractKeyClause whereUpdatedAt($value)
 */
	class ContractKeyClause extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property string|null $title
 * @property string|null $severity
 * @property string|null $category
 * @property string|null $explanation
 * @property string|null $impact
 * @property string|null $suggestion
 * @property int $is_preview
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereIsPreview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereSuggestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractRiskItem whereUpdatedAt($value)
 */
	class ContractRiskItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $package_id
 * @property int $credits_purchased
 * @property int $credits_used
 * @property int $credits_remaining
 * @property string|null $expires_at null = never expires
 * @property string $status
 * @property string|null $stripe_payment_intent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereCreditsPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereCreditsRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereCreditsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditPackPurchase whereUserId($value)
 */
	class CreditPackPurchase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereUpdatedAt($value)
 */
	class Feature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $file
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property numeric|null $price
 * @property string|null $title
 * @property string|null $title_text
 * @property string|null $duration
 * @property string $stripe_price_id
 * @property string|null $plan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereStripePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereTitleText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonthlySubscription whereUpdatedAt($value)
 */
	class MonthlySubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $sub_title
 * @property string|null $description
 * @property numeric $price
 * @property numeric $discount_percentage
 * @property numeric $final_price
 * @property string $currency
 * @property int $page_limit
 * @property numeric|null $extra_page_rate charge per extra page beyond page_limit
 * @property array<array-key, mixed>|null $features array of feature strings for pricing card
 * @property string $package_type
 * @property string|null $billing_cycle
 * @property string|null $stripe_product_id
 * @property string|null $stripe_price_id
 * @property string|null $stripe_plan_id
 * @property int $trial_days
 * @property bool $is_popular
 * @property string|null $badge_text
 * @property int $status 1=active, 0=inactive
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereBadgeText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereExtraPageRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereIsPopular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription wherePackageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription wherePageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereStripePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereStripePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereStripeProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereSubTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereTrialDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PackageAndSubscription whereUpdatedAt($value)
 */
	class PackageAndSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $package_id
 * @property int|null $subscription_id
 * @property int|null $credit_pack_id
 * @property numeric $amount
 * @property string $currency
 * @property int $credits_purchased
 * @property string $payment_method
 * @property string|null $stripe_payment_intent_id
 * @property string|null $stripe_charge_id
 * @property string|null $stripe_customer_id
 * @property string|null $stripe_invoice_id
 * @property string|null $stripe_subscription_id
 * @property string|null $transaction_id
 * @property string $status
 * @property string $payment_type
 * @property numeric $refund_amount
 * @property string|null $refunded_at
 * @property string|null $refund_reason
 * @property int $extra_pages_charged
 * @property numeric $extra_page_total
 * @property string|null $raw_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereCreditPackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereCreditsPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereExtraPageTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereExtraPagesCharged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereRawResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereRefundReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereStripeChargeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereStripeCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereStripeInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereStripeSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payments whereUserId($value)
 */
	class Payments extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $code
 * @property string|null $short_description
 * @property string|null $long_description
 * @property string|null $highlight_title
 * @property numeric $regular_price
 * @property numeric $selling_price
 * @property string|null $image
 * @property int $stock
 * @property int $status 1=Published, 0=Unpublished
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductMultiImage> $productMultiImages
 * @property-read int|null $product_multi_images_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereHighlightTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereLongDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereRegularPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUserId($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductMultiImage whereUpdatedAt($value)
 */
	class ProductMultiImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $total_risks
 * @property int $high_risk_count
 * @property int $medium_risk_count
 * @property int $low_risk_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereHighRiskCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereLowRiskCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereMediumRiskCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereTotalRisks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskOverview whereUpdatedAt($value)
 */
	class RiskOverview extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property numeric|null $price
 * @property array<array-key, mixed>|null $items
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scan whereUpdatedAt($value)
 */
	class Scan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $facebook_link
 * @property string|null $facebook_icon
 * @property string|null $instagram_link
 * @property string|null $instagram_icon
 * @property string|null $twitter_link
 * @property string|null $twitter_icon
 * @property string|null $tiktok_link
 * @property string|null $tiktok_icon
 * @property string|null $whatsapp_link
 * @property string|null $whatsapp_icon
 * @property string|null $linkedin_link
 * @property string|null $linkedin_icon
 * @property string|null $telegram_link
 * @property string|null $telegram_icon
 * @property string|null $youtube_link
 * @property string|null $youtube_icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereFacebookIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereFacebookLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereInstagramIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereInstagramLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereLinkedinIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereLinkedinLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereTelegramIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereTelegramLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereTiktokIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereTiktokLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereTwitterIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereTwitterLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereWhatsappIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereWhatsappLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereYoutubeIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSetting whereYoutubeLink($value)
 */
	class SocialSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUpdatedAt($value)
 */
	class Stat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $title_text
 * @property numeric|null $price
 * @property string|null $price_text
 * @property array<array-key, mixed>|null $items
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan wherePriceText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan whereTitleText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscribePlan whereUpdatedAt($value)
 */
	class SubscribePlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $logo
 * @property string|null $mini_logo
 * @property string|null $favicon
 * @property string|null $system_title
 * @property string|null $company_name
 * @property string|null $tag_line
 * @property string|null $phone_number
 * @property string|null $whatsapp_number
 * @property string|null $email
 * @property string|null $copyright_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $stripe_webhook_secret_subscription
 * @property string|null $stripe_webhook_secret_bulk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereCopyrightText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereMiniLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereStripeWebhookSecretBulk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereStripeWebhookSecretSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereSystemTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereTagLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereWhatsappNumber($value)
 */
	class SystemSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property string|null $username
 * @property string $email
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property string|null $avatar
 * @property string|null $profile_image
 * @property string|null $otp
 * @property string|null $otp_expires_at
 * @property string|null $otp_verified_at
 * @property string|null $password_reset_token
 * @property string|null $password_reset_token_expires_at
 * @property int $role 1 = Admin, 0 = User
 * @property int $status 1 = Active, 0 = Inactive
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserWalet> $wallets
 * @property-read int|null $wallets_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User hasExpiredGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOtpExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOtpVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordResetToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordResetTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $package_id
 * @property string|null $stripe_subscription_id
 * @property string|null $stripe_customer_id
 * @property string|null $stripe_invoice_id
 * @property numeric $amount
 * @property string $currency
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $next_billing_date
 * @property string|null $trial_ends_at
 * @property int $credits_allocated
 * @property int $credits_used
 * @property int $credits_remaining
 * @property string|null $credits_reset_at
 * @property string $payment_status
 * @property string $subscription_status
 * @property int $cancel_at_period_end 1 = will cancel at end of current period
 * @property string|null $cancelled_at
 * @property string|null $cancellation_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PackageAndSubscription $package
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCancelAtPeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCreditsAllocated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCreditsRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCreditsResetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCreditsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereNextBillingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereStripeCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereStripeInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereStripeSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereSubscriptionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereUserId($value)
 */
	class UserSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $package_id
 * @property int|null $subscription_id
 * @property int|null $credit_pack_id
 * @property int|null $payment_id
 * @property string $type
 * @property string $source
 * @property int $credits always positive; type determines direction
 * @property int $previous_balance
 * @property int $current_balance
 * @property int $refundable_credit
 * @property string $refund_status
 * @property string|null $stripe_payment_id
 * @property string|null $note
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereCreditPackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereCurrentBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet wherePreviousBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereRefundableCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereStripePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserWalet whereUserId($value)
 */
	class UserWalet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $package_id
 * @property int|null $subscription_id
 * @property int|null $credit_pack_id
 * @property int|null $contract_id
 * @property int|null $payment_id
 * @property string $type
 * @property string $source
 * @property int $credits
 * @property int $previous_balance
 * @property int $current_balance
 * @property int $refundable_credit
 * @property string $refund_status
 * @property string|null $stripe_payment_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCreditPackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCurrentBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction wherePreviousBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereRefundableCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereStripePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUserId($value)
 */
	class WalletTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereUpdatedAt($value)
 */
	class Work extends \Eloquent {}
}

