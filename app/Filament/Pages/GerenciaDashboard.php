<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;

class GerenciaDashboard extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard de Gerencia';
    protected static string $view = 'filament.pages.gerencia-dashboard';

    public $periodo = '2025';
    public $empresa = 'CLINICA LA LUZ SAC';
    public $sucursal = 'Av. Gran Chimú 085, Zárate';
    public $subgrupo = 'CONSULTAS';

    // Datos para gráficos
    public array $seriesVentas = [];
    public array $labelsMes = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    public array $seriesEspecialidades = [];
    public array $labelsEspecialidades = [];
    
    // Métricas principales
    public $totalVentas = 0;
    public $totalGastos = 0;
    public $totalGanancias = 0;
    public $totalClientes = 0;
    
    // Datos para gráfico de barras mensual
    public array $ventasMensuales = [];
    
    // Datos para gráfico de visitas diarias
    public array $seriesVisitas = [];
    public array $labelsVisitas = [];

    public function mount(): void
    {
        $this->form->fill([
            'periodo' => $this->periodo,
            'empresa' => $this->empresa,
            'sucursal' => $this->sucursal,
            'subgrupo' => $this->subgrupo,
        ]);

        $this->cargarTodosLosDatos();
    }

    public function updated($property): void
    {
        $data = $this->form->getState();

        $this->periodo = $data['periodo'];
        $this->empresa = $data['empresa'];
        $this->sucursal = $data['sucursal'];
        $this->subgrupo = $data['subgrupo'];

        $this->cargarTodosLosDatos();
    }

    private function cargarTodosLosDatos(): void
    {
        $this->cargarMetricasPrincipales();
        $this->cargarSeriesVentas();
        $this->cargarSeriesEspecialidades();
        $this->cargarVentasMensuales();
        $this->cargarSeriesVisitas();
    }

    private function cargarMetricasPrincipales(): void
    {
        // Calcular total de ventas
        $ventas = DB::table('RESUMEN_VENTAS_EMPRESA_MENSUAL')
            ->where('PERIODO', $this->periodo)
            ->sum('TOTAL_VENTAS');
        
        $this->totalVentas = $ventas ?: 0;
        
        // Simular gastos (70% de las ventas)
        $this->totalGastos = $this->totalVentas * 0.7;
        
        // Calcular ganancias
        $this->totalGanancias = $this->totalVentas - $this->totalGastos;
        
        // Simular número de clientes
        $this->totalClientes = rand(150, 200);
    }

    public function cargarSeriesVentas(): void
    {
        $anio = $this->periodo;

        $ventas = DB::table('RESUMEN_VENTAS_EMPRESA_MENSUAL')
            ->select('RAZON_SOCIAL', 'MES', DB::raw('SUM(TOTAL_VENTAS) as total'))
            ->where('PERIODO', $anio)
            ->groupBy('RAZON_SOCIAL', 'MES')
            ->orderBy('RAZON_SOCIAL')
            ->orderBy('MES')
            ->get();

        $mesesOrdenados = ['01','02','03','04','05','06','07','08','09','10','11','12'];

        $series = [];

        foreach ($ventas->groupBy('RAZON_SOCIAL') as $empresa => $registros) {
            $serie = [];

            foreach ($mesesOrdenados as $mes) {
                $registroMes = $registros->firstWhere('MES', $mes);
                $serie[] = $registroMes ? (float) $registroMes->total : 0;
            }

            $series[] = [
                'name' => $empresa,
                'data' => $serie,
            ];
        }

        $this->seriesVentas = $series;
    }

    public function cargarSeriesEspecialidades(): void
    {
        $ventas = DB::table('TABLA_VISTA_VENTA_PRODUCTOS')
            ->select('ESPECIALIDAD', DB::raw('SUM(VEN_CIGV) as total'))
            ->where('PERIODO', $this->periodo)
            ->where('RAZON_SOCIAL', $this->empresa)
            ->where('SUCURSAL', $this->sucursal)
            ->where('SUBGRUPO', $this->subgrupo)
            ->groupBy('ESPECIALIDAD')
            ->orderByDesc(DB::raw('SUM(VEN_CIGV)'))
            ->limit(8)
            ->get();

        $this->labelsEspecialidades = $ventas->pluck('ESPECIALIDAD')->toArray();

        $this->seriesEspecialidades = $ventas->pluck('total')->map(fn($v) => (float) $v)->toArray();
    }

    private function cargarVentasMensuales(): void
    {
        $ventas = DB::table('RESUMEN_VENTAS_EMPRESA_MENSUAL')
            ->select('MES', DB::raw('SUM(TOTAL_VENTAS) as total'))
            ->where('PERIODO', $this->periodo)
            ->groupBy('MES')
            ->orderBy('MES')
            ->get();

        $this->ventasMensuales = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
            $venta = $ventas->firstWhere('MES', $mes);
            $this->ventasMensuales[] = $venta ? (float) $venta->total : 0;
        }
    }

    private function cargarSeriesVisitas(): void
    {
        // Simular datos de visitas diarias para los últimos 30 días
        $this->labelsVisitas = [];
        $visitasDiurnas = [];
        $visitasNocturnas = [];

        for ($i = 29; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $this->labelsVisitas[] = $fecha->format('d/m');
            $visitasDiurnas[] = rand(50, 150);
            $visitasNocturnas[] = rand(20, 80);
        }

        $this->seriesVisitas = [
            [
                'name' => 'Visitas Diurnas',
                'data' => $visitasDiurnas,
            ],
            [
                'name' => 'Visitas Nocturnas', 
                'data' => $visitasNocturnas,
            ]
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filtros')
                ->columns(4)
                ->schema([
                    Select::make('periodo')
                        ->label('Periodo')
                        ->options([
                            '2023' => '2023',
                            '2024' => '2024',
                            '2025' => '2025',
                        ])
                        ->required()
                        ->live(),

                    Select::make('empresa')
                        ->label('Empresa')
                        ->options([
                            'CLINICA LA LUZ SAC' => 'CLINICA LA LUZ SAC',
                        ])
                        ->required()
                        ->live(),

                    Select::make('sucursal')
                        ->label('Sucursal')
                        ->options([
                            'Av. Arequipa 1148, Lima' => 'Av. Arequipa 1148, Lima',
                            'Av. Perú 3811, San Martín de Porres' => 'Av. Perú 3811, San Martín de Porres',
                            'Av. Tupac Amaru 809, Comas' => 'Av. Tupac Amaru 809, Comas',
                            'Av. Gran Chimú 085, Zárate' => 'Av. Gran Chimú 085, Zárate',
                        ])
                        ->required()
                        ->live(),

                    Select::make('subgrupo')
                        ->label('Subgrupo')
                        ->options([
                            'CONSULTAS' => 'CONSULTAS',
                            'LABORATORIO' => 'LABORATORIO',
                            'FARMACIA' => 'FARMACIA',
                            'PROCEDIMIENTO' => 'PROCEDIMIENTO',
                        ])
                        ->required()
                        ->live(),
                ]),
        ];
    }
}
