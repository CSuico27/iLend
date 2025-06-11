<?php

namespace App\Filament\Resources\LoansManagementResource\Pages;

use App\Filament\Resources\LoansManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoansManagement extends ListRecords
{
    protected static string $resource = LoansManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
