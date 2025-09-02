<?php

namespace App\Filament\Resources\GcashResource\Pages;

use App\Filament\Resources\GcashResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGcash extends EditRecord
{
    protected static string $resource = GcashResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
