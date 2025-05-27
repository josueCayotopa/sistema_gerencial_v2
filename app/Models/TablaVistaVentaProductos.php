<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TablaVistaVentaProductos extends Model
{
    use HasFactory;

    protected $table = 'db0.TABLA_VISTA_VENTA_PRODUCTOS';
    protected $primaryKey = 'OPERACION';
    public $incrementing = false;
    public $timestamps = false;
    
    // Solo los campos realmente necesarios
    protected $fillable = [
        'CLIENTE',
        'DOC_CLIENTE',
        'CLIENTE_EMAIL',
        'CLIENTE_TELEFONO',
        'SUCURSAL',
        'FEC_EMISION',
        'VEN_CIGV',
        'CANTIDAD',
        'PRODUCTO'
    ];
    
    protected $dates = ['FEC_EMISION'];
    
    // Scope para datos de clientes
    public function scopeClientes($query)
    {
        return $query->select([
                'CLIENTE',
                'DOC_CLIENTE',
                'CLIENTE_EMAIL',
                'CLIENTE_TELEFONO',
                'SUCURSAL',
                'FEC_EMISION',
                DB::raw('COUNT(DISTINCT OPERACION) as total_compras'),
                DB::raw('SUM(VEN_CIGV) as monto_total'),
                DB::raw('SUM(CANTIDAD) as productos_totales')
            ])
            ->groupBy('CLIENTE', 'DOC_CLIENTE', 'CLIENTE_EMAIL', 'CLIENTE_TELEFONO', 'SUCURSAL', 'FEC_EMISION');
    }
    
    // Scope para búsqueda eficiente
    public function scopeBuscarCliente($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('CLIENTE', 'like', "%{$search}%")
              ->orWhere('DOC_CLIENTE', 'like', "%{$search}%")
              ->orWhere('CLIENTE_EMAIL', 'like', "%{$search}%");
        });
    }
}