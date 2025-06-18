<?php

namespace App\Filament\Resources\MemberLedgerResource\Pages;

use App\Filament\Resources\MemberLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberLedger extends EditRecord
{
    protected static string $resource = MemberLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
