<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'loan_type',
        'loan_amount',
        'interest_rate',
        'loan_term',
        'payment_frequency',
        'interest_amount',
        'total_payment',
        'payment_per_term',
        'status',
        'start_date',
        'end_date',
    ];
    public function ledgers()
    {
        return $this->hasOne(Ledger::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
