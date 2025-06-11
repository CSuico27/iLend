<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoansManagementResource\Pages;
use App\Filament\Resources\LoansManagementResource\RelationManagers;
use App\Models\LoansManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoansManagementResource extends Resource
{
    protected static ?string $model = LoansManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?int $navigationSort = 3;

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
            'index' => Pages\ListLoansManagement::route('/'),
            'create' => Pages\CreateLoansManagement::route('/create'),
            'edit' => Pages\EditLoansManagement::route('/{record}/edit'),
        ];
    }
}
