<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoansManagementResource\Pages;
use App\Filament\Resources\LoansManagementResource\RelationManagers\LedgersRelationManager;
use App\Mail\LoanStatus;
use App\Models\Ledger;
use App\Models\Loan;
use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class LoansManagementResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $modelLabel = 'Application';
    protected static ?string $pluralModelLabel = 'Loans'; 
    protected static ?string $navigationLabel = 'Loans Management';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    // protected static ?string $navigationGroup = 'Loans Management';
    protected static ?int $navigationSort = 3;

    /**
     * @param Set 
     * @param Get 
     * @return void
     */
    private static function updateCalculatedLoanFields(Set $set, Get $get): void
    {
            $loanAmount = (float) preg_replace('/[^\d.]/', '', $get('loan_amount') ?? '');
            $interestRate = (float) ($get('interest_rate') ?? 0);
            $loanTerm = (int) ($get('loan_term') ?? 0);
            $paymentFrequency = $get('payment_frequency') ?? 'monthly';  
            
            $interestAmount = $loanAmount * ($interestRate / 100);
            $set('interest_amount', number_format($interestAmount, 2));
            
            $totalPayment = $loanAmount + $interestAmount;
            $set('total_payment', number_format($totalPayment, 2));

            $count = 0; 
            
            if ($loanTerm > 0 && $paymentFrequency) {
                $count = match ($paymentFrequency) {
                    'daily' => $loanTerm * 30,     
                    'weekly' => $loanTerm * 4.33,  
                    'biweekly' => $loanTerm * 2.17,
                    'monthly' => $loanTerm,   
                    default => 0, 
                };
            }

            $paymentPerTerm = $count > 0 ? $totalPayment / $count : 0;
            $set('payment_per_term', number_format($paymentPerTerm, 2));
            
            $today = Carbon::now();
            $start = match ($paymentFrequency) {
                'daily' => $today->copy()->addDay(),
                'weekly' => $today->copy()->addWeek(),
                'biweekly' => $today->copy()->addWeeks(2),
                'monthly' => $today->copy()->addMonth(),
                default => $today,
            };

            $end = match ($paymentFrequency) {
                'daily' => $start->copy()->addDays($loanTerm * 30),
                'weekly' => $start->copy()->addWeeks((int) round($loanTerm * 4.34)),
                'biweekly' => $start->copy()->addWeeks(($loanTerm * 2.17) + 2),
                'monthly' => $start->copy()->addMonths($loanTerm - 1),
                default => $start,
            };

            $set('start_date', $start->toDateString());
            $set('end_date', $end->toDateString());

        //     $startDate = Carbon::parse($get('start_date'));
        //     $paymentFrequency = $get('payment_frequency') ?? 'monthly';
        //     $currentLedgers = $get('ledgers') ?? [];
            
        //     $newLedgers = [];

        //     for ($i = 0; $i < $count; $i++) {
        //     $existingStatus = $currentLedgers[$i]['status'] ?? 'Pending';

        //     $newLedgers[] = [
        //         'status' => $existingStatus,
        //         'due_date' => $startDate->copy()->toDateString(), 
        //     ];

        //     $startDate = match ($paymentFrequency) {
        //         'daily' => $startDate->addDay(),
        //         'weekly' => $startDate->addWeek(),
        //         'biweekly' => $startDate->addWeeks(2),
        //         'monthly' => $startDate->addMonth(),
        //         default => $startDate,
        //     };
        //     $set('ledgers', $newLedgers);
        // }
    }
    /**
     * @param array 
     * @return array 
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $today = Carbon::now();

        $loanAmount = (float) preg_replace('/[^\d.]/', '', $data['loan_amount'] ?? '');
       
        $interestRate = (float) ($data['interest_rate'] ?? 0);
        $loanTerm = (int) ($data['loan_term'] ?? 0);
        $paymentFrequency = $data['payment_frequency'] ?? 'monthly'; 

        $start = $today; 
        switch ($paymentFrequency) {
            case 'daily':    $start = $today->copy()->addDay(); break;
            case 'weekly':   $start = $today->copy()->addWeek(); break;
            case 'biweekly': $start = $today->copy()->addWeeks(2); break;
            case 'monthly':  $start = $today->copy()->addMonth(); break;
        }
        $data['start_date'] = $start->toDateString();

        $data['end_date'] = $start->copy()->addMonths($loanTerm )->toDateString();

        $interestAmount = $loanAmount * ($interestRate / 100);
        
        $data['interest_amount'] = round($interestAmount, 2);

        $totalPayment = $loanAmount + $interestAmount;
        $data['total_payment'] = round($totalPayment, 2);

        $count = 0;
        if ($loanTerm > 0 && $paymentFrequency) {
            $count = match ($paymentFrequency) {
                'daily' => $loanTerm * 30,
                'weekly' => $loanTerm * 4.33,
                'biweekly' => $loanTerm * 2.17,
                'monthly' => $loanTerm,
                default => 0,
            };
        }
        $paymentPerTerm = $count > 0 ? $totalPayment / $count : 0;
        $data['payment_per_term'] = round($paymentPerTerm, 2);

        $data['interest_amount'] = (float) preg_replace('/[^\d.]/', '', $data['interest_amount'] ?? '0');
        $data['total_payment'] = (float) preg_replace('/[^\d.]/', '', $data['total_payment'] ?? '0');
        $data['payment_per_term'] = (float) preg_replace('/[^\d.]/', '', $data['payment_per_term'] ?? '0');

        return $data; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('user_id')
                        ->label('Pangalan ng Umutang') 
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query
                                ->where('role', '!=', 'admin') 
                                ->whereHas('info', fn ($q) => $q
                                    ->where('status', 'Approved')
                                    ->where('approved_at', '<=', Carbon::now()->subYear())
                                ) 
                                ->whereDoesntHave('loans', fn ($q) => $q
                                    ->where('is_finished', 0)
                                )
                        )
                        ->required(),

                    Grid::make(2)->schema([
                        Select::make('loan_type')
                        ->label('Uri ng Loan')
                        ->options([
                            'regular' => 'Regular Loan',
                            'emergency' => 'Emergency Loan',
                            'car' => 'Car Loan',
                        ])
                        ->required(),

                        TextInput::make('loan_amount')
                            ->label('Halagang Hiniram ₱')
                            ->numeric()
                            ->prefix('₱')
                            ->placeholder('15,000.00')
                            ->helperText('Maximum allowed amount is ₱100,000.00')
                            ->rules(['numeric', 'max:100000.00'])
                            ->mask(RawJs::make(<<<'JS'
                                ($input) => {
                                    const digits = $input.replace(/[^\d]/g, '');

                                    if (!digits) return '';

                                    const number = parseFloat(digits) / 100;

                                    if (number > 100000) return '100,000.00';

                                    return number.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            JS))
                            ->stripCharacters([','])
                            ->required()
                            ->reactive()
                            ->extraAttributes(['class' => 'text-right']),

                        TextInput::make('interest_rate')
                            ->label('Interest Rate (%)')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCalculatedLoanFields($set, $get)),

                        Select::make('loan_term')
                            ->label('Tagal ng Buwan')
                            ->options([
                                3 => '3 months',
                                6 => '6 months',
                                9 => '9 months',
                                12 => '12 months',
                                24 => '24 months',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $frequency = $get('payment_frequency');
                                $start = match ($frequency) {
                                    'daily' => now()->addDay(),
                                    'weekly' => now()->addWeek(),
                                    'biweekly' => now()->addWeeks(2),
                                    'monthly' => now()->addMonth(),
                                    default => now(),
                                };
                                $end = $start->copy()->addMonths((int) $state);
                                $set('start_date', $start->toDateString());
                                $set('end_date', $end->toDateString());
                                self::updateCalculatedLoanFields($set, $get);
                            }),
                    ]),
                    Radio::make('payment_frequency')
                        ->label('Uri ng Hulugan')
                        ->options([
                            'daily' => 'Araw-Araw',
                            'weekly' => 'Linguhan',
                            'biweekly' => 'Ikalawang Linggo',
                            'monthly' => 'Buwanan',
                        ])
                        ->columns(2)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $term = (int) $get('loan_term');
                            $start = match ($state) {
                                'daily' => now()->addDay(),
                                'weekly' => now()->addWeek(),
                                'biweekly' => now()->addWeeks(2),
                                'monthly' => now()->addMonth(),
                                default => now(),
                            };
                            $end = $start->copy()->addMonths($term);
                            $set('start_date', $start->toDateString());
                            $set('end_date', $end->toDateString());
                            self::updateCalculatedLoanFields($set, $get);
                        }),
                    Grid::make(2)->schema([
                        DatePicker::make('start_date')
                        ->label('Date ng Unang Bayad')
                        ->disabled()
                        ->dehydrated(true) 
                        ->default(now()), 

                        DatePicker::make('end_date')
                            ->label('Date ng Huling Bayad')
                            ->disabled() 
                            ->dehydrated(true)
                            ->default(fn (Get $get) => now()->addMonths((int) $get('loan_term'))),

                        TextInput::make('interest_amount')
                            ->label('Kabuuang Interest')
                            ->disabled()
                            ->reactive()
                            ->prefix('₱') 
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => (float) preg_replace('/[^\d.]/', '', $state ?? '0'))
                            ->extraAttributes(attributes: ['class' => 'text-right']), 

                        TextInput::make('total_payment')
                            ->label('Kabuuang Babayaran')
                            ->disabled()  
                            ->reactive()
                            ->prefix('₱')
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => (float) preg_replace('/[^\d.]/', '', $state ?? '0'))
                            ->extraAttributes(['class' => 'text-right']),

                        TextInput::make('payment_per_term')
                            ->label('Halagang Babayaran kada Hulugan')
                            ->disabled() 
                            ->reactive()
                            ->dehydrated(true)
                            ->prefix('₱')
                            ->dehydrateStateUsing(fn ($state) => (float) preg_replace('/[^\d.]/', '', $state ?? '0'))
                            ->extraAttributes(['class' => 'text-right'])
                            ->columnSpanFull(),
                        ]),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Pending')
                            ->required()
                            ->columnSpanFull(),

                        // Repeater::make('ledgers')
                        //     ->relationship('ledgers')
                        //     ->reactive()
                        //     ->deletable(false)
                        //     ->reorderable(false)
                        //     ->addable(false)
                        //     ->schema([
                        //     TextInput::make('due_date')
                        //         ->label('Due Date')
                        //         ->disabled()
                        //         ->dehydrated(true),
                        //     Select::make('status')
                        //         ->label('Payment Status')
                        //         ->options([
                        //             'Pending' => 'Pending',
                        //             'Paid' => 'Paid',
                        //         ])
                        //         ->default('Pending')
                        //         ->required()
                        //     ])
                    ])->collapsible(fn (string $context): bool => $context === 'edit')
                    ->collapsed(fn (string $context): bool => $context === 'edit'),
            ]);
    }
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->where('status', 'Pending');
    // }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id') 
                    ->label('Loan ID')
                    ->searchable(),
                TextColumn::make('user.info.member_id')
                    ->label('Member ID')
                    ->searchable(),
                TextColumn::make('user.name') 
                    ->label('Applicant')
                    ->searchable(),
                TextColumn::make('loan_amount')
                    ->label('Requested Loan')
                    ->money('PHP'),
                TextColumn::make('interest_rate')
                    ->label('Interest Rate')
                    ->suffix('%'),
                TextColumn::make('loan_term')
                    ->label('Loan Term')
                    ->formatStateUsing(fn(?string $state): string => match($state){
                        '3' => '3 months',
                        '6' => '6 months',
                        '9' => '9 months',
                        '12' => '12 months',
                        '24' => '24 months',
                    }),
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
                // TextColumn::make('status')
                //     ->label('Status')
                //     ->formatStateUsing(fn (string $state): string => ucfirst($state))
                //     ->badge()
                //     ->color(fn (string $state): string => match($state) {
                //         'Pending' => 'warning',
                //         'Approved' => 'success',
                //         'Rejected' => 'danger',
                //         default => 'gray'
                //     }),
            ])
            ->recordAction(null)
            ->recordUrl(null)
            ->defaultSort('id', 'desc')
            ->filters([
                // 
            ])
            ->actions([
                ActionGroup::make([
                    // Tables\Actions\Action::make('approve')
                    //     ->label('Approve')
                    //     ->icon('heroicon-o-check')
                    //     ->color('success')
                    //     ->requiresConfirmation()
                    //     ->modalIcon('heroicon-o-check')
                    //     ->modalDescription('Are you sure you want to approve this application?')
                    //     ->action(function ($record) {
                        
                    //         $record->update(['status' => 'Approved']);
                    //         Mail::to($record->user->email)->send(
                    //             new LoanStatus($record, 'Approved')
                    //         );

                    //         Notification::make()
                    //             ->title('Loan approved')
                    //             ->success()
                    //             ->send();
                    //     })->visible(fn ($record) => $record->status === 'Pending'),
                    Tables\Actions\Action::make('setInterestRate')
                        ->label('Set Interest Rate')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->form([
                            TextInput::make('interest_rate')
                                ->label('Interest Rate (%)')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(100)
                                ->suffix('%'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'interest_rate' => $data['interest_rate'],
                            ]);

                            $record->recomputeLoan();

                            Notification::make()
                                ->title('Interest rate set to ' . $data['interest_rate'] . '%')
                                ->success()
                                ->send();
                        })
                        ->visible(fn ($record) => $record->status === 'Pending'),
                        Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->visible(fn ($record) => $record->status === 'Pending')
                            ->action(function ($record) {
                                if ($record->interest_rate == 0) {
                                    Notification::make()
                                        ->title('Interest Rate Required')
                                        ->body('Please set an interest rate before approving this loan.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $record->update(['status' => 'Approved',]);
                                Mail::to($record->user->email)->send(
                                 new LoanStatus($record, 'Approved')
                                );

                                Notification::make()
                                    ->title('Loan Approved')
                                    ->success()
                                    ->send();
                            })
                            ->requiresConfirmation()
                            ->color('success')
                            ->icon('heroicon-o-check-circle'),


                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-x-mark')
                        ->modalDescription('Are you sure you want to reject this application?')
                        ->action(function ($record) {
                        
                            $record->update(['status' => 'Rejected']);
                            Mail::to($record->user->email)->send(
                                new LoanStatus($record, 'Rejected')
                            );

                            Notification::make()
                                ->title('Loan rejected')
                                ->danger()
                                ->send();
                        })->visible(fn ($record) => $record->status === 'Pending'),

                    // Tables\Actions\ViewAction::make()->modalWidth('2xl'),
                    Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'Approved')
                    ->label('View Ledger')
                    ->icon('heroicon-m-eye')
                    ->visible(fn ($record) => $record->status === 'Approved'),

                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Delete Loan'),
                ])
                // ActionGroup::make([
                //     Tables\Actions\DeleteAction::make()
                //         ->modalHeading('Delete Loan')
                //         ->visible(fn ($record) => $record->status === 'Approved'),
                // ])
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
            LedgersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoansManagement::route('/'),
            // 'create' => Pages\CreateLoansManagement::route('/create'),
            'edit' => Pages\EditLoansManagement::route('/{record}/edit'),
        ];
    }
}
