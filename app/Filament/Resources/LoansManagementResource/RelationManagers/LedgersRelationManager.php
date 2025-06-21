<?php

namespace App\Filament\Resources\LoansManagementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['loan.user.info']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('loan')
            ->columns([
                TextColumn::make('loan.user.info.member_id')
                    ->label('Member ID')
                    ->searchable(),
                TextColumn::make('loan.user.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('loan.loan_amount')
                    ->label('Loan Amount')
                    ->money('PHP'),
                TextColumn::make('loan.total_payment')
                    ->label('Total Loan Payable')
                    ->money('PHP')
                    ->weight('bold'),
                TextColumn::make('loan.payment_per_term')
                    ->label('Payment Per Term')
                    ->money('PHP'),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('F j, Y'),
                TextColumn::make('loan.interest_rate')
                    ->label('Interest Rate')
                    ->suffix('%'),
                TextColumn::make('loan.interest_amount')
                    ->label('Interest Amount')
                    ->money('PHP'),
                TextColumn::make('loan.payment_frequency')
                    ->label('Payment Frequency')
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'biweekly' => 'Bi-weekly',
                        'monthly' => 'Monthly',
                        default => ucfirst($state ?? 'Unknown')
                    })
                    ->badge(),
                TextColumn::make('loan.loan_term')
                    ->label('Loan Term')
                    ->suffix(' months'),
                TextColumn::make('status')
                    ->label('Payment Status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Pending' => 'warning',
                        'Paid' => 'success',
                        default => 'gray'
                    }),  
            ])
            ->recordAction(null)
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
