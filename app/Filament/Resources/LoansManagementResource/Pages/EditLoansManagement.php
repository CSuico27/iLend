<?php

namespace App\Filament\Resources\LoansManagementResource\Pages;

use App\Filament\Resources\LoansManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoansManagement extends EditRecord
{
    protected static string $resource = LoansManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
    public function getTitle(): string
    {
        return 'Member Ledger'; 
    }
    public function getBreadcrumb(): string
    {
        return 'View Ledger';
    }
    protected function getFormActions(): array
    {
        return [];
    }
}
