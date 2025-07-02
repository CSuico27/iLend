<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Collection;

class LoanHistory extends Component
{
    public $userId;
    public $user;
    public Collection $loans;

    public function mount($userId = null)
    {
        $this->userId = $userId;
        $this->loans = collect(); 
        $this->loadUserLoans();
    }

    public function loadUserLoans()
    {
        if ($this->userId) {
            $this->user = User::with([
                'loans' => fn ($query) => $query->where('status', 'Approved')->orderBy('created_at', 'desc')
            ])->find($this->userId);

            $this->loans = $this->user?->loans ?? collect();
        } else {
            $this->loans = collect();
        }
    }

    public function render()
    {
        return view('livewire.pages.loan-history', [
            'loans' => $this->loans,
        ]);
    }
}
