<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TablaVistaVentaProductosResource\Pages;
use App\Models\TablaVistaVentaProductos;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Filters\SelectFilter;

class TablaVistaVentaProductosResource extends Resource
{
    protected static ?string $model = TablaVistaVentaProductos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Sin formulario por ahora
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('CLIENTE')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('RAZON_SOCIAL'),
                Tables\Columns\TextColumn::make('GRUPO')
                    ->label('Grupo')
                    ->sortable()
                    ->searchable(),
                    TextColumn::make('PRODUCTO')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOC_CLIENTE')
                    ->label('Documento Cliente')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('RAZON_SOCIAL')
                    ->label('Razon Social')
                    ->query(function (Builder $query, array $data) {
                        return $query->where('RAZON_SOCIAL', $data['value']);
                    })
                    ->options(TablaVistaVentaProductos::pluck('RAZON_SOCIAL', 'RAZON_SOCIAL')->toArray())
                    ->searchable()
                    ->default('CLINICA LA LUZ SAC'),
                Tables\Filters\SelectFilter::make('TIP_ESTADO')
                    ->label('Estado')
                    ->query(function (Builder $query, array $data) {
                        return $query->where('TIP_ESTADO', $data['value']);
                    })
                    ->options([
                        'NN' => 'Vigente',
                        'AN' => 'Anulado',
                    ])
                    ->default('NN'),
                SelectFilter::make('GRUPO')
                    ->label('Grupo')
                    ->options([
                        'FARMACIA' => 'FARMACIA',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->where('GRUPO', $data['value']);
                    })
                    ->default('FARMACIA'),
                SelectFilter::make('PERIODO')
                    ->label('Periodo')
                    ->options([
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                    ])
                    ->default('2025')
                    ->query(function (Builder $query, array $data) {
                        return $query->where('PERIODO', $data['value']);
                    }),

                SelectFilter::make('MES')
                    ->label('Mes')
                    ->options([
                        '01' => 'Enero',
                        '02' => 'Febrero',
                        '03' => 'Marzo',
                        '04' => 'Abril',
                        '05' => 'Mayo',
                        '06' => 'Junio',
                        '07' => 'Julio',
                        '08' => 'Agosto',
                        '09' => 'Setiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre',
                    ])
                    ->default('01')
                    ->query(function (Builder $query, array $data) {
                        return $query->where('MES', $data['value']);
                    }),


            ])
            ->actions([
                Tables\Actions\EditAction::make(), // opcional
                Tables\Actions\DeleteAction::make(), // opcional
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

   

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTablaVistaVentaProductos::route('/'),
        ];
    }
}
