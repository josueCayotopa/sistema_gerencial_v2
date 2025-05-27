<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaProducto extends Model
{
    use HasFactory;
    protected $table = 'db0.TABLA_VISTA_VENTA_PRODUCTOS';
    protected $primaryKey = ['DOCUMENTO', 'CODIGO']; // Clave compuesta
    
    // Campos date para optimizar búsquedas por fecha
    protected $dates = ['FEC_EMISION', 'FEC_ATENCION', 'FEC_ACTUALIZA'];
    
    // Scope para filtrar últimos 3 años
    public function scopeUltimosTresAnios($query)
    {
        return $query->where('FEC_EMISION', '>=', now()->subYears(3));
    }
    
    // Scope para datos resumidos (evita seleccionar todas las columnas)
    public function scopeResumen($query)
    {
        return $query->select([
            'PERIODO', 
            
            'MES', 
            'GRUPO', 
            'SUBGRUPO', 
            'CODIGO', 
            'PRODUCTO',
             'PRECIOPROM', 
            'CANTIDAD', 
            'VEN_CIGV', 
            'STOCK',
            'FEC_EMISION',
            'SUCURSAL', 
            'ESPECIALIDAD'
        ]);
    }
}
