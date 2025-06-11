<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditScoreResource\Pages;
use App\Filament\Resources\CreditScoreResource\RelationManagers;
use App\Models\CreditScore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditScoreResource extends Resource
{
    protected static ?string $model = CreditScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?int $navigationSort = 6;

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
            'index' => Pages\ListCreditScores::route('/'),
            'create' => Pages\CreateCreditScore::route('/create'),
            'edit' => Pages\EditCreditScore::route('/{record}/edit'),
        ];
    }
}
