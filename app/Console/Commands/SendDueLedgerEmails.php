<?php

namespace App\Console\Commands;

use App\Mail\LedgerDueReminder;
use App\Models\Ledger;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;

class SendDueLedgerEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-due-ledger-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    public function schedule(Schedule $schedule)
    {
        // Run daily at 8:00 AM
        $schedule->command(static::class)->everyMinute();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $targetDates = [
            $now->copy()->addDays(5)->toDateString(),
            $now->copy()->addDays(2)->toDateString(),
        ];

        // Get all ledgers that are 5days/2days before the due_date
        $dueLedgers = Ledger::where('status', 'Pending')
            ->whereIn('due_date', $targetDates)
            ->with('loan.user')
            ->get();

        if ($dueLedgers->isEmpty()) {
            $this->info('No due ledgers found.');
            return 0;
        }

        $emailsSent = 0;

        foreach ($dueLedgers as $ledger) {
            $user = $ledger->loan->user;

            if (!$user || !$user->email) {
                $this->warn("Skipped Ledger ID {$ledger->id}: No user or email found");
                continue;
            }

            try {
                // Send email using Mailable
                Mail::to($user->email)->send(new LedgerDueReminder($user, $ledger));
                
                $this->info("Sent email to {$user->email} for Ledger ID {$ledger->id}");
                $emailsSent++;
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("\nTotal emails sent: {$emailsSent} out of {$dueLedgers->count()} due ledgers.");

    }
}
