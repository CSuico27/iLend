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
        'is_finished',
    ];
    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        //when loan is created
        static::created(function ($loan) {
            $loan->generateLedgers();
        });

        //when updating something in the loan
        static::updated(function ($loan) {
            if ($loan->wasChanged(['loan_amount', 'interest_rate', 'loan_term', 'payment_frequency', 'interest_amount', 'total_payment', 'payment_per_term', 'start_date', 'end_date'])) {
                $loan->recomputeLoan();
                $loan->ledgers()->delete(); 
                $loan->generateLedgers();   
            }
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

    public function recomputeLoan()
    {
        $principal = $this->loan_amount;
        $rate = $this->interest_rate / 100;
        $term = $this->loan_term;

        $interestAmount = $principal * $rate * $term;
        $totalPayment = $principal + $interestAmount;

        $paymentCount = match ($this->payment_frequency) {
            'daily' => $term * 30,
            'weekly' => round($term * 4.33),
            'biweekly' => round($term * 2.17),
            'monthly' => $term,
            default => 0,
        };

        $paymentPerTerm = $paymentCount > 0 ? $totalPayment / $paymentCount : 0;

        $this->updateQuietly([
            'interest_amount'   => $interestAmount,
            'total_payment'     => $totalPayment,
            'payment_per_term'  => $paymentPerTerm,
        ]);
    }

}
