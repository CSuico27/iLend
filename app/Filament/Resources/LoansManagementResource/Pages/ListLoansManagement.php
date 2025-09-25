<?php

namespace App\Filament\Resources\LoansManagementResource\Pages;

use App\Filament\Resources\LoansManagementResource;
use App\Models\Interest;
use Dompdf\Css\Color;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLoansManagement extends ListRecords
{
    protected static string $resource = LoansManagementResource::class;

    protected function getHeaderActions(): array
    {
        $rate = Interest::orderByDesc('created_at')->value('interest_rate') ?? 'Not set';
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('newInterest')
                ->label('New Interest Rate')
                ->color('warning') 
                ->icon('heroicon-o-currency-dollar')
                ->form([
                    TextInput::make('interest_rate')
                        ->label('Interest Rate (%)')
                        ->numeric()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Interest::create([
                        'interest_rate' => $data['interest_rate'],
                    ]);
                })
                ->modalHeading('Set New Interest Rate')
                ->modalButton('Save Rate'),
            
            Actions\Action::make('currentRate')
                ->label("Current Interest Rate: {$rate}%")
                ->color('gray')
                ->disabled()
                ->icon('heroicon-o-information-circle'),
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
