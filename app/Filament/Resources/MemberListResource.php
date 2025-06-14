<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberListResource\Pages;
use App\Filament\Resources\MemberListResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section; // Added for wrapping relationship

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
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ])
            ->filters([
                //
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
                            Grid::make('Member Information') 
                                ->relationship('info') 
                                ->schema([
                                    Tabs::make('Member Info')->tabs([
                                        Tab::make('Details')
                                            ->schema([
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
                                                TextInput::make('address') 
                                                    ->label('Address')
                                                    ->disabled(),
                                            ]),
                                        Tab::make('Requirements Submitted')
                                            ->schema([
                                                FileUpload::make('picture') 
                                                    ->label('2x2 Picture')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('user-picture')
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
                                            ]),
                                    ]),
                                ])
                                ->columnSpanFull(), 
                        ]),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])->recordAction(null)
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
            'index' => Pages\ListMemberLists::route('/'),
        ];
    }
}