<x-filament::page>
    <div class="space-y-4">
        {{-- Filtros --}}
        <div>
            {{ $this->form }}
        </div>

        {{-- Contenido dinámico --}}
        @php
            $data = DB::select('EXEC sp_kpi_atenciones_por_mes ?, ?, ?, ?', [
                $periodo,
                $empresa,
                $sucursal,
                $subgrupo,
            ]);

            $labels = collect($data)->pluck('MES');
            $valores = collect($data)->pluck('cantidad_ventas');

            $canvasId = 'chart-' . now()->timestamp; // genera un ID único para forzar redibujo
        @endphp

        <div wire:key="{{ $canvasId }}">
            <canvas
                x-data="{
                    chart: null,
                    init() {
                        this.chart = new Chart(this.$el.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: @js($labels),
                                datasets: [{
                                    label: 'Atenciones por Mes',
                                    data: @js($valores),
                                    backgroundColor: '#B11A1A',
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: true }
                                }
                            }
                        });
                    }
                }"
                style="height: 300px;"
            ></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
</x-filament::page>
