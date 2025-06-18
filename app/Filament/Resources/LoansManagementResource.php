<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoansManagementResource\Pages;
use App\Filament\Resources\LoansManagementResource\RelationManagers\LedgersRelationManager;
use App\Models\Loan;
use Carbon\Carbon;
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

class LoansManagementResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $modelLabel = 'Application';
    protected static ?string $pluralModelLabel = 'Loan Applicants'; 
    protected static ?string $navigationLabel = 'Loan Application';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'Loans Management';
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

        $data['end_date'] = $start->copy()->addMonths($loanTerm)->toDateString();

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
        $data['remaining_balance'] = (float) preg_replace('/[^\d.]/', '', $data['total_payment'] ?? '0');

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
                                ->whereHas('info', fn ($q) => $q->where('status', 'Approved')) 
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
                            ->label('Interest Amount')
                            ->disabled()
                            ->reactive()
                            ->prefix('₱') 
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => (float) preg_replace('/[^\d.]/', '', $state ?? '0'))
                            ->extraAttributes(attributes: ['class' => 'text-right']), 

                        TextInput::make('total_payment')
                            ->label('Total Payment')
                            ->disabled()  
                            ->reactive()
                            ->prefix('₱')
                            ->dehydrated(true)
                            ->dehydrateStateUsing(fn ($state) => (float) preg_replace('/[^\d.]/', '', $state ?? '0'))
                            ->extraAttributes(['class' => 'text-right']),

                        TextInput::make('payment_per_term')
                            ->label('Payment Amount Per Term')
                            ->disabled() 
                            ->reactive()
                            ->dehydrated(true)
                            ->prefix('₱')
                            ->dehydrateStateUsing(fn ($state) => (float) preg_replace('/[^\d.]/', '', $state ?? '0'))
                            ->extraAttributes(['class' => 'text-right']),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Pending')
                            ->required(),
                        ]),
                    Repeater::make('ledgers')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('amount_paid')
                                    ->label('Amount Paid')
                                    ->numeric()
                                    ->disabled()
                                    ->prefix('₱')
                                    ->default(0),

                                TextInput::make('remaining_balance')
                                    ->label('Remaining Balance')
                                    ->numeric()
                                    ->disabled()
                                    ->reactive()
                                    ->prefix('₱')
                            ]),
                            Select::make('ledgers.status')
                                ->label('Status')
                                ->options([
                                    'Pending' => 'Pending',
                                    'Paid' => 'Paid',
                                ])
                                ->default('Pending')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ]),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'Pending');
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name') 
                    ->label('Applicant'),
                TextColumn::make('loan_amount')
                    ->label('Requested Loan')
                    ->money('PHP'),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray'
                    }),
            ])
            ->recordAction(null)
            ->filters([
                // 
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check')
                        ->modalDescription('Are you sure you want to approve this application?')
                        ->action(function ($record) {
                        
                            $record->update(['status' => 'Approved']);

                            Notification::make()
                                ->title('Loan approved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-x-mark')
                        ->modalDescription('Are you sure you want to reject this application?')
                        ->action(function ($record) {
                        
                            $record->update(['status' => 'Rejected']);

                            Notification::make()
                                ->title('Loan rejected')
                                ->danger()
                                ->send();
                        }),

                    // Tables\Actions\ViewAction::make()->modalWidth('2xl'),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListLoansManagement::route('/'),
            // 'create' => Pages\CreateLoansManagement::route('/create'),
            // 'edit' => Pages\EditLoansManagement::route('/{record}/edit'),
        ];
    }
}
