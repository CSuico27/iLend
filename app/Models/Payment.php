<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'ledger_id',
        'payment_method',
        'amount',
        'proof_of_billing',
        'status',
        'date_received',
        'receipt'
    ];
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function getUserAttribute()
    {
        return $this->ledger?->loan?->user;
    }

}
