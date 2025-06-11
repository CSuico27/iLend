<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenerateReportsResource\Pages;
use App\Filament\Resources\GenerateReportsResource\RelationManagers;
use App\Models\GenerateReports;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GenerateReportsResource extends Resource
{
    protected static ?string $model = GenerateReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 4;

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
            'index' => Pages\ListGenerateReports::route('/'),
            'create' => Pages\CreateGenerateReports::route('/create'),
            'edit' => Pages\EditGenerateReports::route('/{record}/edit'),
        ];
    }
}
