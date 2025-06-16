<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResumenVentasEmpresaMensual extends Model
{
    protected $table = 'RESUMEN_VENTAS_EMPRESA_MENSUAL';
    
    // No tiene timestamps
    public $timestamps = false;
    
    // Clave primaria compuesta
    protected $primaryKey = ['PERIODO', 'MES', 'RAZON_SOCIAL'];
    public $incrementing = false;
    
    protected $fillable = [
        'PERIODO',
        'MES', 
        'RAZON_SOCIAL',
        'TOTAL_VENTAS'
    ];

    protected $casts = [
        'TOTAL_VENTAS' => 'decimal:2'
    ];

    /**
     * Obtener datos para gráfico de líneas por empresa y período
     */
    public static function getVentasLineChart($periodo, $empresa, $mesHasta = null)
    {
        try {
            $query = self::select('MES', 'TOTAL_VENTAS')
                ->where('PERIODO', $periodo)
                ->where('RAZON_SOCIAL', $empresa)
                ->orderBy('MES');
            
            // Si se especifica mes hasta, filtrar
            if ($mesHasta) {
                $query->where('MES', '<=', str_pad($mesHasta, 2, '0', STR_PAD_LEFT));
            }
            
            $datos = $query->get();
            
            // Crear array con todos los meses del año
            $ventasPorMes = [];
            $mesesLabels = [];
            
            for ($i = 1; $i <= 12; $i++) {
                $mesStr = str_pad($i, 2, '0', STR_PAD_LEFT);
                $venta = $datos->firstWhere('MES', $mesStr);
                
                // Si hay mes hasta definido y el mes actual es mayor, no incluir
                if ($mesHasta && $i > $mesHasta) {
                    break;
                }
                
                $ventasPorMes[] = $venta ? (float) $venta->TOTAL_VENTAS : 0;
                $mesesLabels[] = self::getNombreMes($i);
            }
            
            return [
                'data' => $ventasPorMes,
                'labels' => $mesesLabels,
                'empresa' => $empresa
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error en getVentasLineChart: ' . $e->getMessage());
            return [
                'data' => [],
                'labels' => [],
                'empresa' => $empresa
            ];
        }
    }

    /**
     * Obtener comparativa de múltiples empresas
     */
    public static function getComparativaEmpresas($periodo, $empresas = [], $mesHasta = null)
    {
        try {
            $query = self::select('RAZON_SOCIAL', 'MES', 'TOTAL_VENTAS')
                ->where('PERIODO', $periodo);
            
            if (!empty($empresas)) {
                $query->whereIn('RAZON_SOCIAL', $empresas);
            } else {
                // Si no se especifican empresas, tomar las top 5
                $topEmpresas = self::select('RAZON_SOCIAL')
                    ->selectRaw('SUM(TOTAL_VENTAS) as total')
                    ->where('PERIODO', $periodo)
                    ->groupBy('RAZON_SOCIAL')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->pluck('RAZON_SOCIAL');
                
                $query->whereIn('RAZON_SOCIAL', $topEmpresas);
            }
            
            if ($mesHasta) {
                $query->where('MES', '<=', str_pad($mesHasta, 2, '0', STR_PAD_LEFT));
            }
            
            $datos = $query->orderBy('RAZON_SOCIAL')->orderBy('MES')->get();
            
            // Agrupar por empresa
            $series = [];
            $empresasUnicas = $datos->pluck('RAZON_SOCIAL')->unique();
            
            foreach ($empresasUnicas as $empresa) {
                $ventasEmpresa = [];
                $datosEmpresa = $datos->where('RAZON_SOCIAL', $empresa);
                
                $maxMes = $mesHasta ?: 12;
                for ($i = 1; $i <= $maxMes; $i++) {
                    $mesStr = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $venta = $datosEmpresa->firstWhere('MES', $mesStr);
                    $ventasEmpresa[] = $venta ? (float) $venta->TOTAL_VENTAS : 0;
                }
                
                $series[] = [
                    'name' => $empresa,
                    'data' => $ventasEmpresa
                ];
            }
            
            // Labels de meses
            $maxMes = $mesHasta ?: 12;
            $labels = [];
            for ($i = 1; $i <= $maxMes; $i++) {
                $labels[] = self::getNombreMes($i);
            }
            
            return [
                'series' => $series,
                'labels' => $labels
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error en getComparativaEmpresas: ' . $e->getMessage());
            return [
                'series' => [],
                'labels' => []
            ];
        }
    }

    /**
     * Obtener estadísticas del período
     */
    public static function getEstadisticasPeriodo($periodo, $empresa = null, $mesHasta = null)
    {
        try {
            $query = self::where('PERIODO', $periodo);
            
            if ($empresa) {
                $query->where('RAZON_SOCIAL', $empresa);
            }
            
            if ($mesHasta) {
                $query->where('MES', '<=', str_pad($mesHasta, 2, '0', STR_PAD_LEFT));
            }
            
            $stats = $query->selectRaw('
                SUM(TOTAL_VENTAS) as total_ventas,
                AVG(TOTAL_VENTAS) as promedio_mensual,
                MAX(TOTAL_VENTAS) as mes_mayor_venta,
                MIN(TOTAL_VENTAS) as mes_menor_venta,
                COUNT(DISTINCT RAZON_SOCIAL) as total_empresas
            ')->first();
            
            return [
                'total_ventas' => (float) ($stats->total_ventas ?? 0),
                'promedio_mensual' => (float) ($stats->promedio_mensual ?? 0),
                'mes_mayor_venta' => (float) ($stats->mes_mayor_venta ?? 0),
                'mes_menor_venta' => (float) ($stats->mes_menor_venta ?? 0),
                'total_empresas' => (int) ($stats->total_empresas ?? 0)
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error en getEstadisticasPeriodo: ' . $e->getMessage());
            return [
                'total_ventas' => 0,
                'promedio_mensual' => 0,
                'mes_mayor_venta' => 0,
                'mes_menor_venta' => 0,
                'total_empresas' => 0
            ];
        }
    }

    /**
     * Obtener nombre del mes
     */
    private static function getNombreMes($numeroMes)
    {
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        
        return $meses[$numeroMes] ?? 'N/A';
    }

    /**
     * Actualizar resumen de ventas ejecutando el SP
     */
    public static function actualizarResumen()
    {
        try {
            DB::statement('EXEC SP_ACTUALIZAR_RESUMEN_VENTAS');
            return true;
        } catch (\Exception $e) {
            \Log::error('Error actualizando resumen de ventas: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener datos pivoteados de todas las empresas por período
     */
    public static function getVentasTodasEmpresas($periodo)
    {
        try {
            // Primero verificar si hay datos para el período
            $existenDatos = self::where('PERIODO', $periodo)->exists();
            
            if (!$existenDatos) {
                Log::warning("No hay datos para el período: {$periodo}");
                return [
                    'series' => [],
                    'labels' => ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                    'total_empresas' => 0,
                    'mensaje' => "No hay datos disponibles para el período {$periodo}"
                ];
            }
            
            $sql = "
            SELECT 
                RAZON_SOCIAL,
                SUM(CASE WHEN MES = '01' THEN TOTAL_VENTAS ELSE 0 END) AS Ene,
                SUM(CASE WHEN MES = '02' THEN TOTAL_VENTAS ELSE 0 END) AS Feb,
                SUM(CASE WHEN MES = '03' THEN TOTAL_VENTAS ELSE 0 END) AS Mar,
                SUM(CASE WHEN MES = '04' THEN TOTAL_VENTAS ELSE 0 END) AS Abr,
                SUM(CASE WHEN MES = '05' THEN TOTAL_VENTAS ELSE 0 END) AS May,
                SUM(CASE WHEN MES = '06' THEN TOTAL_VENTAS ELSE 0 END) AS Jun,
                SUM(CASE WHEN MES = '07' THEN TOTAL_VENTAS ELSE 0 END) AS Jul,
                SUM(CASE WHEN MES = '08' THEN TOTAL_VENTAS ELSE 0 END) AS Ago,
                SUM(CASE WHEN MES = '09' THEN TOTAL_VENTAS ELSE 0 END) AS Sep,
                SUM(CASE WHEN MES = '10' THEN TOTAL_VENTAS ELSE 0 END) AS Oct,
                SUM(CASE WHEN MES = '11' THEN TOTAL_VENTAS ELSE 0 END) AS Nov,
                SUM(CASE WHEN MES = '12' THEN TOTAL_VENTAS ELSE 0 END) AS Dic,
                SUM(TOTAL_VENTAS) as total_anual
            FROM RESUMEN_VENTAS_EMPRESA_MENSUAL
            WHERE PERIODO = ?
            GROUP BY RAZON_SOCIAL
            HAVING SUM(TOTAL_VENTAS) > 0
            ORDER BY total_anual DESC, RAZON_SOCIAL
        ";
        
        $resultados = DB::select($sql, [$periodo]);
        
        // Convertir a formato para ApexCharts
        $series = [];
        $labels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        foreach ($resultados as $empresa) {
            $series[] = [
                'name' => $empresa->RAZON_SOCIAL,
                'data' => [
                    (float) $empresa->Ene, (float) $empresa->Feb, (float) $empresa->Mar,
                    (float) $empresa->Abr, (float) $empresa->May, (float) $empresa->Jun,
                    (float) $empresa->Jul, (float) $empresa->Ago, (float) $empresa->Sep,
                    (float) $empresa->Oct, (float) $empresa->Nov, (float) $empresa->Dic,
                ]
            ];
        }
        
        return [
            'series' => $series,
            'labels' => $labels,
            'total_empresas' => count($resultados),
            'mensaje' => count($resultados) > 0 ? null : "No hay ventas registradas para el período {$periodo}"
        ];
        
    } catch (\Exception $e) {
        \Log::error('Error en getVentasTodasEmpresas: ' . $e->getMessage());
        return [
            'series' => [],
            'labels' => ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
            'total_empresas' => 0,
            'mensaje' => 'Error al cargar datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener estadísticas generales del período
 */
public static function getEstadisticasGenerales($periodo)
{
    try {
        // Verificar si hay datos
        $existenDatos = self::where('PERIODO', $periodo)->exists();
        
        if (!$existenDatos) {
            return [
                'total_ventas' => 0,
                'promedio_mensual' => 0,
                'mes_mayor_venta' => 0,
                'mes_menor_venta' => 0,
                'total_empresas' => 0,
                'total_registros' => 0,
                'periodo_activo' => $periodo,
                'tiene_datos' => false
            ];
        }
        
        $stats = self::where('PERIODO', $periodo)
            ->selectRaw('
                SUM(TOTAL_VENTAS) as total_ventas,
                AVG(TOTAL_VENTAS) as promedio_mensual,
                MAX(TOTAL_VENTAS) as mes_mayor_venta,
                MIN(CASE WHEN TOTAL_VENTAS > 0 THEN TOTAL_VENTAS END) as mes_menor_venta,
                COUNT(DISTINCT RAZON_SOCIAL) as total_empresas,
                COUNT(*) as total_registros
            ')->first();
        
        return [
            'total_ventas' => (float) ($stats->total_ventas ?? 0),
            'promedio_mensual' => (float) ($stats->promedio_mensual ?? 0),
            'mes_mayor_venta' => (float) ($stats->mes_mayor_venta ?? 0),
            'mes_menor_venta' => (float) ($stats->mes_menor_venta ?? 0),
            'total_empresas' => (int) ($stats->total_empresas ?? 0),
            'total_registros' => (int) ($stats->total_registros ?? 0),
            'periodo_activo' => $periodo,
            'tiene_datos' => true
        ];
        
    } catch (\Exception $e) {
        \Log::error('Error en getEstadisticasGenerales: ' . $e->getMessage());
        return [
            'total_ventas' => 0,
            'promedio_mensual' => 0,
            'mes_mayor_venta' => 0,
            'mes_menor_venta' => 0,
            'total_empresas' => 0,
            'total_registros' => 0,
            'periodo_activo' => $periodo,
            'tiene_datos' => false
        ];
    }
}
}
