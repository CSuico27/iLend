<?php

namespace App\Livewire\Client;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class PortalPage extends Component
{
    use WireUiActions;
    public $activeTab = 'dashboard';

    public $activeLoanDetails;
    public $hasActiveLoan = false;
    public $activeLoanAmount = 0;
    public $totalPaid = 0;
    public $remainingBalance = 0;

    public $userLoans;
    public $selectedLoan = null;
    public bool $showLoanApplicationModal = false;
    public $user_id;
    public $user_name;
    public $loan_type;
    public $loan_amount;
    public $interest_rate;
    public $loan_term;
    public $payment_frequency;
    public $start_date;
    public $end_date;
    public $interest_amount;
    public $total_payment;
    public $payment_per_term;

    protected $queryString = ['activeTab'];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount(){
        if (Auth::check()) {
            
            $user = Auth::user();
            $userProfile = Auth::user()->info;
            $this->user_id = Auth::user()->id;
            $this->user_name = Auth::user()->name;

            if ($userProfile && $userProfile->status == 'Pending') {
                return redirect()->route('user.home')->with('portal_error', 'Your membership application is currently under review. Please wait for approval.');
            }

            $this->userLoans = $user->loans()->with(['ledgers.payment'])->get();

            $this->activeLoanDetails = $user->loans()
                ->with(['ledgers.payment'])
                ->where('is_finished', false)
                ->where('status', 'Approved')
                ->latest()
                ->first();

            if ($this->activeLoanDetails) {
                $this->hasActiveLoan = true;
            
                $this->activeLoanAmount = $this->activeLoanDetails->loan_amount;
            
                $totalPayable = $this->activeLoanAmount * (1 + ($this->activeLoanDetails->interest_rate / 100));
            
                $this->totalPaid = $this->activeLoanDetails->ledgers->sum(function ($ledger) {
                    return $ledger->payment?->amount ?? 0;
                });
            
                $this->remainingBalance = round($totalPayable - $this->totalPaid, 2);
            } else {
                $this->hasActiveLoan = false;
                $this->activeLoanAmount = 0;
                $this->totalPaid = 0;
                $this->remainingBalance = 0;
            }
            
        }
    }

    public function loadLoanDetails($loanId)
    {
        $this->selectedLoan = Auth::user()->loans()->with('ledgers.payment')->find($loanId);
    }

    public function clearSelectedLoan(){
        $this->selectedLoan = null;
    }

    public function getCanApplyProperty()
    {
        if (!Auth::check()) {
            return false;
        }

        $userLoans = $this->userLoans ?? Auth::user()->loans()->get();

        if ($userLoans->isEmpty()) {
            return true;
        }

        return $userLoans->every(fn($loan) => $loan->is_finished && $loan->status !== 'Pending');
    }

    public function openLoanApplicationModal()
    {
        if (! $this->canApply) {
            $this->notification()->error(
                'Loan Application Blocked',
                'You have a pending or active loan. Please settle it before applying again.'
            );
            return;
        }
        $this->showLoanApplicationModal = true;
    }

    public function calculateLoan()
    {
            $loanAmount = (float) preg_replace('/[^\d.]/', '', $this->loan_amount ?? '');
            $interestRate = (float) ($this->interest_rate ?? 0);
            $loanTerm = (int) ($this->loan_term ?? 0);
            $paymentFrequency = $this->payment_frequency ?? 'monthly';

            $interestAmount = $loanAmount * ($interestRate / 100);
            $this->interest_amount = $interestAmount;

            $totalPayment = $loanAmount + $interestAmount;
            $this->total_payment = $totalPayment;

            $count = 0;
            
            if ($loanTerm > 0 && $paymentFrequency) {
                $count = match ($paymentFrequency) {
                    'daily' => $loanTerm * 30,     
                    'weekly' => $loanTerm * 4.33,  
                    'biweekly' => $loanTerm * 2.17,
                    'monthly' => $loanTerm,   
                    default => 0, 
                };
            }

            $paymentPerTerm = $count > 0 ? $totalPayment / $count : 0;
            $this->payment_per_term = $paymentPerTerm;

            $today = Carbon::now();
            $start = match ($paymentFrequency) {
                'daily' => $today->copy()->addDay(),
                'weekly' => $today->copy()->addWeek(),
                'biweekly' => $today->copy()->addWeeks(2),
                'monthly' => $today->copy()->addMonth(),
                default => $today,
            };

            $end = match ($paymentFrequency) {
                'daily' => $start->copy()->addDays($loanTerm * 30),
                'weekly' => $start->copy()->addWeeks((int) round($loanTerm * 4.34)),
                'biweekly' => $start->copy()->addWeeks(($loanTerm * 2.17) + 2),
                'monthly' => $start->copy()->addMonths($loanTerm - 1), 
                default => $start,
            };

            $this->start_date = $start;
            $this->end_date = $end;
    }
    public function updated($property)
    {
        if (in_array($property, ['loan_amount', 'interest_rate', 'loan_term', 'payment_frequency', 'start_date', 'end_date'])) {
            $this->calculateLoan();
        }
    }
    public function submitLoanApplication()
    {

        try {
            $this->validate([
                'user_id' => 'required|exists:users,id',
                'loan_amount' => 'required|numeric|max:100000',
                'loan_type' => 'required|string|max:255',
                'interest_rate' => 'required|numeric|min:1|max:100',
                'loan_term' => 'required|integer|min:1|max:60',
                'payment_frequency' => 'required|string|in:daily,weekly,bi-weekly,monthly',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'interest_amount' => 'nullable|numeric|min:1',
                'total_payment' => 'nullable|numeric',
                'payment_per_term' => 'nullable|numeric',
            ]);

            loan::create([
                'user_id' => $this->user_id,
                'loan_type' => $this->loan_type,
                'loan_amount' => (float) preg_replace('/[^\d.]/', '', $this->loan_amount ?? ''),
                'interest_rate' => (float) ($this->interest_rate ?? 0),
                'loan_term' => (int) ($this->loan_term ?? 0),
                'payment_frequency' => $this->payment_frequency,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'interest_amount' => (float) ($this->interest_amount ?? 0),
                'total_payment' => (float) ($this->total_payment ?? 0),
                'payment_per_term' => (float) ($this->payment_per_term ?? 0),
            ]);
            $this->notification()->success(
                'Success!',
                'Loan application submitted successfully. We’ll notify you once it’s approved.'
            );
            $this->showLoanApplicationModal = false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notification()->error(
                'Error!',
                'Loan application is invalid.'
            );
            return;
        }
    }

    public function render()
    {
        return view('livewire.client.portal-page');
    }
}
