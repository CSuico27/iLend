<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogsResource\Pages;
use App\Models\Log;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LogsResource extends Resource
{
    protected static ?string $model = Log::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $pluralModelLabel = 'Audit Trails'; 
    protected static ?string $navigationLabel = 'Logs';
    protected static ?int $navigationSort = 7;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member_id')
                    ->label('Member ID')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('user.info', function ($q) use ($search) {
                            $q->where('member_id', 'like', "%{$search}%");
                        });
                    })
                    ->getStateUsing(fn ($record) => 
                        $record->changes ? json_decode($record->changes)->affected_member_id ?? 'N/A' : 'N/A'
                    ),     
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Performed By')
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->user?->name ?? 'N/A'),
                
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Created' => 'warning',
                        'Updated' => 'success',
                        'Deleted' => 'danger',
                        default => 'gray'
                    }),
                
                Tables\Columns\TextColumn::make('model')
                    ->label('Entry')
                    ->getStateUsing(function ($record) {
                        $action = $record->action ?? 'Action';
                        return match($record->model) {
                            'User' => $action === 'Created' ? 'Created Account' : "{$action} User",
                            'Loan' => $action === 'Created' ? 'Created Loan' : "{$action} Loan",
                            'Payment' => $action === 'Created' ? 'Created Payment' : "{$action} Payment",
                            'Ledger' => $action === 'Created' ? 'Created Ledger' : "{$action} Ledger",
                            'SeminarSchedule' => $action === 'Created' ? 'Created Seminar' : "{$action} Seminar",
                            'gcash' => $action === 'Created' ? 'Created GCash QR' : "{$action} GCash QR",
                            default => $record->model
                        };
                    }),

                
                Tables\Columns\TextColumn::make('for_user')
                    ->label('Affected User')
                    ->getStateUsing(fn ($record) => 
                        $record->changes ? json_decode($record->changes)->affected_user ?? 'N/A' : 'N/A'
                    ),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->status ?? 'N/A'),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->getStateUsing(fn ($record) => $record->amount ? '₱' . number_format($record->amount, 2) : 'N/A'),
                
                Tables\Columns\TextColumn::make('loan_id')
                    ->label('Loan ID')
                    ->getStateUsing(fn ($record) => $record->loan_id ?? 'N/A'),
                
                Tables\Columns\TextColumn::make('ledger_id')
                    ->label('Ledger ID')
                    ->getStateUsing(fn ($record) => $record->ledger_id ?? 'N/A'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('model')
                    ->label('Model Type')
                    ->options([
                        'User' => 'User',
                        'Loan' => 'Loan',
                        'Payment' => 'Payment',
                        'Ledger' => 'Ledger',
                    ])
                    ->multiple(),
                
                SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'Created' => 'Created',
                        'Updated' => 'Updated',
                        'Deleted' => 'Deleted',
                    ])
                    ->multiple(),
                
                Filter::make('created_at')
                    ->label('Filter')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->maxDate(now()),
                        Forms\Components\DatePicker::make('created_until')
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })
            ])
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Filter')
            )
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogs::route('/'),
            // 'create' => Pages\CreateLogs::route('/create'),
            // 'edit' => Pages\EditLogs::route('/{record}/edit'),
        ];
    }
}