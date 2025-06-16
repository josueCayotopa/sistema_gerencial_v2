<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TablaVistaVentaProductos extends Model
{
    use HasFactory;

    protected $table = 'TABLA_VISTA_VENTA_PRODUCTOS';
    protected $primaryKey = 'OPERACION';
    public $incrementing = false;
    public $timestamps = false;
    
    // Campos optimizados sin duplicados
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
    
    // Scopes optimizados para consultas frecuentes
    public function scopeActivos($query)
    {
        return $query->where('TIP_ESTADO', 'NN');
    }
    
    public function scopeDocumentosValidos($query)
    {
        return $query->whereIn('COD_DOCUMENTO', ['FV', 'BV', 'NC', 'ND']);
    }
    
    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('PERIODO', $periodo);
    }
    
    public function scopePorEmpresa($query, $empresa)
    {
        return $query->where('RAZON_SOCIAL', $empresa);
    }
    
    // Método optimizado para obtener opciones de select
    public static function getOpcionesOptimizadas($campo, $filtros = [])
    {
        $query = self::select($campo)
            ->activos()
            ->documentosValidos()
            ->whereNotNull($campo)
            ->where($campo, '!=', '');
            
        // Aplicar filtros adicionales
        foreach ($filtros as $key => $value) {
            if ($value) {
                $query->where($key, $value);
            }
        }
        
        return $query->groupBy($campo)
            ->orderBy($campo)
            ->limit(100) // Limitar para evitar timeouts
            ->pluck($campo, $campo)
            ->toArray();
    }
    
    // Método para consultas de ventas optimizadas
    public static function getVentasPivoteadas($filtros = [])
    {
        $sql = "
            SELECT 
                RAZON_SOCIAL,
                SUM(CASE WHEN MES = '01' THEN VEN_CIGV ELSE 0 END) AS Ene,
                SUM(CASE WHEN MES = '02' THEN VEN_CIGV ELSE 0 END) AS Feb,
                SUM(CASE WHEN MES = '03' THEN VEN_CIGV ELSE 0 END) AS Mar,
                SUM(CASE WHEN MES = '04' THEN VEN_CIGV ELSE 0 END) AS Abr,
                SUM(CASE WHEN MES = '05' THEN VEN_CIGV ELSE 0 END) AS May,
                SUM(CASE WHEN MES = '06' THEN VEN_CIGV ELSE 0 END) AS Jun,
                SUM(CASE WHEN MES = '07' THEN VEN_CIGV ELSE 0 END) AS Jul,
                SUM(CASE WHEN MES = '08' THEN VEN_CIGV ELSE 0 END) AS Ago,
                SUM(CASE WHEN MES = '09' THEN VEN_CIGV ELSE 0 END) AS Sep,
                SUM(CASE WHEN MES = '10' THEN VEN_CIGV ELSE 0 END) AS Oct,
                SUM(CASE WHEN MES = '11' THEN VEN_CIGV ELSE 0 END) AS Nov,
                SUM(CASE WHEN MES = '12' THEN VEN_CIGV ELSE 0 END) AS Dic
            FROM TABLA_VISTA_VENTA_PRODUCTOS
            WHERE 
                PERIODO = ? 
                AND TIP_ESTADO = 'NN'
                AND COD_DOCUMENTO IN ('FV', 'BV', 'NC', 'ND')
        ";
        
        $params = [$filtros['PERIODO'] ?? date('Y')];
        
        // Aplicar filtros dinámicos
        if (!empty($filtros['RAZON_SOCIAL'])) {
            $sql .= " AND RAZON_SOCIAL = ?";
            $params[] = $filtros['RAZON_SOCIAL'];
        }
        
        if (!empty($filtros['SUCURSAL'])) {
            $sql .= " AND SUCURSAL = ?";
            $params[] = $filtros['SUCURSAL'];
        }
        
        if (!empty($filtros['SUBGRUPO'])) {
            $sql .= " AND SUBGRUPO = ?";
            $params[] = $filtros['SUBGRUPO'];
        }
        
        $sql .= " GROUP BY RAZON_SOCIAL ORDER BY RAZON_SOCIAL LIMIT 20";
        
        return DB::select($sql, $params);
    }
}
