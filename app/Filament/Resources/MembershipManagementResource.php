<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipManagementResource\Pages;
use App\Mail\MemberStatusNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MembershipManagementResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $pluralModelLabel = 'Membership Requests';
    protected static ?string $navigationLabel = 'Membership Management';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('info', function ($query) {
            $query->where('status', 'pending')
                ->where('is_applied_for_membership', 1);
        })->count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'warning';
    }

    protected static ?string $navigationBadgeTooltip = 'Pending Members';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Membership Form')
                    ->tabs([
                        Tab::make('Account Info')
                            ->schema([
                                TextInput::make('name')->label('Full Name')->required(),
                                TextInput::make('email')->email()->required()->unique(),
                                // TextInput::make('password')
                                //     ->password()
                                //     ->revealable()
                                //     ->dehydrateStateUsing(fn($state) => $state ? bcrypt($state) : null)
                                //     ->required(fn(string $context) => $context === 'create'),
                            ]),
                        Tab::make('Member Info')
                            ->schema([
                                Grid::make()
                                    ->relationship('info')
                                    ->schema([
                                        Tabs::make('Member Details')
                                            ->tabs([
                                                Tab::make('Details')
                                                    ->icon('heroicon-o-user')
                                                    ->schema([
                                                        TextInput::make('member_id')
                                                            ->label('Member ID')
                                                            ->default(function () {
                                                                $nextId = User::max('id') + 1;
                                                                return str_pad($nextId, 5, '0', STR_PAD_LEFT);
                                                            })
                                                            ->disabled()
                                                            ->dehydrated()
                                                            ->required(),
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextInput::make('phone')
                                                                    ->label('Phone')
                                                                    ->tel()
                                                                    ->mask('+63-999-999-9999')
                                                                    ->placeholder('+63-917-1234-5678')
                                                                    ->required(),
                                                                DatePicker::make('birthdate')
                                                                    ->label('Birthdate')
                                                                    ->required()
                                                                    ->displayFormat('m/d/Y')
                                                                    ->native(false)
                                                                    ->closeOnDateSelection(),
                                                            ]),
                                                        Select::make('gender')
                                                            ->label('Gender')
                                                            ->options([
                                                                'Male' => 'Male',
                                                                'Female' => 'Female',
                                                                'Not Specified' => 'Not Specified',
                                                            ])
                                                            ->required(),
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
                                                            ->required(),
                                                        TextInput::make('province')
                                                            ->label('Province')
                                                            ->required(),
                                                        TextInput::make('municipality')
                                                            ->label('Municipality')
                                                            ->required(),
                                                        TextInput::make('barangay')
                                                            ->label('Barangay')
                                                            ->required(),
                                                    ]),
                                                Tab::make('Requirements')
                                                    ->icon('heroicon-o-identification')
                                                    ->schema([
                                                        FileUpload::make('biodata')
                                                            ->label('Biodata')
                                                            ->image()
                                                            ->directory('user-biodata')
                                                            ->preserveFilenames()
                                                            ->disk('public')
                                                            ->imageCropAspectRatio('1:1')
                                                            ->openable()
                                                            ->required(),
                                                        FileUpload::make('brgy_clearance')
                                                            ->label('Barangay Clearance')
                                                            ->image()
                                                            ->directory('brgy-clearance')
                                                            ->preserveFilenames()
                                                            ->disk('public')
                                                            ->imageCropAspectRatio('4:3')
                                                            ->openable()
                                                            ->required(),
                                                        FileUpload::make('valid_id')
                                                            ->label('Valid ID')
                                                            ->image()
                                                            ->directory('valid-ids')
                                                            ->preserveFilenames()
                                                            ->disk('public')
                                                            ->imageCropAspectRatio('4:3')
                                                            ->openable()
                                                            ->required(),
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
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->where('role', 'user')
                    ->whereHas('info', fn($q) => $q
                        ->where('status', 'Pending')
                        ->where('is_applied_for_membership', 1)
                    );
            })
            ->columns([
                TextColumn::make('info.member_id')
                    ->label('Member ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('info.status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray'
                    }),
                TextColumn::make('created_at')
                    ->label('Date Applied')
                    ->date('F j, Y'),
                TextColumn::make('actions_header')
                    ->label('Actions')
                    ->getStateUsing(fn() => null)
                    ->sortable(false)
                    ->searchable(false),
            ])
            ->recordAction(null)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->modalWidth('2xl')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->extraModalFooterActions(fn(User $record): array => [
                             Tables\Actions\Action::make('close')
                                ->label('Close')
                                ->color('gray')
                                ->after(fn () => redirect()->route('filament.admin.resources.membership-managements.index')),
                            Tables\Actions\Action::make('approve')
                                ->label('Approve')
                                ->icon('heroicon-o-check')
                                ->color('success')
                                ->extraAttributes(['class' => 'ml-auto'])
                                ->visible(fn() => $record->info?->status === 'Pending')
                                ->action(function (User $record) {
                                    $record->info->update([
                                        'status' => 'Approved',
                                        'approved_at' => now()
                                    ]);
                                    Mail::to($record->email)->send(
                                        new MemberStatusNotification($record, 'Approved')
                                    );
                                    Notification::make()
                                        ->title('Membership application approved')
                                        ->success()
                                        ->send();
                                })
                                ->after(fn () => redirect()->route('filament.admin.resources.membership-managements.index')),

                            Tables\Actions\Action::make('reject')
                                ->label('Reject')
                                ->icon('heroicon-o-x-mark')
                                ->color('danger')
                                ->form([
                                    Textarea::make('reason')
                                        ->label('Reason for rejection')
                                        ->placeholder('Please provide a reason for rejecting this membership application...')
                                        ->rows(4)
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                                ->modalHeading('Reject Membership Application')
                                ->modalDescription('This action will reject the membership application and notify the user via email.')
                                ->modalIcon('heroicon-o-exclamation-triangle')
                                ->modalIconColor('danger')
                                ->modalWidth('md')
                                ->modalSubmitActionLabel('Reject Application')
                                ->modalCancelActionLabel('Cancel')
                                ->visible(fn() => $record->info?->status === 'Pending')
                                ->requiresConfirmation()
                                ->action(function (array $data, User $record) {
                                    $reason = $data['reason'];
                                    $info = $record->info;

                                    if ($info?->biodata) {
                                        Storage::disk('public')->delete($info->biodata);
                                    }
                                    if ($info?->brgy_clearance) {
                                        Storage::disk('public')->delete($info->brgy_clearance);
                                    }
                                    if ($info?->valid_id) {
                                        Storage::disk('public')->delete($info->valid_id);
                                    }

                                    $info?->update([
                                        'phone' => null,
                                        'birthdate' => null,
                                        'gender' => null,
                                        'region' => null,
                                        'province' => null,
                                        'municipality' => null,
                                        'barangay' => null,
                                        'biodata' => null,
                                        'brgy_clearance' => null,
                                        'valid_id' => null,
                                        'tin_number' => null,
                                        'approved_at' => null,
                                        'is_applied_for_membership' => 0,
                                        'status' => 'Pending',
                                    ]);

                                    Mail::to($record->email)->send(
                                        new MemberStatusNotification($record, 'Rejected', $reason)
                                    );

                                    Notification::make()
                                        ->title('Membership application rejected')
                                        ->danger()
                                        ->send();
                                })
                                ->after(fn () => redirect()->route('filament.admin.resources.membership-managements.index')),
                        ]),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->extraAttributes([
                    'class' => '-ml-10',
                    'style' => 'margin-left:-78px;',
                ]),
            ])
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
            'index' => Pages\ListMembershipManagement::route('/'),
        ];
    }
}
