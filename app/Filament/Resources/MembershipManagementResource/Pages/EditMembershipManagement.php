<?php

namespace App\Filament\Resources\MembershipManagementResource\Pages;

use App\Filament\Resources\MembershipManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMembershipManagement extends EditRecord
{
    protected static string $resource = MembershipManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
