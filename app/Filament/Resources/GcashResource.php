<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GcashResource\Pages;
use App\Filament\Resources\GcashResource\RelationManagers;
use App\Models\Gcash;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GcashResource extends Resource
{
    protected static ?string $model = Gcash::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'GCash QR';
    protected static ?string $pluralModelLabel = 'GCash QR';
     protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('qr_path')
                ->label('GCash QR')
                ->directory('gcash-qrs')
                ->image()
                ->imagePreviewHeight('250')
                ->required()
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                ImageColumn::make('qr_path')
                ->label('GCash QR')
                ->getStateUsing(fn ($record) => $record->qr_path ? asset('storage/' . $record->qr_path) : null)
                ->height(100)
                ->width(100)
                ->square(),
            ])
             ->recordAction(null)
             
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
            'index' => Pages\ListGcashes::route('/'),
            // 'create' => Pages\CreateGcash::route('/create'),
            // 'edit' => Pages\EditGcash::route('/{record}/edit'),
        ];
    }
}
