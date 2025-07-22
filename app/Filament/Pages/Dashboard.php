<?php

namespace App\Filament\Pages;

use App\Models\SeminarSchedule;
use Filament\Facades\Filament;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    public function getViewData(): array
    {

        return [
            'widgets' => $this->getWidgets(),
        ];
    }
}
