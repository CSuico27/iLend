<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $fillable = [
        'loan_id',
        'amount_paid',
        'remaining_balance',
        'status'
    ];
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
