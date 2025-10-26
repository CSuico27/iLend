<?php

namespace App\Observers;

use App\Models\gcash;
use App\Models\Loan;
use App\Models\Log;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;

class LogObserver
{
    public function created($model): void
    {
        // Skip Ledger creation logs
        if (class_basename($model) === 'Ledger') {
            return;
        }

        if ($model instanceof \App\Models\SeminarSchedule) {
            if (isset($model->user_ids) && is_array($model->user_ids)) {
                foreach ($model->user_ids as $userId) {
                    $user = User::find($userId);

                    if ($user) {
                        Log::create([
                            'user_id' => Auth::id(), 
                            'action' => 'Created',
                            'model' => class_basename($model),
                            'model_id' => $model->id,
                            'status' => $model->status ?? 'N/A',
                            'changes' => json_encode([
                                'affected_user' => $user->name,
                                'affected_member_id' => $user->info->member_id ?? 'N/A',
                            ]),
                        ]);
                    }
                }
            }

            return;
        }

        $changes = [];

        if ($model instanceof Loan) {
            $model->refresh(); 
            $changes['status'] = $model->status ?? 'N/A';
        }

        if ($model instanceof User) {
            dispatch(function() use ($model, $changes) {
                $model->refresh();
                $model->load('info');
                $this->storeLog('Created', $model, $changes);
            })->afterResponse();
            return;
        }
        
        if ($model instanceof UserProfile) {
            $model->refresh(); 
            if ($model->status === 'Approved') {
                $changes['status'] = $model->status;
                $this->storeLog('Created', $model, $changes);
            }
            return; 
        }

        if ($model instanceof gcash) {
            $this->storeLog('Created', $model, ['gcash_id' => $model->id]);
            return;
        }

        $this->storeLog('Created', $model, $changes);

        if ($model instanceof Payment) {
            $this->createPaymentNotification($model);
        }
    }

    public function updated($model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']); 

        if ($model instanceof Loan) {
            $important = ['status']; 
            $changes = array_intersect_key($changes, array_flip($important));
        }

        if ($model instanceof User) {
            $important = ['email', 'role', 'name'];
            $changes = array_intersect_key($changes, array_flip($important));
        }

        if (class_basename($model) === 'Ledger') {
            if (isset($changes['status'])) {
                if ($model->status !== 'Paid') {
                    return; 
                }
                $model->load(['loan.user.info', 'payment']);
            } else {
                return; 
            }
        }

        if ($model instanceof UserProfile) {
            $important = ['status'];
            $changes = array_intersect_key($changes, array_flip($important));

            if (empty($changes)) {
                return;
            }

            $this->storeLog('Updated', $model, $changes);
            return;
        }

        if ($model instanceof \App\Models\SeminarSchedule) {
            if ($model->wasChanged('status')) {
                $oldStatus = $model->getOriginal('status');
                $newStatus = $model->status;

                if (isset($model->user_ids) && is_array($model->user_ids)) {
                    foreach ($model->user_ids as $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            Log::create([
                                'user_id' => Auth::id(),
                                'action' => 'Updated',
                                'model' => class_basename($model),
                                'model_id' => $model->id,
                                'status' => $newStatus,
                                'changes' => json_encode([
                                    'affected_user' => $user->name,
                                    'affected_member_id' => $user->info->member_id ?? 'N/A',
                                    'old_status' => $oldStatus ?? 'N/A',
                                    'new_status' => $newStatus ?? 'N/A',
                                ]),
                            ]);
                        }
                    }
                }
            }

            return;
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
                $extra['affected_member_id'] = $model->user?->info?->member_id ?? null;
                break;

            case 'Ledger':
                $amount = $model->payment->amount ?? null;
                $loanId = $model->loan_id ?? null;
                $ledgerId = $model->id;
                $extra['affected_user'] = $model->loan->user?->name ?? null;
                $extra['affected_member_id'] = $model->loan->user?->info?->member_id ?? null;
                break;

            case 'Payment':
                $amount = $model->amount ?? null;
                $loanId = $model->ledger->loan_id ?? null;
                $ledgerId = $model->ledger_id;
                $extra['affected_user'] = $model->ledger->loan->user?->name ?? null;
                $extra['affected_member_id'] = $model->ledger->loan->user?->info?->member_id ?? null;
                break;

            case 'User':
                $extra['affected_user'] = $model->name ?? null;
                $changes['user_email'] = $model->email ?? null;
                $changes['user_role'] = $model->role ?? null;
                $extra['affected_member_id'] = $model->info?->member_id ?? null;
                break;
            case 'UserProfile':
                $extra['affected_user'] = $model->user?->name ?? null;
                $extra['affected_member_id'] = $model->member_id ?? null;
                $changes['status'] = $model->status ?? null;
                break;
            case 'SeminarSchedule':
                if (isset($model->user_ids) && is_array($model->user_ids)) {
                    foreach ($model->user_ids as $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            Log::create([
                                'user_id' => Auth::id(),
                                'action' => $action,
                                'model' => class_basename($model),
                                'model_id' => $model->id,
                                'status' => $model->status ?? 'N/A',
                                'changes' => json_encode([
                                    'affected_user' => $user->name,
                                    'affected_member_id' => $user->info->member_id ?? 'N/A',
                                ]),
                            ]);
                        }
                    }
                }
                return;
            case 'gcash':
                $extra['affected_user'] = null;
                $extra['affected_member_id'] = null;
                $changes['gcash_id'] = $model->id;
            break;
            default:
                break;
        }

        if (!empty($extra)) {
            $changes = array_merge($extra, $changes);
        }
        $userId = Auth::check() ? Auth::id() : null;
    
        if ($action === 'Created' && $model instanceof User && !$userId) {
            $userId = $model->id;
        }

        try {
            Log::create([
                'action'    => $action,
                'model'     => class_basename($model),
                'model_id'  => $model->id,
                'changes'   => !empty($changes) ? json_encode($changes) : null,
                'user_id'   => $userId,
                'amount'    => $amount,
                'status'    => $changes['status'] ?? $model->status ?? null,
                'loan_id'   => $loanId,
                'ledger_id' => $ledgerId,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to store log: ' . $e->getMessage());
        }
    }
    protected function createPaymentNotification($payment): void
    {
        $userName = $payment->ledger->loan->user?->name ?? 'Unknown User';

        try {
            \App\Models\Notifications::create([
                'message' => "{$userName} has made a payment of ₱{$payment->amount} for Ledger ID: {$payment->ledger->id} under Loan ID: {$payment->ledger->loan_id}.",
                'loan_id' => $payment->ledger->loan_id,
                'ledger_id' => $payment->ledger->id,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create payment notification: ' . $e->getMessage());
        }
    }
}
