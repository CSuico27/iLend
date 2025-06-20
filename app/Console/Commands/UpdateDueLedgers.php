<?php

namespace App\Console\Commands;

use App\Models\Ledger;
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
    protected $description = 'Mark past-due ledgers as Due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $updated = Ledger::where('status', 'Pending')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'Due']);

        $this->info("Updated $updated ledger(s) to Due.");
        Log::info("UpdateDueLedgers: Updated $updated ledger(s) to Due at " . now());
    }
}
