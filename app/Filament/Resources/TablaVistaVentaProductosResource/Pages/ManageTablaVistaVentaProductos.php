<?php

namespace App\Filament\Resources\TablaVistaVentaProductosResource\Pages;

use App\Filament\Resources\TablaVistaVentaProductosResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTablaVistaVentaProductos extends ManageRecords
{
    protected static string $resource = TablaVistaVentaProductosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
