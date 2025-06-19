<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\SeminarSchedule;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SeminarScheduleResource\Pages;
use App\Mail\SeminarCreatedNotification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class SeminarScheduleResource extends Resource
{
    protected static ?string $model = SeminarSchedule::class;
    protected static ?string $modelLabel = 'Seminar Schedule';
    protected static ?string $pluralModelLabel = 'Seminars';
    protected static ?string $navigationLabel = 'Seminar Schedule';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('title')
                    ->label('Seminar Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                DatePicker::make('seminar_date')
                    ->label('Seminar Date')
                    ->placeholder('Select a Seminar Date')
                    ->minDate(today())
                    ->displayFormat('m/d/Y')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->suffixIcon('heroicon-o-calendar')
                    ->columnSpanFull(),

                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->required()
                    ->native(false)
                    ->suffixIcon('heroicon-o-clock')
                    ->seconds(false),

                TimePicker::make('end_time')
                    ->label('End Time')
                    ->required()
                    ->native(false)
                    ->suffixIcon('heroicon-o-clock')
                    ->seconds(false),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Scheduled' => 'Scheduled',
                        'Completed' => 'Completed',
                    ])
                    ->default('Scheduled')
                    ->required()
                    ->columnSpanFull(),

                Select::make('user_ids')
                    ->label('Attendees')
                    ->options(User::where('role', '!=', 'admin')
                        ->whereHas('info', fn ($q) => $q->where('status', 'Approved'))
                        ->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('details')
                    ->label('Description')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('seminar_date')
                    ->label('Date')
                    ->date('F d, Y'),
                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('h:i A')),
                TextColumn::make('end_time')
                    ->label('End Time')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('h:i A')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Scheduled' => 'warning',
                        'Completed' => 'success',
                        default => 'gray'
                    })
                    ->searchable(),
                ImageColumn::make('user_avatars')
                    ->label('Attendees')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->getStateUsing(function ($record) {
                        if (!$record->user_ids) return [];
                        
                        return User::whereIn('id', $record->user_ids)
                            ->pluck('avatar') 
                            ->filter() 
                            ->take(5) 
                            ->toArray();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction(null)
            ->filters([])
            ->actions([
                Action::make('viewParticipants')
                    ->label('Attendees')
                    ->button()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn(SeminarSchedule $record): Infolist =>
                        Infolist::make()
                            ->record($record) 
                            ->schema([
                            Section::make()
                                ->schema([
                                    \Filament\Infolists\Components\Grid::make(1)
                                        ->schema(function (SeminarSchedule $record) {
                                            return collect($record->assigned_users_names ?? [])
                                                ->map(fn($name) => TextEntry::make($name)->default($name)->label(''))
                                                ->all();
                                        }),
                                ]),
                        ])
                    
                    ),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSeminarSchedules::route('/'),
            //'create' => Pages\CreateSeminarSchedule::route('/create'),
            // 'edit' => Pages\EditSeminarSchedule::route('/{record}/edit'),
        ];
    }
}
