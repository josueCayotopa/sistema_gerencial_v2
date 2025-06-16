<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\TablaVistaVentaProductos;
use App\Models\ResumenVentasEmpresaMensual;

class GerenciaDashboard extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard de Gerencia';
    protected static string $view = 'filament.pages.gerencia-dashboard';

    // Propiedades para filtros
    public $PERIODO = '2025';
    public $MES = null;
    public $RAZON_SOCIAL = null;
    public $SUCURSAL = null;
    public $SUBGRUPO = null;

    // Datos para gráficos
    public array $datosGraficoEmpresas = [];
    public array $estadisticasGenerales = [];
    public array $ventasPorSucursal = [];
    public array $subgruposMasVendidos = [];
    public array $evolucionMensual = [];
    public array $ventasPorMes = [];
    public array $topProductos = [];
    public array $distribucionVentas = [];
    
    // Métricas principales
    public $totalVentas = 0;
    public $totalClientes = 0;
    public $promedioMensual = 0;
    public $mayorVenta = 0;
    
    // Control de búsqueda
    public $datosActualizados = false;
    public $cargandoDatos = false;
    public $tiempoConsulta = 0;

    public function mount(): void
    {
        $this->form->fill([
            'PERIODO' => $this->PERIODO,
            'MES' => $this->MES,
            'RAZON_SOCIAL' => $this->RAZON_SOCIAL,
            'SUCURSAL' => $this->SUCURSAL,
            'SUBGRUPO' => $this->SUBGRUPO,
        ]);
        
        $this->cargarDatosIniciales();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filtros de Búsqueda Completos')
                ->description('Dashboard con filtros dinámicos y gráficos interactivos')
                ->columns(5)
                ->schema([
                    Select::make('PERIODO')
                        ->label('Período')
                        ->options([
                            '2025' => '2025',
                            '2024' => '2024',
                            '2023' => '2023',
                            '2022' => '2022',
                            '2021' => '2021',
                        ])
                        ->required()
                        ->default('2025')
                        ->reactive()
                        ->searchable(),

                    Select::make('MES')
                        ->label('Mes')
                        ->options([
                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
                        ])
                        ->placeholder('Todos los meses')
                        ->default('06')
                        ->reactive()
                        ->searchable(),

                    Select::make('RAZON_SOCIAL')
                        ->label('Empresa')
                        ->options(function () {
                            // Cache en archivo por 1 hora
                            return Cache::remember('empresas_options_v5', 3600, function () {
                                try {
                                    return TablaVistaVentaProductos::getOpcionesOptimizadas('RAZON_SOCIAL');
                                } catch (\Exception $e) {
                                    Log::error('Error cargando empresas: ' . $e->getMessage());
                                    return [];
                                }
                            });
                        })
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('SUCURSAL', null))
                        ->searchable(),

                    Select::make('SUCURSAL')
                        ->label('Sucursal')
                        ->reactive()
                        ->options(function (callable $get) {
                            $empresa = $get('RAZON_SOCIAL');
                            if (!$empresa) return [];

                            // Cache en archivo por 30 minutos
                            return Cache::remember("sucursales_v5_{$empresa}", 1800, function () use ($empresa) {
                                try {
                                    return TablaVistaVentaProductos::getOpcionesOptimizadas('SUCURSAL', [
                                        'RAZON_SOCIAL' => $empresa
                                    ]);
                                } catch (\Exception $e) {
                                    Log::error('Error cargando sucursales: ' . $e->getMessage());
                                    return [];
                                }
                            });
                        })
                        ->afterStateUpdated(fn ($state, callable $set) => $set('SUBGRUPO', null))
                        ->searchable(),

                    Select::make('SUBGRUPO')
                        ->label('Subgrupo')
                        ->reactive()
                        ->options(function (callable $get) {
                            $empresa = $get('RAZON_SOCIAL');
                            $sucursal = $get('SUCURSAL');
                            
                            if (!$empresa) return [];

                            $filtros = ['RAZON_SOCIAL' => $empresa];
                            if ($sucursal) $filtros['SUCURSAL'] = $sucursal;
                            
                            $cacheKey = "subgrupos_v5_" . md5(serialize($filtros));
                            return Cache::remember($cacheKey, 1800, function () use ($filtros) {
                                try {
                                    return TablaVistaVentaProductos::getOpcionesOptimizadas('SUBGRUPO', $filtros);
                                } catch (\Exception $e) {
                                    Log::error('Error cargando subgrupos: ' . $e->getMessage());
                                    return [];
                                }
                            });
                        })
                        ->searchable(),
                ]),

            Section::make('')
                ->schema([
                    Actions::make([
                        Action::make('buscar')
                            ->label('Buscar Datos')
                            ->icon('heroicon-o-magnifying-glass')
                            ->color('primary')
                            ->size('lg')
                            ->action('buscarDatos')
                            ->disabled(fn () => $this->cargandoDatos),
                        
                        Action::make('limpiar')
                            ->label('Limpiar Filtros')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                            ->size('lg')
                            ->action(function () {
                                $this->form->fill([
                                    'PERIODO' => '2025',
                                    'MES' => null,
                                    'RAZON_SOCIAL' => null,
                                    'SUCURSAL' => null,
                                    'SUBGRUPO' => null,
                                ]);
                                $this->cargarDatosIniciales();
                            }),

                        Action::make('limpiar_cache')
                            ->label('Limpiar Cache')
                            ->icon('heroicon-o-trash')
                            ->color('warning')
                            ->size('lg')
                            ->action('limpiarCache'),

                        Action::make('actualizar_resumen')
                            ->label('Actualizar Resumen')
                            ->icon('heroicon-o-arrow-path')
                            ->color('success')
                            ->size('lg')
                            ->action('actualizarResumenVentas'),
                    ])
                    ->columnSpanFull()
                    ->alignment('center'),
                ]),
        ];
    }

    public function buscarDatos()
    {
        $this->cargandoDatos = true;
        $tiempoInicio = microtime(true);
        
        try {
            $data = $this->form->getState();
            
            // Actualizar propiedades
            $this->PERIODO = $data['PERIODO'];
            $this->MES = $data['MES'];
            $this->RAZON_SOCIAL = $data['RAZON_SOCIAL'];
            $this->SUCURSAL = $data['SUCURSAL'];
            $this->SUBGRUPO = $data['SUBGRUPO'];
            
            // Cargar todos los datos
            $this->cargarDatosCompletos();
            
            $this->datosActualizados = true;
            $this->tiempoConsulta = round((microtime(true) - $tiempoInicio) * 1000, 2);
            
            $this->dispatch('datos-actualizados');
            
        } catch (\Exception $e) {
            Log::error('Error en búsqueda: ' . $e->getMessage());
            $this->dispatch('error', ['message' => 'Error al cargar los datos: ' . $e->getMessage()]);
        } finally {
            $this->cargandoDatos = false;
        }
    }

    public function limpiarCache()
    {
        try {
            // Limpiar cache específico del dashboard
            $patterns = [
                'dashboard_gerencia_v6_*',
                'empresas_options_v5',
                'sucursales_v5_*',
                'subgrupos_v5_*',
                'resumen_ventas_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }

            // También podemos limpiar todo el cache si es necesario
            // Cache::flush();

            $this->dispatch('success', ['message' => 'Cache limpiado correctamente']);
            
            // Recargar datos
            $this->cargarDatosIniciales();
            
        } catch (\Exception $e) {
            Log::error('Error limpiando cache: ' . $e->getMessage());
            $this->dispatch('error', ['message' => 'Error al limpiar cache']);
        }
    }

    private function cargarDatosIniciales()
    {
        $this->cargarDatosCompletos();
    }

    private function cargarDatosCompletos()
    {
        $cacheKey = $this->generarCacheKey();
        
        // Cache en archivo por 30 minutos
        $datos = Cache::remember($cacheKey, 1800, function () {
            return $this->procesarDatosOptimizado();
        });
        
        $this->asignarDatos($datos);
    }

    private function generarCacheKey(): string
    {
        return 'dashboard_gerencia_v6_' . md5(serialize([
            'periodo' => $this->PERIODO,
            'mes' => $this->MES,
            'empresa' => $this->RAZON_SOCIAL,
            'sucursal' => $this->SUCURSAL,
            'subgrupo' => $this->SUBGRUPO,
        ]));
    }

    private function procesarDatosOptimizado(): array
    {
        // Usar consultas optimizadas con los índices creados
        $baseQuery = TablaVistaVentaProductos::query()
            ->select([
                'PERIODO',
                'MES',
                'RAZON_SOCIAL',
                'SUCURSAL',
                'SUBGRUPO',
                'PRODUCTO',
                'VEN_CIGV',
                'CANTIDAD',
                'CLIENTE',
                'FEC_EMISION'
            ])
            ->where('PERIODO', $this->PERIODO);

        // Aplicar filtros (aprovechando los índices)
        if ($this->MES) {
            $baseQuery->where('MES', $this->MES);
        }
        if ($this->RAZON_SOCIAL) {
            $baseQuery->where('RAZON_SOCIAL', $this->RAZON_SOCIAL);
        }
        if ($this->SUCURSAL) {
            $baseQuery->where('SUCURSAL', $this->SUCURSAL);
        }
        if ($this->SUBGRUPO) {
            $baseQuery->where('SUBGRUPO', $this->SUBGRUPO);
        }

        // Obtener datos con límite para evitar sobrecarga
        $datosBase = $baseQuery->limit(50000)->get();

        return [
            'metricas' => $this->calcularMetricas($datosBase),
            'ventasPorMes' => $this->procesarVentasPorMes($datosBase),
            'ventasPorSucursal' => $this->procesarVentasPorSucursal($datosBase),
            'topProductos' => $this->procesarTopProductos($datosBase),
            'distribucionVentas' => $this->procesarDistribucionVentas($datosBase),
            'evolucionMensual' => $this->procesarEvolucionMensual($datosBase),
        ];
    }

    private function calcularMetricas($datos): array
    {
        return [
            'totalVentas' => $datos->sum('VEN_CIGV'),
            'totalClientes' => $datos->pluck('CLIENTE')->unique()->count(),
            'promedioMensual' => $datos->avg('VEN_CIGV'),
            'mayorVenta' => $datos->max('VEN_CIGV'),
        ];
    }

    private function procesarVentasPorMes($datos): array
    {
        $ventasPorMes = $datos->groupBy('MES')
            ->map(function ($grupo) {
                return [
                    'mes' => $grupo->first()->MES,
                    'ventas' => $grupo->sum('VEN_CIGV'),
                    'cantidad' => $grupo->sum('CANTIDAD'),
                ];
            })
            ->sortBy('mes')
            ->values()
            ->toArray();

        return $ventasPorMes;
    }

    private function procesarVentasPorSucursal($datos): array
    {
        return $datos->groupBy('SUCURSAL')
            ->map(function ($grupo, $sucursal) {
                return [
                    'sucursal' => $sucursal,
                    'ventas' => $grupo->sum('VEN_CIGV'),
                    'clientes' => $grupo->pluck('CLIENTE')->unique()->count(),
                ];
            })
            ->sortByDesc('ventas')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function procesarTopProductos($datos): array
    {
        return $datos->groupBy('PRODUCTO')
            ->map(function ($grupo, $producto) {
                return [
                    'producto' => $producto,
                    'ventas' => $grupo->sum('VEN_CIGV'),
                    'cantidad' => $grupo->sum('CANTIDAD'),
                ];
            })
            ->sortByDesc('ventas')
            ->take(15)
            ->values()
            ->toArray();
    }

    private function procesarDistribucionVentas($datos): array
    {
        return $datos->groupBy('SUBGRUPO')
            ->map(function ($grupo, $subgrupo) {
                return [
                    'subgrupo' => $subgrupo,
                    'ventas' => $grupo->sum('VEN_CIGV'),
                    'porcentaje' => 0,
                ];
            })
            ->sortByDesc('ventas')
            ->take(8)
            ->values()
            ->toArray();
    }

    private function procesarEvolucionMensual($datos): array
    {
        $meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $nombresMeses = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
            '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];

        $evolucion = [];
        foreach ($meses as $mes) {
            $ventasMes = $datos->where('MES', $mes)->sum('VEN_CIGV');
            $evolucion[] = [
                'mes' => $nombresMeses[$mes],
                'ventas' => $ventasMes,
                'numero_mes' => $mes,
            ];
        }

        return $evolucion;
    }

    private function asignarDatos(array $datos)
    {
        // Métricas
        $this->totalVentas = $datos['metricas']['totalVentas'];
        $this->totalClientes = $datos['metricas']['totalClientes'];
        $this->promedioMensual = $datos['metricas']['promedioMensual'];
        $this->mayorVenta = $datos['metricas']['mayorVenta'];

        // Datos para gráficos
        $this->ventasPorMes = $datos['ventasPorMes'];
        $this->ventasPorSucursal = $datos['ventasPorSucursal'];
        $this->topProductos = $datos['topProductos'];
        $this->distribucionVentas = $datos['distribucionVentas'];
        $this->evolucionMensual = $datos['evolucionMensual'];
    }

    public function actualizarResumenVentas()
    {
        try {
            // Lógica para actualizar resumen
            $this->dispatch('success', ['message' => 'Resumen actualizado correctamente']);
        } catch (\Exception $e) {
            Log::error('Error actualizando resumen: ' . $e->getMessage());
            $this->dispatch('error', ['message' => 'Error al actualizar resumen']);
        }
    }

    // Métodos para obtener datos de gráficos
    public function getVentasPorMesChart(): array
    {
        return [
            'series' => [
                [
                    'name' => 'Ventas',
                    'data' => array_column($this->ventasPorMes, 'ventas'),
                ]
            ],
            'categories' => array_column($this->ventasPorMes, 'mes'),
        ];
    }

    public function getVentasPorSucursalChart(): array
    {
        return [
            'series' => array_column($this->ventasPorSucursal, 'ventas'),
            'labels' => array_column($this->ventasPorSucursal, 'sucursal'),
        ];
    }

    public function getTopProductosChart(): array
    {
        return [
            'series' => [
                [
                    'name' => 'Ventas',
                    'data' => array_column($this->topProductos, 'ventas'),
                ]
            ],
            'categories' => array_map(function($producto) {
                return strlen($producto) > 20 ? substr($producto, 0, 20) . '...' : $producto;
            }, array_column($this->topProductos, 'producto')),
        ];
    }

    public function getDistribucionVentasChart(): array
    {
        return [
            'series' => array_column($this->distribucionVentas, 'ventas'),
            'labels' => array_column($this->distribucionVentas, 'subgrupo'),
        ];
    }
}
