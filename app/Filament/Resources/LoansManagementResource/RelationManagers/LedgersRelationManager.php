<?php

namespace App\Filament\Resources\LoansManagementResource\RelationManagers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action; 
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    private function updateLoanFinishedStatus($loan)
    {
        $totalLedgers = $loan->ledgers()->count();
        $paidLedgers = $loan->ledgers()->where('status', 'Paid')->count();
        
        $isFinished = ($totalLedgers > 0 && $totalLedgers === $paidLedgers);
        
        if ($loan->is_finished !== $isFinished) {
            $loan->update(['is_finished' => $isFinished]);
            
            if ($isFinished) {
                Notification::make()
                    ->title('Loan Completed!')
                    ->body("Loan for {$loan->user->name} has been fully paid.")
                    ->success()
                    ->send();
            }
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('loan')
            ->columns([
                TextColumn::make('loan.user.info.member_id')
                    ->label('Member ID'),
                TextColumn::make('loan.user.name')
                    ->label('Name'),
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
                        'Due' => 'danger',
                        default => 'gray'
                    }),  
            ])
            ->recordAction(null)
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Export Ledger PDF')
                ->label('Export as PDF')
                ->action(function ($livewire) {
                    $loan = $livewire->getOwnerRecord();

                    $pdf = Pdf::loadView('pdf.show-ledger', [
                        'loan' => $loan,
                        'ledgersCollection' => $loan->ledgers->values(),
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->stream()),
                        'loan-ledger.pdf'
                    );
                })
                ->icon('heroicon-o-arrow-down-tray')
                ->openUrlInNewTab(),
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('pay')
                    ->label('Pay')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->form([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->default(fn ($record) => $record->loan->payment_per_term)
                            ->disabled()
                            ->dehydrated() 
                            ->numeric()
                            ->required()
                            ->prefix('₱')
                            ->mask(RawJs::make(<<<'JS'
                                $money($input, '.', ',', 2)
                                JS
                                                    ))
                            ->stripCharacters([',']),
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'Cash' => 'Cash',
                                'Gcash' => 'Gcash',
                                'Bank Transfer' => 'Bank Transfer',
                            ])
                            ->required(),
                        DatePicker::make('date_received')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $payment = $record->payment()->create([
                            'amount' => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'date_received' => $data['date_received'],
                        ]);

                        $pdf = Pdf::loadView('pdf.receipt', [
                            'ledger' => $record,
                            'loan' => $record->loan,
                            'user' => $record->loan->user,
                        ]);

                        $receiptPath = 'receipts/' . $payment->id . '.pdf';
                        Storage::disk('public')->put($receiptPath, $pdf->output());

                        $payment->receipt = $receiptPath;
                        $payment->save();

                        $record->update(['status' => 'Paid']);

                        $this->updateLoanFinishedStatus($record->loan);

                        Notification::make()
                            ->title('Payment Successful')
                            ->success()
                            ->send();
                    })->visible(function ($record) {
                        $hasUnpaidEarlierLedger = $record->loan
                            ->ledgers()
                            ->where('due_date', '<', $record->due_date)
                            ->where('status', '!=', 'Paid')
                            ->exists();

                        return $record->status !== 'Paid' && !$hasUnpaidEarlierLedger;
                    }),
                    Action::make('downloadReceipt')
                        ->label('Receipt')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->visible(fn ($record) => $record->status === 'Paid' && optional($record->payment)->receipt)
                        ->action(function ($record) {
                            $receiptPath = optional($record->payment)->receipt;

                            if ($receiptPath && Storage::disk('public')->exists($receiptPath)) {
                                $filename = basename($receiptPath);
                                return response()->streamDownload(
                                    fn () => print(Storage::disk('public')->get($receiptPath)),
                                    $filename
                                );
                            }
                        })
                        ->openUrlInNewTab()
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
