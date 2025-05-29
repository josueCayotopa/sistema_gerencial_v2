<?php

namespace App\Filament\Resources\TablaVistaVentaProductosResource\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VentasPorMesSegmentado extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Mes (Segmentado)';
    protected static ?string $maxHeight = '350px';

    // Parámetros del formulario del widget
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('anio')->default(2025)->required()->numeric(),
            TextInput::make('empresa')->default('CLINICA LA LUZ SAC')->required(),
            TextInput::make('sucursal')->default('JULIACA')->required(),
            TextInput::make('subgrupo')->default('CONSULTAS')->required(),
        ];
    }

    protected function getData(): array
    {
        $data = $this->form->getState();

        $anio = $data['anio'];
        $empresa = $data['empresa'];
        $sucursal = $data['sucursal'];
        $subgrupo = $data['subgrupo'];

        $resultados = DB::select('EXEC sp_kpi_ventas_por_mes ?, ?, ?, ?', [
            $anio,
            $empresa,
            $sucursal,
            $subgrupo,
        ]);

        $meses = [];
        $ventas = [];

        foreach ($resultados as $row) {
            $meses[] = str_pad($row->MES, 2, '0', STR_PAD_LEFT);
            $ventas[] = (float) $row->cantidad_ventas;
        }

        return [
            'labels' => $meses,
            'datasets' => [
                [
                    'label' => 'Cantidad de Ventas',
                    'data' => $ventas,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Puede ser 'line', 'pie', etc.
    }
}
