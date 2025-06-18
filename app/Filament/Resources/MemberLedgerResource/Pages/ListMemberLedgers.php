<?php

namespace App\Filament\Resources\MemberLedgerResource\Pages;

use App\Filament\Resources\MemberLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberLedgers extends ListRecords
{
    protected static string $resource = MemberLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
