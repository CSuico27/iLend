<?php

namespace App\Livewire\Client;

use App\Models\CreditScore;
use App\Models\Interest;
use App\Models\Ledger;
use App\Models\Loan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\WireUiActions;

#[Title('Portal')]
class PortalPage extends Component
{
    use WireUiActions;
    use WithFilePond;
    use WithFileUploads;
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

    public $userInfo;

    public $showProfileEditModal = false;
    public $phone;
    public $address;
    public $avatar;

    protected $queryString = ['activeTab'];
    public $selected_gcash_qr;
    public $gcashQrs = [];
    public $remainingLedgers = 0;


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
            $this->userInfo = User::with('info')->find(Auth::id());

            //for edit profile fields
            $this->phone = $this->userInfo->info->phone ?? '';
            $this->address = $this->userInfo->info->address ?? '';

            $this->interest_rate = Interest::orderByDesc('created_at')->value('interest_rate') ?? 0;

            //for gcash qr codes
            $this->gcashQrs = \App\Models\gcash::all();

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

                $this->remainingLedgers = $this->activeLoanDetails
                    ->ledgers()
                    ->where('status', '!=', 'Paid')
                    ->count();
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

        $user = Auth::user();

        if (!$user->info() || !$user->info->approved_at) {
            return false;
        }

        $approvedAt = Carbon::parse($user->info->approved_at);
        $diff = $approvedAt->diff(now());
        
        if ($diff->y < 1) {
            return false;
        }

        $userLoans = $this->userLoans ?? $user->loans()->get();

        if ($userLoans->isEmpty()) {
            return true;
        }

        return $userLoans->every(fn($loan) => $loan->is_finished && $loan->status !== 'Pending');
    }
    public function getLoanApplicationErrorProperty()
    {
        $user = Auth::user();

        $approvedAt = Carbon::parse($user->info->approved_at);
        $diff = $approvedAt->diff(now());
        
        if ($diff->y < 1) {
            return 'not_one_year';
        }

        $userLoans = $this->userLoans ?? $user->loans()->get();

        if ($userLoans->isNotEmpty()) {
        
            if ($userLoans->contains(fn($loan) => $loan->status === 'Pending')) {
                return 'pending_application';
            }

            if ($userLoans->contains(fn($loan) => !$loan->is_finished && $loan->status === 'Approved')) {
                return 'ongoing_loan';
            }
        }

        return null; 
    }
    public function getPopupErrorData()
    {
        $errorType = $this->loanApplicationError;

        $popupData = [
            'not_one_year' => [
                'title' => 'Membership Requirement',
                'message' => 'You must be a member for at least 1 year before applying for a loan.',
            ],
            'pending_application' => [
                'title' => 'Pending Application',
                'message' => 'You already have a pending loan application. Please wait for it to be processed.',
            ],
            'ongoing_loan' => [
                'title' => 'Ongoing Loan',
                'message' => 'You already have an ongoing loan. Please complete it before applying for a new one.',
            ],
        ];

        return $popupData[$errorType] ?? [
            'title' => 'Loan Application Blocked',
            'message' => 'You cannot apply for a loan at this time.',
        ];
    }

    public function openLoanApplicationModal()
    {
        if (! $this->canApply) {
            $popup = $this->getPopupErrorData();

            $this->notification()->error(
                $popup['title'],
                $popup['message']
            );
            return;
        }

        $this->showLoanApplicationModal = true;
    }

    public function calculateLoan()
    {
        $loanAmount = (float) preg_replace('/[^\d.]/', '', $this->loan_amount ?? '');

        if (Auth::check() && Auth::user()->role === 'user') {
            $interestRate = Interest::latestRate();
        } else {
            $interestRate = (float) ($this->interest_rate ?? 0);
        }

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
            $this->calculateLoan();
            
            if (Auth::check() && Auth::user()->role === 'user') {
                $calculatedInterestRate = Interest::latestRate();
            } else {
                $calculatedInterestRate = (float) ($this->interest_rate ?? 0);
            }
            
            $this->validate([
                'user_id' => 'required|exists:users,id',
                'loan_amount' => 'required|numeric|max:100000',
                'loan_type' => 'required|string|max:255',
                'interest_rate' => 'nullable|numeric|max:100',
                'loan_term' => 'required|integer|min:1|max:60',
                'payment_frequency' => 'required|string|in:daily,weekly,biweekly,monthly',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'interest_amount' => 'nullable|numeric|min:0',
                'total_payment' => 'nullable|numeric|min:0',
                'payment_per_term' => 'nullable|numeric|min:0',
            ]);

            loan::create([
                'user_id' => $this->user_id,
                'loan_type' => $this->loan_type,
                'loan_amount' => (float) preg_replace('/[^\d.]/', '', $this->loan_amount ?? ''),
                'interest_rate' => $calculatedInterestRate,
                'loan_term' => (int) ($this->loan_term ?? 0),
                'payment_frequency' => $this->payment_frequency,
                'start_date' => $this->start_date instanceof Carbon ? $this->start_date->format('Y-m-d H:i:s') : $this->start_date,
                'end_date' => $this->end_date instanceof Carbon ? $this->end_date->format('Y-m-d H:i:s') : $this->end_date,
                'interest_amount' => (float) ($this->interest_amount ?? 0),
                'total_payment' => (float) ($this->total_payment ?? 0),
                'payment_per_term' => (float) ($this->payment_per_term ?? 0),
            ]);
            
            $this->notification()->success(
                'Success!',
                'Loan application submitted successfully. We\'ll notify you once it\'s approved.'
            );
            
            $this->showLoanApplicationModal = false;
            
            $this->reset([
                'loan_amount', 
                'loan_type', 
                'interest_rate', 
                'loan_term', 
                'payment_frequency',
                'start_date',
                'end_date',
                'interest_amount',
                'total_payment',
                'payment_per_term'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notification()->error(
                'Error!',
                'Loan application is invalid. Please check your input fields.'
            );
            return;
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error!',
                'An unexpected error occurred. Please try again.'
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

        if ($this->activeLoanDetails) {
            $this->remainingLedgers = $this->activeLoanDetails
                ->ledgers()
                ->where('status', '!=', 'Paid')
                ->count();
        }
        
        $this->showPaymentModal = false;
        
        $this->notification()->success(
            'Payment Submitted',
            'Your payment has been submitted for admin review.'
        );
    }
    public function updateProfile()
    {
        $this->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar'  => 'nullable|image|max:2048',
        ]);

        $phone = $this->phone;
        if ($phone && !str_starts_with($phone, '+63')) {
            $phone = ltrim($phone, '0');
            $phone = '+63 ' . $phone;
        }

        $user = Auth::user();

         if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $user->update(['avatar' => $path]); // avatar column in users table
        }

        $user->info->update([
            'phone'   => $phone,
            'address' => $this->address,
        ]);

        $this->notification()->success('Profile Updated', 'Your profile information has been saved successfully.');

        $this->userInfo->refresh();
        $this->showProfileEditModal = false;
    }

    public function generateAndDownloadLedger($loanId)
    {
        try {
            $loan = Loan::with(['user', 'ledgers.payment'])->findOrFail($loanId);
            
            if ($loan->user_id !== Auth::id()) {
                $this->notification()->error('Access Denied', 'You can only download your own loan ledger.');
                return;
            }

            $ledgers = $loan->ledgers()->orderBy('id')->get();
            $firstLedgerId = $ledgers->first()?->id ?? 'no-ledger';

            $pdf = Pdf::loadView('pdf.show-ledger', [
                'loan' => $loan,
                'ledgersCollection' => $loan->ledgers->values(),
            ]);

            $userName = str_replace(' ', '_', strtolower($loan->user->name));
            $timestamp = now()->format('Ymd');

            $filename = "{$firstLedgerId}_{$userName}_ledger_{$timestamp}.pdf";

            $path = "ledgers/{$filename}";
            Storage::disk('public')->put($path, $pdf->output());

            $loan->update(['ledger_pdf_path' => $path]);

            $this->notification()->success('PDF Generated', 'Ledger saved as: ' . $filename);
            
            $this->dispatch('$refresh');

            return response()->streamDownload(
                fn () => print($pdf->stream()),
                $filename
            );

        } catch (\Exception $e) {
            $this->notification()->error('Generation Failed', 'Unable to generate ledger PDF. Please try again.');
            logger('Ledger generation error: ' . $e->getMessage());
        }
    }

    public function getRemainingLedgersCount($loanId)
    {
        $loan = Loan::with('ledgers')->findOrFail($loanId);

        return $loan->ledgers()->where('status', '!=', 'Paid')->count();
    }

    public function render()
    {
        return view('livewire.client.portal-page');
    }
}
