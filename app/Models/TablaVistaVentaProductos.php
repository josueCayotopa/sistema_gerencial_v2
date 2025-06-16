<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TablaVistaVentaProductos extends Model
{
    use HasFactory;

    protected $table = 'TABLA_VISTA_VENTA_PRODUCTOS';
    protected $primaryKey = 'OPERACION';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'PERIODO',
        'MES',
        'RAZON_SOCIAL',
        'GRUPO',
        'SUBGRUPO',
        'COD_FAMILIAP',
        'DES_FAMILIA',
        'COD_FAMILIA',
        'DES_SUBFAMILIA',
        'CODIGO',
        'PRODUCTO',
        'PRECIOPROM',
        'CANTIDAD',
        'VEN_CIGV',
        'DOCUMENTO',
        'COD_DOCUMENTO',
        'SUCURSAL',
        'FEC_EMISION',
        'FEC_ACTUALIZA',
        'FECHA_ATENCION',
        'OPERACION',
        'NUM_PARTE_PROV',
        'CLIENTE',
        'PACIENTE',
        'DOC_CLIENTE',
        'CLIENTE_EMAIL',
        'CLIENTE_TELEFONO',
        'TIP_ESTADO',
        'ESPECIALIDAD',
    ];
    
    protected $dates = ['FEC_EMISION', 'FEC_ACTUALIZA', 'FECHA_ATENCION'];

    /**
     * Obtiene opciones optimizadas para selects
     */
    public static function getOpcionesOptimizadas(string $campo, array $filtros = []): array
    {
        $cacheKey = "opciones_{$campo}_" . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 3600, function () use ($campo, $filtros) {
            $query = static::select($campo)
                ->whereNotNull($campo)
                ->where($campo, '!=', '');

            // Aplicar filtros
            foreach ($filtros as $key => $value) {
                if ($value !== null) {
                    $query->where($key, $value);
                }
            }

            return $query->distinct()
                ->orderBy($campo)
                ->limit(1000) // Limitar para performance
                ->pluck($campo, $campo)
                ->toArray();
        });
    }

    /**
     * Scope para filtros optimizados
     */
    public function scopeFiltrosOptimizados($query, array $filtros)
    {
        foreach ($filtros as $campo => $valor) {
            if ($valor !== null && $valor !== '') {
                $query->where($campo, $valor);
            }
        }
        
        return $query;
    }

    /**
     * Obtiene resumen de ventas optimizado
     */
    public static function getResumenVentas(array $filtros = []): array
    {
        $cacheKey = 'resumen_ventas_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 1800, function () use ($filtros) {
            $query = static::query();
            
            // Aplicar filtros
            foreach ($filtros as $campo => $valor) {
                if ($valor !== null && $valor !== '') {
                    $query->where($campo, $valor);
                }
            }
            
            return $query->selectRaw('
                COUNT(*) as total_registros,
                SUM(VEN_CIGV) as total_ventas,
                AVG(VEN_CIGV) as promedio_ventas,
                MAX(VEN_CIGV) as mayor_venta,
                MIN(VEN_CIGV) as menor_venta,
                COUNT(DISTINCT CLIENTE) as total_clientes,
                COUNT(DISTINCT PRODUCTO) as total_productos
            ')->first()->toArray();
        });
    }
}
