<?php

namespace App\Livewire\Client;

use App\Models\CreditScore;
use App\Models\Ledger;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\WireUiActions;

class PortalPage extends Component
{
    use WireUiActions;
    use WithFilePond;
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

    public $creditScore, $creditTier, $creditRemarks, $creditLastUpdated;
    public $payment_method;
    public $amount;
    public $proof_of_billing;
    public bool $showPaymentModal = false;

    protected $queryString = ['activeTab'];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        session(['activeTab' => $tab]);
    }

    public function activateCreditScoreTab()
    {
        $this->activeTab = 'cs';
        session(['activeTab' => 'cs']);
        $this->dispatch('reload');
    }

    public function mount(){

        $this->activeTab = session('activeTab', 'dashboard');
        if (Auth::check()) {
            
            $user = Auth::user();
            $userProfile = Auth::user()->info;
            $this->user_id = Auth::user()->id;
            $this->user_name = Auth::user()->name;

            if ($userProfile && $userProfile->status == 'Pending') {
                return redirect()->route('user.home')->with('portal_error', 'Your membership application is currently under review. Please wait for approval.');
            }

            // Get user's credit score
            $creditScore = CreditScore::where('user_id', $user->id)->first();
            $this->creditScore = $creditScore?->score ?? 0;
            $this->creditTier = $creditScore?->tier ?? 'N/A';
            $this->creditRemarks = $creditScore?->remarks ?? 'No data';
            $this->creditLastUpdated = $creditScore?->updated_at?->format('M d, Y') ?? 'Never';

            $this->userLoans = $user->loans()->with(['ledgers.payment'])->get();
            $this->amount = $user->loans()->first()?->payment_per_term ?? 0;

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

    public function openPaymentModal($ledgerId)
    {
        $ledger = Ledger::with('loan.ledgers')->findOrFail($ledgerId);

        if ($ledger->status === 'Paid' || optional($ledger->payment)?->status === 'Pending') {
            $this->notification()->error(
                'Payment Error',
                'This ledger has already been paid or is pending approval.'
            );
            return;
        }

        $hasUnpaidEarlierLedger = $ledger->loan
            ->ledgers()
            ->where('due_date', '<', $ledger->due_date)
            ->where('status', '!=', 'Paid')
            ->exists();

        if ($hasUnpaidEarlierLedger) {
            $this->notification()->error(
                'Payment Error',
                'You must pay earlier dues before this one.'
            );
            return;
        }

        $this->showPaymentModal = true;
    }
    public function save($ledgerId)
    {
        $ledger = Ledger::findOrFail($ledgerId);
        // Validate inputs
        $this->validate([
            'payment_method' => 'required|in:GCash,Bank Transfer',
            'amount' => 'required|numeric|min:1',
            'proof_of_billing' => 'required|file|image|max:2048',
        ]);

        // Store uploaded file
        $filePath = $this->proof_of_billing->store('proof-of-billing', 'public');

        // Create the payment
        $ledger->payment()->create([
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'proof_of_billing' => $filePath,
            'status' => 'Pending',
            'date_received' => now(),
        ]);
        
        $this->showPaymentModal = false;
        
        $this->notification()->success(
            'Payment Submitted',
            'Your payment has been submitted for admin review.'
        );
    }

    public function render()
    {
        return view('livewire.client.portal-page');
    }
}
