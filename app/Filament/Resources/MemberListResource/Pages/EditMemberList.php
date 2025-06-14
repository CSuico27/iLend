<?php

namespace App\Filament\Resources\MemberListResource\Pages;

use App\Filament\Resources\MemberListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberList extends EditRecord
{
    protected static string $resource = MemberListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
