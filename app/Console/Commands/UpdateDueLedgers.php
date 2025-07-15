<?php

namespace App\Console\Commands;

use App\Models\CreditScore;
use App\Models\Ledger;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class UpdateDueLedgers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-due-ledgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark past-due ledgers as Due and recalculate credit scores.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $ledgers = Ledger::where('status', 'Pending')
            ->whereDate('due_date', '<', $today)
            ->get();

        $updatedCount = 0;
        
        foreach ($ledgers as $ledger) {
            $ledger->update([
                'is_due' => 1,
            ]);
            $updatedCount++;
        }

        $this->info("Updated $updatedCount ledger(s) to Due and is_due = 1.");
        Log::info("UpdateDueLedgers: Updated $updatedCount ledger(s) to Due and is_due = 1 at " . now());
        

        $this->recalculateCreditScores();

        $this->info("Credit scores recalculated.");
    }

    protected function recalculateCreditScores()
    {
        User::with(['loans.ledgers.payment'])   
            ->where('role', '!=', 'admin')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $score = 50;
                    $onTime = 0;
                    $late = 0;
                    $unpaid = 0;
                    $completedLoans = 0;

                    foreach ($user->loans as $loan) {
                        $loanCompleted = true;

                        foreach ($loan->ledgers as $ledger) {
                            $dueDate = Carbon::parse($ledger->due_date);
                            $payment = $ledger->payment;

                            if ($ledger->status === 'Paid') {
                                if (
                                    $payment &&
                                    $payment->status === 'Approved'
                                ) {
                                    $receivedDate = Carbon::parse($payment->date_received);
                                    if ($receivedDate->lte($dueDate)) {
                                        $onTime++;
                                        $score += 2;
                                        Log::info("On-time approved payment → User #{$user->id}, Ledger #{$ledger->id}");
                                    } else {
                                        $late++;
                                        $score += 1;
                                        Log::info("Late approved payment → User #{$user->id}, Ledger #{$ledger->id}");
                                    }
                                } else {
                                    Log::info("Ignored unapproved payment → User #{$user->id}, Ledger #{$ledger->id}, Payment Status: {$payment?->status}");
                                }
                            } elseif ($ledger->status === 'Pending') {
                                if ($dueDate->lt(now())) {
                                    // Unpaid & overdue
                                    $unpaid++;
                                    $score -= 3;
                                    $loanCompleted = false;
                                    Log::info("Unpaid overdue ledger → User #{$user->id}, Ledger #{$ledger->id}, Due: {$dueDate}");
                                } else {
                                    // Still before due date
                                    Log::info("Upcoming payment → User #{$user->id}, Ledger #{$ledger->id}, Due: {$dueDate}");
                                }
                            }

                            Log::info("User #{$user->id} Ledger #{$ledger->id}: Payment Received = {$payment?->date_received}, Status = {$payment?->status}, Due = {$dueDate}");
                        }

                        if ($loan->is_finished) {
                            $completedLoans++;
                            $score += 5;
                        } elseif (!$loanCompleted && Carbon::parse($loan->end_date)->lt(now())) {
                            $score -= 5;
                        }
                    }

                    $score = max(0, min(100, $score));

                    $tier = match (true) {
                        $score >= 81 => 'Excellent',
                        $score >= 61 => 'Good',
                        $score >= 41 => 'Fair',
                        default => 'Poor',
                    };

                    CreditScore::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'score' => $score,
                            'tier' => $tier,
                            'remarks' => "Auto: +{$onTime} on-time, +{$late} late, -{$unpaid} unpaid, {$completedLoans} completed"
                        ]
                    );

                    Log::info("User #{$user->id} Final Score: {$score} | On-time: {$onTime}, Late: {$late}, Unpaid: {$unpaid}, Completed Loans: {$completedLoans}");
                }
            });
    }


}
