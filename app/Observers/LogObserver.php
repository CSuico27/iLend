<?php

namespace App\Observers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;


class LogObserver
{
    public function created($model): void
    {
        // Skip Ledger creation logs
        if (class_basename($model) === 'Ledger') {
            return;
        }

        $changes = [];

        if ($model instanceof \App\Models\Loan) {
           
            $model->refresh(); 

            $changes['status'] = $model->status ?? 'N/A';
        }

        $this->storeLog('Created', $model, $changes);

        if ($model instanceof \App\Models\Payment) {
            $this->createPaymentNotification($model);
        }

        $this->storeLog('Created', $model);
    }

    public function updated($model): void
    {
        $changes = $model->getChanges();

        unset($changes['updated_at']);

        if ($model instanceof \App\Models\Loan) {
            $important = ['status']; 
            $importantChanges = array_intersect_key($changes, array_flip($important));

            if (empty($importantChanges)) {
                return; 
            }

            $changes = $importantChanges;
        }

        if (empty($changes)) {
            return;
        }

        $this->storeLog('Updated', $model, $changes);
    }

    public function deleted($model): void
    {
        $this->storeLog('Deleted', $model);
    }

    protected function storeLog(string $action, $model, array $changes = []): void
    {
        $ledgerId = null;
        $loanId = null;
        $amount = null;
        $extra = [];
        switch (class_basename($model)) {
            case 'Loan':
                $amount = $model->loan_amount ?? null;
                $loanId = $model->id; 
                $ledgerId = $model->ledger_id ?? null;
                $extra['affected_user'] = $model->user?->name ?? null;
                break;

            case 'Ledger':
                $amount = $model->payment->amount ?? null;
                $loanId = $model->loan_id ?? null;
                $ledgerId = $model->id;
                $extra['affected_user'] = $model->loan->user?->name ?? null;
                break;

            case 'Payment':
                $amount = $model->amount ?? null;
                $loanId = $model->ledger->loan_id ?? null;
                $ledgerId = $model->ledger_id ?? null;
                $extra['affected_user'] = $model->ledger->loan->user?->name ?? null;
                break;
        }

        if (!empty($extra)) {
            $changes = array_merge($extra, $changes);
        }

        Log::create([
            'action'    => $action,
            'model'     => class_basename($model),
            'model_id'  => $model->id,
            'changes'   => !empty($changes) ? json_encode($changes) : null,
            'user_id'   => Auth::id(),
            'amount'    => $amount,
            'status'    => $changes['status'] ?? $model->status ?? null,
            'loan_id'   => $loanId,
            'ledger_id' => $ledgerId
        ]);
    }
    protected function createPaymentNotification($payment): void
    {
         $userName = $payment->ledger->loan->user?->name ?? 'Unknown User';

        \App\Models\Notifications::create([
            'message' => "{$userName} has made a payment of ₱{$payment->amount} for Ledger ID: {$payment->ledger->id} under Loan ID: {$payment->ledger->loan_id}.",
            'is_read' => false,
        ]);
    }
}
