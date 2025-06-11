<?php

namespace App\Filament\Resources\SeminarScheduleResource\Pages;

use App\Filament\Resources\SeminarScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeminarSchedules extends ListRecords
{
    protected static string $resource = SeminarScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
