<?php

namespace App\Filament\Resources\GenerateReportsResource\Pages;

use App\Filament\Resources\GenerateReportsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGenerateReports extends ListRecords
{
    protected static string $resource = GenerateReportsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
