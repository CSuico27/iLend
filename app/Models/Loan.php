<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
    protected static function booted()
    {
        static::created(function ($loan) {
            $loan->generateLedgers();
        });
    }

    public function generateLedgers()
    {
        $paymentFrequency = $this->payment_frequency;
        $loanTerm = $this->loan_term;
        
        $count = match ($paymentFrequency) {
            'daily' => $loanTerm * 30,
            'weekly' => round($loanTerm * 4.33),
            'biweekly' => round($loanTerm * 2.17),
            'monthly' => $loanTerm,
            default => 0,
        };

        $startDate = Carbon::parse($this->start_date);
        $ledgers = [];
        
        for ($i = 0; $i < $count; $i++) {
            $ledgers[] = [
                'loan_id' => $this->id, 
                'status' => 'Pending',
                'due_date' => $startDate->copy()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $startDate = match ($paymentFrequency) {
                'daily' => $startDate->addDay(),
                'weekly' => $startDate->addWeek(),
                'biweekly' => $startDate->addWeeks(2),
                'monthly' => $startDate->addMonth(),
                default => $startDate,
            };
        }

        DB::table('ledgers')->insert($ledgers);
    }
}
