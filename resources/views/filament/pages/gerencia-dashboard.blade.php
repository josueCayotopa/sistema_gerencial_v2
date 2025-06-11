<x-filament::page>
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Spinner de carga --}}
    <div wire:loading class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
        <div class="text-center">
            <svg class="animate-spin h-8 w-8 mx-auto mb-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-gray-600 font-medium">Cargando dashboard...</p>
        </div>
    </div>

    <div wire:loading.remove class="space-y-6">
        {{-- Métricas principales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Ventas --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Ventas</p>
                        <p class="text-2xl font-bold">S/ {{ number_format($totalVentas, 0) }}</p>
                        <p class="text-blue-100 text-xs mt-1">+12.5% vs mes anterior</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                        </svg>
                    </div>
                </div>
                <div id="sparkline-ventas" class="mt-4" style="height: 50px;"></div>
            </div>

            {{-- Total Gastos --}}
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Total Gastos</p>
                        <p class="text-2xl font-bold">S/ {{ number_format($totalGastos, 0) }}</p>
                        <p class="text-red-100 text-xs mt-1">+8.2% vs mes anterior</p>
                    </div>
                    <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM6 12a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div id="sparkline-gastos" class="mt-4" style="height: 50px;"></div>
            </div>

            {{-- Ganancias --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Ganancias</p>
                        <p class="text-2xl font-bold">S/ {{ number_format($totalGanancias, 0) }}</p>
                        <p class="text-green-100 text-xs mt-1">+18.7% vs mes anterior</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div id="sparkline-ganancias" class="mt-4" style="height: 50px;"></div>
            </div>

            {{-- Total Clientes --}}
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Clientes Atendidos</p>
                        <p class="text-2xl font-bold">{{ number_format($totalClientes) }}</p>
                        <p class="text-purple-100 text-xs mt-1">+5.3% vs mes anterior</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                </div>
                <div id="sparkline-clientes" class="mt-4" style="height: 50px;"></div>
            </div>
        </div>

        {{-- Gráficos principales --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfico de líneas - Ventas por empresa --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ventas Mensuales por Empresa</h3>
                    <div class="flex space-x-2">
                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="graficoVentasEmpresas" style="height: 350px;"></div>
            </div>

            {{-- Gráfico de dona - Ventas por especialidad --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ventas por Especialidad</h3>
                    <div class="flex space-x-2">
                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="graficoEspecialidades" style="height: 350px;"></div>
            </div>
        </div>

        {{-- Gráficos secundarios --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfico de barras - Ventas mensuales --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ventas Mensuales {{ $periodo }}</h3>
                    <span class="text-sm text-gray-500">Total: S/ {{ number_format($totalVentas, 0) }}</span>
                </div>
                <div id="graficoVentasMensuales" style="height: 300px;"></div>
            </div>

            {{-- Gráfico de área - Visitas diarias --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Visitas Diarias</h3>
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Diurnas</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Nocturnas</span>
                        </div>
                    </div>
                </div>
                <div id="graficoVisitas" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let charts = {};

            function renderAllCharts() {
                // Destruir gráficos existentes
                Object.values(charts).forEach(chart => {
                    if (chart) chart.destroy();
                });

                // Gráfico de líneas - Ventas por empresa
                charts.ventasEmpresas = new ApexCharts(document.querySelector("#graficoVentasEmpresas"), {
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: { show: true },
                        zoom: { enabled: true }
                    },
                    series: @json($seriesVentas),
                    xaxis: {
                        categories: @json($labelsMes)
                    },
                    colors: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 5,
                        hover: {
                            size: 7
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: value => 'S/ ' + value.toLocaleString()
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 5
                    }
                });

                // Gráfico de dona - Especialidades
                charts.especialidades = new ApexCharts(document.querySelector("#graficoEspecialidades"), {
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    series: @json($seriesEspecialidades),
                    labels: @json($labelsEspecialidades),
                    colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function (w) {
                                            return 'S/ ' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString()
                                        }
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    },
                    tooltip: {
                        y: {
                            formatter: value => 'S/ ' + value.toLocaleString()
                        }
                    }
                });

                // Gráfico de barras - Ventas mensuales
                charts.ventasMensuales = new ApexCharts(document.querySelector("#graficoVentasMensuales"), {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Ventas',
                        data: @json($ventasMensuales)
                    }],
                    xaxis: {
                        categories: @json($labelsMes)
                    },
                    colors: ['#3B82F6'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '60%'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: value => 'S/ ' + value.toLocaleString()
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 5
                    }
                });

                // Gráfico de área - Visitas
                charts.visitas = new ApexCharts(document.querySelector("#graficoVisitas"), {
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: { show: false }
                    },
                    series: @json($seriesVisitas),
                    xaxis: {
                        categories: @json($labelsVisitas)
                    },
                    colors: ['#3B82F6', '#10B981'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    tooltip: {
                        y: {
                            formatter: value => value + ' visitas'
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 5
                    }
                });

                // Sparklines para las métricas
                const sparklineOptions = {
                    chart: {
                        type: 'line',
                        height: 50,
                        sparkline: { enabled: true }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    tooltip: { enabled: false },
                    markers: { size: 0 }
                };

                // Sparkline ventas
                charts.sparklineVentas = new ApexCharts(document.querySelector("#sparkline-ventas"), {
                    ...sparklineOptions,
                    series: [{
                        data: [23, 45, 32, 67, 49, 72, 52, 61, 78, 45, 67, 89]
                    }],
                    colors: ['rgba(255,255,255,0.8)']
                });

                // Sparkline gastos
                charts.sparklineGastos = new ApexCharts(document.querySelector("#sparkline-gastos"), {
                    ...sparklineOptions,
                    series: [{
                        data: [34, 56, 43, 78, 65, 82, 67, 71, 88, 56, 77, 95]
                    }],
                    colors: ['rgba(255,255,255,0.8)']
                });

                // Sparkline ganancias
                charts.sparklineGanancias = new ApexCharts(document.querySelector("#sparkline-ganancias"), {
                    ...sparklineOptions,
                    series: [{
                        data: [12, 25, 18, 35, 28, 42, 31, 38, 45, 28, 41, 52]
                    }],
                    colors: ['rgba(255,255,255,0.8)']
                });

                // Sparkline clientes
                charts.sparklineClientes = new ApexCharts(document.querySelector("#sparkline-clientes"), {
                    ...sparklineOptions,
                    series: [{
                        data: [145, 167, 152, 178, 165, 189, 172, 181, 195, 168, 184, 201]
                    }],
                    colors: ['rgba(255,255,255,0.8)']
                });

                // Renderizar todos los gráficos
                Object.values(charts).forEach(chart => {
                    if (chart) chart.render();
                });
            }

            // Renderizar gráficos iniciales
            renderAllCharts();

            // Redibuja cuando Livewire actualiza
            Livewire.hook('message.processed', () => {
                setTimeout(renderAllCharts, 100);
            });
        });
    </script>

    <style>
        .apexcharts-tooltip {
            background: rgba(0, 0, 0, 0.8) !important;
            border: none !important;
            border-radius: 8px !important;
            color: white !important;
        }
        
        .apexcharts-legend-text {
            color: #374151 !important;
        }
    </style>
</x-filament::page>
