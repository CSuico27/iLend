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
            ->filters([])
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