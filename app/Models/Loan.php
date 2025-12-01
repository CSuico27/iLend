<?php

namespace App\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        'remaining_balance',
        'payment_per_term',
        'status',
        'soa_path',
        'approved_at',
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
            $loan->updateQuietly([
                'remaining_balance' => $loan->total_payment,
            ]);

            $loan->generateLedgers();
            $loan->generateLedgerPdf();
        });

        //when updating something in the loan
        static::updated(function ($loan) {
            if ($loan->wasChanged(['loan_amount', 'interest_rate', 'loan_term', 'payment_frequency', 'interest_amount', 'total_payment', 'payment_per_term', 'start_date', 'end_date'])) {
                $loan->recomputeLoan();
                $loan->ledgers()->delete(); 
                $loan->generateLedgers();   
                $loan->generateLedgerPdf();
            }
        });

        static::created(function ($loan) {
            $loan->generateSOA();
        });
    }

    public function generateLedgerPdf()
    {
        $ledgers = $this->ledgers()->orderBy('id')->get();

        if ($ledgers->isEmpty()) {
            return;
        }

        $pdf = Pdf::loadView('pdf.show-ledger', [
            'loan' => $this,
            'ledgersCollection' => $ledgers,
        ]);

        $userName = str_replace(' ', '_', strtolower($this->user->name));
        $timestamp = now()->format('Ymd');
        $loanID = $this->id;

        $filename = "{$loanID}_{$userName}_ledger_{$timestamp}.pdf";
        $path = "ledgers/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        foreach ($ledgers as $ledger) {
            $ledger->ledger_path = $path;
            $ledger->save();
        }
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

        $interestAmount = $principal * $rate;
        $totalPayment   = $principal + $interestAmount;

        $paymentCount = match ($this->payment_frequency) {
            'daily'    => $term * 30,
            'weekly'   => round($term * 4.33),
            'biweekly' => round($term * 2.17),
            'monthly'  => $term,
            default    => 0,
        };

        $paymentPerTerm = $paymentCount > 0 ? $totalPayment / $paymentCount : 0;

        $this->updateQuietly([
            'interest_amount'   => $interestAmount,
            'total_payment'     => $totalPayment,
            'remaining_balance' => $this->remaining_balance ?: $totalPayment, 
            'payment_per_term'  => $paymentPerTerm,
        ]);
    }
   
    public function generateSOA()
    {
        $totalPaid = $this->ledgers->sum(fn ($ledger) =>
            $ledger->payment?->status === 'Approved'
                ? ($ledger->payment->amount ?? 0)
                : 0
        );

        $remainingBalance = $this->remaining_balance
            ?? ($this->total_payment - $totalPaid);

        $pdf = Pdf::loadView('pdf.view-soa', [
            'loan' => $this,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
        ]);

        $userName = str_replace(' ', '_', strtolower($this->user->name));
        $timestamp = now()->format('Ymd_His'); // Added time for uniqueness
        $loanID = $this->id;

        $filename = "SOA_{$loanID}_{$userName}_{$timestamp}.pdf";
        $path = "soa/{$filename}";

        if ($this->soa_path && Storage::disk('public')->exists($this->soa_path)) {
            Storage::disk('public')->delete($this->soa_path);
        }

        Storage::disk('public')->put($path, $pdf->output());
        
        $this->updateQuietly(['soa_path' => $path]); 
        
        return $path;
    }

    public function getLatestSOA()
    {
        return $this->generateSOA();
    }
}
