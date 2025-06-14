<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipManagementResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MembershipManagementResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $pluralModelLabel = 'Membership Requests'; 
    protected static ?string $navigationLabel = 'Membership Management';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('info', function ($query) {
            $query->where('status', 'pending');
        })->count();
    }
    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'warning';
    }
    protected static ?string $navigationBadgeTooltip = 'Pending Members';
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Membership Form')
                    ->tabs([
                        Tab::make('Account Info')
                            ->schema([
                                TextInput::make('name')->label('Full Name')->required(),
                                TextInput::make('email')->email()->required(),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                                    ->required(fn (string $context) => $context === 'create'),
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

                                                        TextInput::make('address')
                                                            ->label('Address')
                                                            ->required(),
                                                    ]),

                                                Tab::make('Requirements')
                                                    ->icon('heroicon-o-identification')
                                                    ->schema([
                                                        FileUpload::make('picture')
                                                            ->label('2x2 Picture')
                                                            ->image()
                                                            ->directory('user-picture')
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
                    ->whereHas('info', fn ($q) => $q->where('status', 'Pending')); 
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
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray'
                    }),
                TextColumn::make('created_at')
                    ->label('Date Applied') 
                    ->date('F j, Y'),
            ])
            ->recordAction(null)
            ->filters([
                
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (User $record) => $record->info?->status === 'Pending')
                        ->action(function (User $record) {
                            $record->info?->update(['status' => 'Approved']);

                            \Filament\Notifications\Notification::make()
                                ->title('Member approved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (User $record) => $record->info?->status === 'Pending')
                        ->action(function (User $record) {
                            $record->info?->update(['status' => 'Rejected']);
                            \Filament\Notifications\Notification::make()
                                ->title('Member rejected')
                                ->danger()
                                ->send();
                        }),
                    Tables\Actions\ViewAction::make()->modalWidth('2xl'),
                    Tables\Actions\DeleteAction::make(),
                ])
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
