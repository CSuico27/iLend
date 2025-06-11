<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeminarScheduleResource\Pages;
use App\Filament\Resources\SeminarScheduleResource\RelationManagers;
use App\Models\SeminarSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeminarScheduleResource extends Resource
{
    protected static ?string $model = SeminarSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?int $navigationSort = 2;

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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSeminarSchedules::route('/'),
            'create' => Pages\CreateSeminarSchedule::route('/create'),
            'edit' => Pages\EditSeminarSchedule::route('/{record}/edit'),
        ];
    }
}
