<?php

namespace App\Filament\Resources\MembershipManagementResource\Pages;

use App\Filament\Resources\MembershipManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembershipManagement extends ListRecords
{
    protected static string $resource = MembershipManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Member')->modalWidth('2xl'),
        ];
    }
    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn () => null;
    }
}