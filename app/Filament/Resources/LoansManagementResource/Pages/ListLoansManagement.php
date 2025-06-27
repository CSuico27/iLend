<?php

namespace App\Filament\Resources\LoansManagementResource\Pages;

use App\Filament\Resources\LoansManagementResource;
use Dompdf\Css\Color;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLoansManagement extends ListRecords
{
    protected static string $resource = LoansManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'Pending' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'Pending')
                    ),
            'Approved' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'Approved')
                        ->where('is_finished', 0)
                ),
            'Rejected' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'Rejected')
                ),
            'Completed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('is_finished', 1)
                        ->where('status', 'Approved')
                ),
        ];
    }
}
