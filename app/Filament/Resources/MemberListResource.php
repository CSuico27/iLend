<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberListResource\Pages;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Support\RawJs;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;

class MemberListResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $pluralModelLabel = 'Approved Members';
    protected static ?string $navigationLabel = 'Member List';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('info', function ($query) {
                $query->where('status', 'approved');
            });
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('info.member_id')->label('Member ID')->searchable(),
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('info.approved_at')->label('Date Approved')->date('M j, Y'),
                TextColumn::make('membershipDuration')
                    ->badge()
                    ->label('Membership Duration')
                    ->getStateUsing(function ($record) {
                        if (!$record->info?->approved_at) return null;

                        $approvedAt = Carbon::parse($record->info->approved_at);
                        $diff = $approvedAt->diff(now());

                        $years = $diff->y;
                        $months = $diff->m;

                        if ($years === 0 && $months === 0) {
                            return 'Less than a month';
                        }

                        $text = [];

                        if ($years > 0) {
                            $text[] = $years . ' year' . ($years > 1 ? 's' : '');
                        }

                        if ($months > 0) {
                            $text[] = $months . ' month' . ($months > 1 ? 's' : '');
                        }

                        return implode(' and ', $text);
                    })
                    ->color('success'),
                TextColumn::make('actions_header')
                    ->label('Actions')
                    ->getStateUsing(fn() => null)
                    ->sortable(false)
                    ->searchable(false),
            ])
            ->filters([
                
                Tables\Filters\Filter::make('recent')
                    ->label('Approved in last 30 days')
                    ->query(fn ($query) => $query->whereHas('info', function ($q) {
                        $q->where('approved_at', '>=', now()->subDays(30));
                    })),
            
                Tables\Filters\SelectFilter::make('membershipDuration')
                    ->label('Membership Duration')
                    ->options([
                        'less_than_month' => 'Less than 1 Month',
                        '1_12_months' => '1–12 Months',
                        '1_5_years' => '1–5 Years',
                        '5_plus_years' => '5+ Years',
                    ])
                    ->query(function ($query, array $data) {
                        return $query->whereHas('info', function ($q) use ($data) {
                            if ($data['value'] === 'less_than_month') {
                                $q->where('approved_at', '>=', now()->subMonth());
                            } elseif ($data['value'] === '1_12_months') {
                                $q->whereBetween('approved_at', [now()->subYear(), now()->subMonth()]);
                            } elseif ($data['value'] === '1_5_years') {
                                $q->whereBetween('approved_at', [now()->subYears(5), now()->subYear()]);
                            } elseif ($data['value'] === '5_plus_years') {
                                $q->where('approved_at', '<=', now()->subYears(5));
                            }
                        });
                    }),
            ])
            
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('View')
                        ->modalWidth('2xl')
                        ->record(fn (User $record) => $record->load('info'))
                        ->form([
                            TextInput::make('name')
                                        ->label(false)
                                        ->required(),
                            Tabs::make('Profile Tabs')->tabs([
                                Tab::make('Member Info')->schema([
                                    Grid::make('Member Information')
                                        ->relationship('info')
                                        ->schema([
                                            Tabs::make('Info Tabs')->tabs([
                                                Tab::make('Details')->schema([
                                                    Grid::make(2)->schema([
                                                        TextInput::make('phone')
                                                            ->label('Phone')
                                                            ->disabled(),

                                                        DatePicker::make('birthdate')
                                                            ->label('Birthdate')
                                                            ->disabled(),
                                                    ]),
                                                    Select::make('gender')
                                                        ->label('Gender')
                                                        ->options([
                                                            'Male' => 'Male',
                                                            'Female' => 'Female',
                                                            'Not Specified' => 'Not Specified',
                                                        ])
                                                        ->disabled(),
                                                    Select::make('marital_status')
                                                        ->label('Marital Status')
                                                        ->options([
                                                            'Single' => 'Single',
                                                            'Married' => 'Married',
                                                            'Divorced' => 'Divorced',
                                                            'Widowed' => 'Widowed',
                                                        ])
                                                        ->disabled(),
                                                    TextInput::make('region')
                                                        ->label('Region')
                                                        ->disabled(),
                                                    TextInput::make('province')
                                                        ->label('Province')
                                                        ->disabled(),
                                                    TextInput::make('municipality')
                                                        ->label('Municipality')
                                                        ->disabled(),
                                                    TextInput::make('barangay')
                                                        ->label('Barangay')
                                                        ->disabled(),
                                                ]),

                                                Tab::make('Requirements Submitted')->schema([
                                                    FileUpload::make('biodata')
                                                        ->label('Biodata')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory('user-biodata')
                                                        ->openable()
                                                        ->disabled(),

                                                    FileUpload::make('brgy_clearance')
                                                        ->label('Barangay Clearance')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory('brgy-clearance')
                                                        ->openable()
                                                        ->disabled(),

                                                    FileUpload::make('valid_id')
                                                        ->label('Valid ID')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory('valid-ids')
                                                        ->openable()
                                                        ->disabled(),

                                                    TextInput::make('tin_number')
                                                        ->label('TIN Number')
                                                        ->disabled()
                                                        ->formatStateUsing(function (?string $state): ?string {
                                                            if ($state && strlen($state) === 14 && ctype_digit($state)) {
                                                                return substr($state, 0, 3) . '-' .
                                                                    substr($state, 3, 3) . '-' .
                                                                    substr($state, 6, 3) . '-' .
                                                                    substr($state, 9, 5);
                                                            }
                                                            return $state;
                                                        }),
                                                ]),
                                            ]),
                                        ])
                                        ->columnSpanFull(),
                                ]),
                                Tab::make('Loan History')->schema([
                                    View::make('livewire.pages.loan-history')
                                        ->viewData(fn ($record) => [
                                            'loans' => $record->loans()->orderBy('created_at', 'desc')->get(),
                                        ]),
                            ]),
                            // Tab::make('Credit Score')->schema([
                            //     View::make('filament.widgets.credit-score-chart')
                            //         ->viewData(fn ($record) => [
                            //             'creditScore' => $record->creditScore?->score ?? 0,
                            //         ]),
                            // ]),

                            ]),
                        ]),
                    Tables\Actions\ViewAction::make('viewCreditScore')
                        ->label('View Credit Score')
                        ->icon('heroicon-o-chart-pie')
                        ->modalHeading('Credit Score')
                        ->modalWidth('2xl')
                        ->modalContent(fn ($record) => view('filament.custom.credit-score-chart', [
                            'creditScore' => $record->creditScore?->score ?? 0,
                        ])),
                    Tables\Actions\Action::make('changeApprovedDate')
                        ->label('Change Duration')
                        ->icon('heroicon-o-calendar')
                        ->form([
                            DatePicker::make('approved_at')
                                ->label('Approved At')
                                ->required(),
                        ])
                        ->fillForm(fn (User $record): array => [
                            'approved_at' => $record->info?->approved_at,
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->info->update([
                                'approved_at' => $data['approved_at'],
                            ]);
                        })
                        ->modalHeading('Update Approved Date')
                        ->modalButton('Save Date'),
                    Tables\Actions\Action::make('calculateDividend')
                        ->label('Calculate Dividend')
                        ->icon('heroicon-o-calculator')
                        ->form([
                            TextInput::make('total_share')
                                ->label('Total Share Capital per Month (Sum of Ending Balances)')
                                ->numeric()
                                ->prefix('₱')
                                ->required()
                                ->helperText('Maximum allowed amount is ₱100,000.00')
                                ->mask(RawJs::make(<<<'JS'
                                    ($input) => {
                                        const digits = $input.replace(/[^\d]/g, '');

                                        if (!digits) return '';

                                        const number = parseFloat(digits) / 100;

                                        if (number > 100000 ) return '0.00';

                                        return number.toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }
                                JS))
                                ->stripCharacters([','])
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    // Remove commas and convert to float
                                    $total = floatval(str_replace(',', '', $state ?? 0));
                                    $average = $total / 12;
                                    $rate = floatval($get('interest_rate') ?? 0) / 100;
                                    $dividend = $average * $rate;
                                    
                                    $set('total_average_share', number_format($average, 2, '.', ''));
                                    $set('dividend_amount', number_format($dividend, 2, '.', ''));
                                }),
                            
                            TextInput::make('total_average_share')
                                ->label('Total Average Share')
                                ->numeric()
                                ->prefix('₱')
                                ->disabled()
                                ->dehydrated()
                                ->inputMode('decimal'),
                            
                            TextInput::make('interest_rate')
                                ->label('Interest Rate')
                                ->numeric()
                                ->prefix('%')
                                ->disabled()
                                ->dehydrated()
                                ->live()
                                ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                    $total = floatval($get('total_share') ?? 0);
                                    $average = $total / 12;
                                    $rate = floatval($state ?? 0) / 100;
                                    $dividend = $average * $rate;
                                    
                                    $set('total_average_share', round($average, 2));
                                    $set('average_share_months', round($average, 2));
                                    $set('dividend_amount', round($dividend, 2));
                                }),
                            
                            TextInput::make('dividend_amount')
                                ->label('Computed Dividend')
                                ->numeric()
                                ->prefix('%')
                                ->disabled()
                                ->dehydrated()
                                ->inputMode('decimal'),
                        ])
                        ->fillForm(function (User $record): array {
                            $interestRate = \App\Models\Interest::orderByDesc('created_at')->value('interest_rate') ?? 0;
                            
                            $existingDividend = $record->dividends()->latest()->first();
                            
                            return [
                                'total_share' => $existingDividend?->total_share ?? 0,
                                'total_average_share' => $existingDividend ? ($existingDividend->total_share / 12) : 0,
                                'interest_rate' => $interestRate,
                                'dividend_amount' => $existingDividend?->dividend_amount ?? 0,
                            ];
                        })
                        ->modalWidth('lg')
                        ->modalHeading(fn (User $record) => "Calculate Dividend for {$record->name}")
                        ->modalSubmitActionLabel('Save Dividend')
                        ->action(function (User $record, array $data): void {
                            $record->dividends()->updateOrCreate(
                                ['user_id' => $record->id],
                                [
                                    'total_share' => $data['total_share'],
                                    'total_average_share' => $data['total_average_share'],
                                    'dividend_amount' => $data['dividend_amount'],
                                ]
                            );
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Dividend Saved!')
                                ->body("Total dividend of " . number_format($data['dividend_amount'], 2) . "%" . " has been saved for {$record->name}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->extraAttributes([
                    'class' => '-ml-10',
                    'style' => 'margin-left:-70px;',
                ]),
            ])
            ->recordAction(null)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberLists::route('/'),
        ];
    }
}