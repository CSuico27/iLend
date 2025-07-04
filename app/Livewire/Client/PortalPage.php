<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PortalPage extends Component
{
    public $activeTab = 'dashboard';

    public $activeLoanDetails;
    public $hasActiveLoan = false;
    public $activeLoanAmount = 0;
    public $totalPaid = 0;
    public $remainingBalance = 0;

    public $userLoans;
    public $selectedLoan = null;

    protected $queryString = ['activeTab'];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount(){
        if (Auth::check()) {
            
            $user = Auth::user();
            $userProfile = Auth::user()->info;

            if ($userProfile && $userProfile->status == 'Pending') {
                return redirect()->route('user.home')->with('portal_error', 'Your membership application is currently under review. Please wait for approval.');
            }

            $this->userLoans = $user->loans()->with(['ledgers.payment'])->get();

            $this->activeLoanDetails = $user->loans()
                ->with(['ledgers.payment'])
                ->where('is_finished', false)
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

    public function render()
    {
        return view('livewire.client.portal-page');
    }
}
