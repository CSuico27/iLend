<?php

namespace App\Providers;

use App\Models\Ledger;
use App\Models\Loan;
use App\Models\Payment;
use App\Observers\LogObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Loan::observe(LogObserver::class);
        Payment::observe(LogObserver::class);
        Ledger::observe(LogObserver::class);
    }
}
