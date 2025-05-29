<?php

namespace App\Filament\Resources\TablaVistaVentaProductosResource\Widgets;

use Filament\Widgets\ChartWidget;

class VentasPorMes extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
