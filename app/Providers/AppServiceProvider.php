<?php

namespace App\Providers;

use App\Models\Ledger;
use App\Models\Loan;
use App\Models\Payment;
use App\Observers\LogObserver;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
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
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Blade::render('<livewire:filament.custom-notification />')
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => Blade::render('<livewire:components.notification-bell />')
        );
        Loan::observe(LogObserver::class);
        Payment::observe(LogObserver::class);
        Ledger::observe(LogObserver::class);
    }
}
