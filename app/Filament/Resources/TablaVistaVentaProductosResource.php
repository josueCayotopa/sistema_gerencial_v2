<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TablaVistaVentaProductosResource\Pages;
use App\Filament\Resources\TablaVistaVentaProductosResource\RelationManagers;
use App\Models\TablaVistaVentaProductos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TablaVistaVentaProductosResource extends Resource
{
    protected static ?string $model = TablaVistaVentaProductos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
protected function getTableRecordsPerPageSelectOptions(): array
{
    return [10, 25, 50, 100];
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTablaVistaVentaProductos::route('/'),
        ];
    }
}
