<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulario de filtros -->
        <div class="bg-white rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        <!-- Indicadores de estado -->
        @if($cargandoDatos)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-blue-700 font-medium">Cargando datos...</span>
                </div>
            </div>
        @endif

        @if($datosActualizados && !$cargandoDatos)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <span class="text-green-700 font-medium">✓ Datos actualizados correctamente</span>
                    <span class="text-green-600 text-sm">Tiempo: {{ $tiempoConsulta }}ms</span>
                </div>
            </div>
        @endif

        <!-- Métricas principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Ventas</p>
                        <p class="text-2xl font-bold">S/ {{ number_format($totalVentas, 2) }}</p>
                    </div>
                    <div class="bg-blue-400 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Clientes</p>
                        <p class="text-2xl font-bold">{{ number_format($totalClientes) }}</p>
                    </div>
                    <div class="bg-green-400 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Promedio Mensual</p>
                        <p class="text-2xl font-bold">S/ {{ number_format($promedioMensual, 2) }}</p>
                    </div>
                    <div class="bg-purple-400 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Mayor Venta</p>
                        <p class="text-2xl font-bold">S/ {{ number_format($mayorVenta, 2) }}</p>
                    </div>
                    <div class="bg-orange-400 rounded-full p-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfico de Ventas por Mes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Evolución de Ventas Mensual</h3>
                <div id="ventasPorMesChart"></div>
            </div>

            <!-- Gráfico de Ventas por Sucursal -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Sucursal</h3>
                <div id="ventasPorSucursalChart"></div>
            </div>

            <!-- Gráfico de Top Productos -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 15 Productos</h3>
                <div id="topProductosChart"></div>
            </div>

            <!-- Gráfico de Distribución por Subgrupo -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Subgrupo</h3>
                <div id="distribucionVentasChart"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        // Escuchar evento de actualización de datos
        window.addEventListener('datos-actualizados', function() {
            setTimeout(() => {
                initializeCharts();
            }, 100);
        });

        function initializeCharts() {
            // Gráfico de Ventas por Mes
            const ventasPorMesData = @json($this->getVentasPorMesChart());
            const ventasPorMesChart = new ApexCharts(document.querySelector("#ventasPorMesChart"), {
                series: ventasPorMesData.series,
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: ventasPorMesData.categories
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return "S/ " + val.toLocaleString();
                        }
                    }
                },
                colors: ['#3B82F6'],
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                }
            });
            ventasPorMesChart.render();

            // Gráfico de Ventas por Sucursal
            const ventasPorSucursalData = @json($this->getVentasPorSucursalChart());
            const ventasPorSucursalChart = new ApexCharts(document.querySelector("#ventasPorSucursalChart"), {
                series: ventasPorSucursalData.series,
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: ventasPorSucursalData.labels,
                colors: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#F97316', '#84CC16', '#EC4899'],
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                }
            });
            ventasPorSucursalChart.render();

            // Gráfico de Top Productos
            const topProductosData = @json($this->getTopProductosChart());
            const topProductosChart = new ApexCharts(document.querySelector("#topProductosChart"), {
                series: topProductosData.series,
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: topProductosData.categories,
                    labels: {
                        formatter: function (val) {
                            return "S/ " + val.toLocaleString();
                        }
                    }
                },
                colors: ['#8B5CF6']
            });
            topProductosChart.render();

            // Gráfico de Distribución por Subgrupo
            const distribucionData = @json($this->getDistribucionVentasChart());
            const distribucionChart = new ApexCharts(document.querySelector("#distribucionVentasChart"), {
                series: distribucionData.series,
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: distribucionData.labels,
                colors: ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'],
                legend: {
                    position: 'bottom'
                }
            });
            distribucionChart.render();
        }
    </script>
    @endpush
</x-filament-panels::page>
