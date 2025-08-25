<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'action',
        'model',
        'model_id',
        'changes',
        'user_id',
        'amount',
        'status',
        'loan_id',
        'ledger_id'
    ];
    protected $casts = [
        'changes' => 'array',
        'amount' => 'decimal:2',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}
