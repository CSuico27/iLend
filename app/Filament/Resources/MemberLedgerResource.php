<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberLedgerResource\Pages;
use App\Filament\Resources\MemberLedgerResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberLedgerResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $pluralModelLabel = 'Member Ledger'; 
    protected static ?string $navigationLabel = 'Member Ledger';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Loans Management';
    protected static ?int $navigationSort = 4;
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user.info']) 
            ->where('status', 'Approved'); 
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
                TextColumn::make('user.info.member_id')
                    ->label('Member ID')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('loan_amount')
                    ->label('Loan Amount')
                    ->money('PHP'),

                TextColumn::make('interest_rate')
                    ->label('Interest Rate')
                    ->suffix('%'),

                TextColumn::make('interest_amount')
                    ->label('Interest Amount')
                    ->money('PHP'),

                TextColumn::make('total_payment')
                    ->label('Total Loan Payable')
                    ->money('PHP')
                    ->weight('bold'),

                TextColumn::make('payment_frequency')
                    ->label('Payment Frequency')
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'biweekly' => 'Bi-weekly',
                        'monthly' => 'Monthly',
                        default => ucfirst($state ?? 'Unknown')
                    })
                    ->badge(),

                TextColumn::make('payment_per_term')
                    ->label('Payment Per Term')
                    ->money('PHP'),

                TextColumn::make('loan_term')
                    ->label('Loan Term')
                    ->suffix(' months'),

                TextColumn::make('ledgers.due_date')
                    ->label('Due Date'),

                TextColumn::make('ledgers.status')
                    ->label('Payment Status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Pending' => 'warning',
                        'Paid' => 'success',
                        default => 'gray'
                    }),  
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make()
                //     ->modalWidth('4xl'),
                    
                // Tables\Actions\Action::make('view_schedule')
                //     ->label('Payment Schedule')
                //     ->icon('heroicon-o-calendar-days')
                //     ->color('info')
                    // ->modalContent(function ($record) {
                    //     // You can create a custom view to show payment schedule
                    //     return view('filament.loan-schedule', compact('record'));
                    // })
                    // ->modalWidth('3xl'),
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
            'index' => Pages\ListMemberLedgers::route('/'),
            // 'create' => Pages\CreateMemberLedger::route('/create'),
            // 'edit' => Pages\EditMemberLedger::route('/{record}/edit'),
        ];
    }
}