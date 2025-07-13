<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $fillable = [
        'loan_id',
        'due_date',
        'is_due',
        'status'
    ];
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
