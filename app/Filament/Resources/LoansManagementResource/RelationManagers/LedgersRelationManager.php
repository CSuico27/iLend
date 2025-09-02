<?php

namespace App\Filament\Resources\LoansManagementResource\RelationManagers;

use App\Mail\PaymentStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Tables\Actions\ActionGroup as TableActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action; 
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Title;

class LedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['loan.user.info', 'payment']);
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
                TextColumn::make('is_due')
                    ->label('Is Due')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success'),
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

                        $ledgers = $loan->ledgers()->orderBy('id')->get();
                        // $firstLedgerId = $ledgers->first()?->id ?? 'no-ledger';

                        $pdf = Pdf::loadView('pdf.show-ledger', [
                            'loan' => $loan,
                            'ledgersCollection' => $loan->ledgers->values(),
                        ]);

                        $userName = str_replace(' ', '_', strtolower($loan->user->name));
                        $timestamp = now()->format('Ymd');
                        $loanID = $loan->id;

                        $filename = "{$loanID}_{$userName}_ledger_{$timestamp}.pdf";
                        $path = "ledgers/{$filename}";
                        
                        Storage::disk('public')->put($path, $pdf->output());
                        
                        foreach ($ledgers as $ledger) {
                            $ledger->ledger_path = $path;
                            $ledger->save();
                        }

                        Notification::make()
                            ->title('Ledger has been saved')
                            ->success()
                            ->send();

                        return response()->download(
                            storage_path("app/public/{$path}"),
                            $filename
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
        'GCash' => 'GCash',
        'Bank Transfer' => 'Bank Transfer',
    ])
    ->required()
    ->reactive(),

Select::make('selected_gcash_qr')
    ->label('Select GCash QR')
    ->options(
        \App\Models\Gcash::pluck('id')->mapWithKeys(fn ($id) => [$id => "QR #{$id}"])
    )
    ->visible(fn ($get) => $get('payment_method') === 'GCash')
    ->reactive(),

Placeholder::make('gcash_qr_preview')
    ->label('')
    ->content(function ($get) {
        if ($get('payment_method') !== 'GCash' || !$get('selected_gcash_qr')) {
            return '';
        }

        $qr = \App\Models\Gcash::find($get('selected_gcash_qr'));

        return $qr && $qr->qr_path
            ? new HtmlString(
                '<img src="' . asset('storage/' . $qr->qr_path) . '" 
                    style="max-width: 250px; height: auto; border-radius: 10px; 
                    box-shadow: 0 4px 6px rgba(0,0,0,0.2);">'
              )
            : '';
    })
    ->visible(fn ($get) => $get('payment_method') === 'GCash' && $get('selected_gcash_qr'))
    ->extraAttributes(['class' => 'flex justify-center']),

                        FileUpload::make('proof_of_billing')
                            ->label('Upload Proof of Billing')
                            ->directory('proof-of-billing')
                            ->openable(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Approved')
                            ->required(),
                        DatePicker::make('date_received')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $payment = $record->payment()->create([
                            'amount' => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'proof_of_billing' => $data['proof_of_billing'],
                            'date_received' => $data['date_received'],
                            'status' => $data['status'],
                        ]);

                        // $pdf = Pdf::loadView('pdf.receipt', [
                        //     'ledger' => $record,
                        //     'loan' => $record->loan,
                        //     'user' => $record->loan->user,
                        // ]);

                        // $userName = str_replace(' ', '_', strtolower($record->loan->user->name));
                        // $paymentDate = Carbon::parse($data['date_received'])->format('Ymd');
                        // $receiptFileName = "{$payment->id}_receipt_{$userName}_{$paymentDate}.pdf";
                        // $receiptPath = 'receipts/' . $receiptFileName;
                        // Storage::disk('public')->put($receiptPath, $pdf->output());

                        // $payment->receipt = $receiptPath;
                        // $payment->save();

                        // $record->update(['status' => 'Paid']);

                        // $this->updateLoanFinishedStatus($record->loan);
                        if ($data['status'] === 'Approved') {
                            $pdf = Pdf::loadView('pdf.receipt', [
                                'ledger' => $record,
                                'loan' => $record->loan,
                                'user' => $record->loan->user,
                                'payment' => $payment,
                            ]);

                            $userName = str_replace(' ', '_', strtolower($record->loan->user->name));
                            $paymentDate = Carbon::parse($data['date_received'])->format('Ymd');
                            $receiptFileName = "{$payment->id}_receipt_{$userName}_{$paymentDate}.pdf";
                            $receiptPath = 'receipts/' . $receiptFileName;

                            Storage::disk('public')->put($receiptPath, $pdf->output());

                            $payment->update([
                                'receipt' => $receiptPath,
                            ]);

                            $record->update(['status' => 'Paid']);
                            $this->updateLoanFinishedStatus($record->loan);

                            Mail::to($record->loan->user->email)->send(
                                new PaymentStatus($payment, 'Approved')
                            );

                            Notification::make()
                                ->title('Payment Approved')
                                ->success()
                                ->send();
                        }

                        if($data['status'] === 'Rejected'){
                            Mail::to($record->loan->user->email)->send(
                                new PaymentStatus($payment, 'Rejected')
                            );
                            Notification::make()
                                ->title('Payment Rejected')
                                ->danger()
                                ->send();
                        }

                        Notification::make()
                            ->title('Payment has been submitted.')
                            ->success()
                            ->send();
                    })->visible(function ($record) {
                        $hasUnpaidEarlierLedger = $record->loan
                            ->ledgers()
                            ->where('due_date', '<', $record->due_date)
                            ->where('status', '!=', 'Paid')
                            ->exists();

                        return $record->status !== 'Paid' 
                            && !$hasUnpaidEarlierLedger
                            && optional($record->payment)->status !== 'Pending';
                    }),
                    
                    TableActionGroup::make([
                        Action::make('viewPayment')
                            ->label('View Payment')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->visible(fn ($record) => optional($record->payment)->status === 'Pending')
                            ->form([
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->prefix('₱')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($record) => number_format($record->payment->amount, 2)),

                                TextInput::make('payment_method')
                                    ->label('Payment Method')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($record) => $record->payment->payment_method),

                                TextInput::make('date_received')
                                    ->label('Date Received')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($record) => Carbon::parse($record->payment->date_received)->format('F j, Y')),

                                TextInput::make('status')
                                    ->label('Payment Status')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($record) => ucfirst($record->payment->status)),

                                FileUpload::make('proof_of_billing')
                                    ->label('Proof of Billing')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(fn ($record) => $record->payment->proof_of_billing ? [$record->payment->proof_of_billing] : [])
                                    ->openable(),
                            ])
                            ->modalHeading('View Payment Details')
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)  
                            ->modalCancelAction(false)   
                            ->extraModalFooterActions(fn($record) => [
                                Action::make('close')
                                    ->label('Close')
                                    ->color('gray')
                                    ->after(fn ($record) => redirect()->route(
                                'filament.admin.resources.loans-managements.edit',
                                ['record' => $record->loan_id] 
                                    )),
                                Action::make('approve')
                                    ->label('Approve')
                                    ->icon('heroicon-o-check-circle')
                                    ->color('success')
                                    ->extraAttributes(['class' => 'ml-auto'])
                                    ->visible(fn() => $record->payment?->status === 'Pending')
                                    ->action(function ($record) {
                                        $payment = $record->payment;

                                        $pdf = Pdf::loadView('pdf.receipt', [
                                            'ledger' => $record,
                                            'loan'   => $record->loan,
                                            'user'   => $record->loan->user,
                                            'payment'=> $payment,
                                        ]);

                                        $userName = str_replace(' ', '_', strtolower($record->loan->user->name));
                                        $paymentDate = Carbon::parse($payment->date_received)->format('Ymd');
                                        $receiptFileName = "{$payment->id}_receipt_{$userName}_{$paymentDate}.pdf";
                                        $receiptPath = 'receipts/' . $receiptFileName;

                                        Storage::disk('public')->put($receiptPath, $pdf->output());

                                        $payment->update([
                                            'status' => 'Approved',
                                            'receipt' => $receiptPath,
                                        ]);

                                        $record->update(['status' => 'Paid']);
                                        $this->updateLoanFinishedStatus($record->loan);

                                        Mail::to($record->loan->user->email)->send(
                                            new PaymentStatus($record->payment, 'Approved')
                                        );

                                        Notification::make()
                                            ->title('Payment Approved')
                                            ->success()
                                            ->send();
                                    })
                                    ->after(fn ($record) => redirect()->route(
                                'filament.admin.resources.loans-managements.edit',
                                ['record' => $record->loan_id] 
                                    )),

                                Action::make('reject')
                                    ->label('Reject')
                                    ->icon('heroicon-o-x-circle')
                                    ->color('danger')
                                    ->visible(fn() => $record->payment?->status === 'Pending')
                                    ->requiresConfirmation()
                                    ->action(function ($record) {
                                        $payment = $record->payment;
                                        $payment->update([
                                            'receipt' => null,
                                            'status' => 'Rejected',
                                            'proof_of_billing' => null,
                                        ]);
                                        $record->update(['status' => 'Pending']);

                                        Mail::to($record->loan->user->email)->send(
                                            new PaymentStatus($payment, 'Rejected')
                                        );

                                        Notification::make()
                                            ->title('Payment Rejected')
                                            ->danger()
                                            ->send();
                                    })
                                    ->after(fn ($record) => redirect()->route(
                                'filament.admin.resources.loans-managements.edit',
                                ['record' => $record->loan_id] 
                                    )),
                            ])

                    ]),
                   
                    // ->visible(function ($record) {
                    //     $approveVisible = optional($record->payment)->status === 'Pending' && $record->status === 'Pending';
                    //     $rejectVisible = optional($record->payment)->status === 'Pending' && $record->status === 'Pending';
                    //     $downloadVisible = optional($record->payment)->status === 'Approved' && optional($record->payment)->receipt && $record->status === 'Paid';

                    //     return $approveVisible || $rejectVisible || $downloadVisible;
                    // })
                    Action::make('downloadReceipt')
                        ->label('Download Receipt')
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
