<?php

namespace App\Filament\Resources\TablaVistaVentaProductosResource\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TablaVistaVentaProductos extends ApexChartWidget
{
    
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'tablaVistaVentaProductos';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'TablaVistaVentaProductos';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'TablaVistaVentaProductos',
                    'data' => [2, 4, 6, 10, 14, 7, 2, 9, 10, 15, 13, 18],
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }
}
