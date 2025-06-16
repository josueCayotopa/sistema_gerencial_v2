<?php

namespace App\Filament\Resources\TablaVistaVentaProductosResource\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Support\Enums\MaxWidth;

class VentasPorMes extends ApexChartWidget
{
    protected static string $chartId = 'ventasPorMes';
    protected static ?string $heading = 'Ventas por Mes';
    protected static ?string $subheading = 'Total de ventas mensuales (S/) - Año 2025';
    protected static ?int $contentHeight = 400;
    protected static ?string $footer = 'Datos extraídos desde TABLA_VISTA_VENTA_PRODUCTOS';
    protected static ?string $pollingInterval = null; // No auto-refresh
    protected static bool $deferLoading = false; // Mostrar inmediatamente

    protected function getData(): array
    {
        $result = DB::table('TABLA_VISTA_VENTA_PRODUCTOS')
            ->select('MES', DB::raw('SUM(VEN_CIGV) as total'))
            ->where('PERIODO', '2025')
            ->groupBy('MES')
            ->orderBy('MES')
            ->get();

        $mesesMap = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar',
            '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Set',
            '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];

        $labels = [];
        $data = [];

        foreach ($result as $row) {
            $labels[] = $mesesMap[$row->MES] ?? $row->MES;
            $data[] = round($row->total, 2);
        }

        return [
            'chart' => [
                'type' => 'bar',
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'title' => ['text' => 'Mes'],
            ],
            'yaxis' => [
                'title' => ['text' => 'Ventas S/'],
                'labels' => ['formatter' => 'function (val) { return "S/ " + val.toLocaleString(); }'],
            ],
            'series' => [
                [
                    'name' => 'Ventas S/',
                    'data' => $data,
                ],
            ],
            'colors' => ['#B11A1A'], // Color institucional
            'dataLabels' => [
                'enabled' => true,
                'formatter' => 'function (val) { return "S/ " + val.toFixed(0); }',
            ],
        ];
    }
}
