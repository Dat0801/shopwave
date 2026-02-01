<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_payment_method_id',
        'stripe_card_brand',
        'stripe_card_last4',
        'stripe_card_exp_month',
        'stripe_card_exp_year',
        'type',
        'last4',
        'holder_name',
        'expiry_date',
        'is_default',
        'label',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
