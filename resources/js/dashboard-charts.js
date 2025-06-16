// import ApexCharts from "apexcharts"

// // Configuración global optimizada para ApexCharts
// window.ApexCharts = ApexCharts

// // Clase para manejar todos los gráficos del dashboard
// class DashboardCharts {
//   constructor() {
//     this.charts = {}
//     this.isInitialized = false
//     this.baseConfig = {
//       chart: {
//         fontFamily: "Inter, system-ui, sans-serif",
//         foreColor: "#6B7280",
//         animations: {
//           enabled: true,
//           easing: "easeinout",
//           speed: 400,
//           animateGradually: {
//             enabled: true,
//             delay: 150,
//           },
//         },
//         toolbar: {
//           show: true,
//           offsetX: 0,
//           offsetY: 0,
//           tools: {
//             download: true,
//             selection: true,
//             zoom: true,
//             zoomin: true,
//             zoomout: true,
//             pan: true,
//             reset: true,
//           },
//           export: {
//             csv: {
//               filename: undefined,
//               columnDelimiter: ",",
//               headerCategory: "category",
//               headerValue: "value",
//             },
//             svg: {
//               filename: undefined,
//             },
//             png: {
//               filename: undefined,
//             },
//           },
//         },
//         background: "transparent",
//       },
//       colors: ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6", "#EC4899", "#06B6D4", "#84CC16"],
//       grid: {
//         borderColor: "#E5E7EB",
//         strokeDashArray: 3,
//         xaxis: {
//           lines: {
//             show: true,
//           },
//         },
//         yaxis: {
//           lines: {
//             show: true,
//           },
//         },
//       },
//       tooltip: {
//         theme: "light",
//         style: {
//           fontSize: "12px",
//           fontFamily: "Inter, system-ui, sans-serif",
//         },
//         fillSeriesColor: false,
//         marker: {
//           show: true,
//         },
//       },
//       legend: {
//         fontSize: "12px",
//         fontFamily: "Inter, system-ui, sans-serif",
//         fontWeight: 400,
//         labels: {
//           colors: "#374151",
//         },
//       },
//     }
//   }

//   // Inicializar gráfico principal de líneas
//   initLineChart(data) {
//     console.log("🚀 Inicializando gráfico de líneas con datos:", data)

//     if (!data || !data.datosGraficoLineas || data.datosGraficoLineas.data.length === 0) {
//       console.log("⚠️ No hay datos para el gráfico de líneas")
//       this.showNoDataMessage("grafico-ventas")
//       return
//     }

//     this.destroyChart("lineChart")

//     try {
//       this.renderLineChartWithAnnotations(data.datosGraficoLineas, data.estadisticasPeriodo)
//       console.log("✅ Gráfico de líneas inicializado correctamente")
//     } catch (error) {
//       console.error("❌ Error inicializando gráfico de líneas:", error)
//     }
//   }

//   // Inicializar gráfico principal de múltiples empresas
//   initMultiLineChart(data) {
//     console.log("🚀 Inicializando gráfico multi-empresa con datos:", data)

//     if (!data || !data.datosGraficoEmpresas || data.datosGraficoEmpresas.series.length === 0) {
//       console.log("⚠️ No hay datos para el gráfico multi-empresa")
//       this.showNoDataMessage("grafico-ventas")
//       return
//     }

//     this.destroyChart("multiLineChart")

//     try {
//       this.renderMultiLineChart(data.datosGraficoEmpresas, data.estadisticasGenerales)
//       console.log("✅ Gráfico multi-empresa inicializado correctamente")
//     } catch (error) {
//       console.error("❌ Error inicializando gráfico multi-empresa:", error)
//     }
//   }

//   // Renderizar gráfico de líneas múltiples
//   renderMultiLineChart(datosGrafico, estadisticas) {
//     const element = document.querySelector("#grafico-ventas")
//     if (!element) {
//       console.warn("⚠️ Elemento #grafico-ventas no encontrado")
//       return
//     }

//     // Generar colores dinámicos para cada empresa
//     const colors = this.generateDynamicColors(datosGrafico.series.length)

//     const options = {
//       ...this.baseConfig,
//       chart: {
//         ...this.baseConfig.chart,
//         type: "line",
//         height: 500,
//         id: "multiLineChart",
//         zoom: {
//           enabled: true,
//           type: "x",
//           autoScaleYaxis: true,
//         },
//       },
//       series: datosGrafico.series,
//       xaxis: {
//         categories: datosGrafico.labels,
//         labels: {
//           style: {
//             fontSize: "12px",
//             colors: "#6B7280",
//           },
//         },
//         title: {
//           text: "Meses",
//           style: {
//             fontSize: "14px",
//             fontWeight: 600,
//             color: "#374151",
//           },
//         },
//       },
//       yaxis: {
//         labels: {
//           formatter: (val) => {
//             if (val >= 1000000) {
//               return "S/ " + (val / 1000000).toFixed(1) + "M"
//             } else if (val >= 1000) {
//               return "S/ " + (val / 1000).toFixed(0) + "K"
//             }
//             return "S/ " + val.toFixed(0)
//           },
//           style: {
//             colors: "#6B7280",
//           },
//         },
//         title: {
//           text: "Ventas (S/)",
//           style: {
//             fontSize: "14px",
//             fontWeight: 600,
//             color: "#374151",
//           },
//         },
//       },
//       stroke: {
//         curve: "smooth",
//         width: 3,
//         lineCap: "round",
//       },
//       markers: {
//         size: 5,
//         strokeWidth: 2,
//         strokeColors: "#fff",
//         hover: {
//           size: 7,
//         },
//       },
//       colors: colors,
//       tooltip: {
//         shared: true,
//         intersect: false,
//         y: {
//           formatter: (val) =>
//             "S/ " +
//             val.toLocaleString("es-PE", {
//               minimumFractionDigits: 2,
//               maximumFractionDigits: 2,
//             }),
//         },
//       },
//       legend: {
//         show: true,
//         position: "top",
//         horizontalAlign: "center",
//         offsetY: -10,
//         itemMargin: {
//           horizontal: 10,
//           vertical: 5,
//         },
//       },
//       title: {
//         text: `Evolución de Ventas por Empresa`,
//         align: "left",
//         style: {
//           fontSize: "18px",
//           fontWeight: 600,
//           color: "#1F2937",
//         },
//       },
//       subtitle: {
//         text: `${datosGrafico.total_empresas} empresas | Total: S/ ${estadisticas.total_ventas?.toLocaleString("es-PE") || "0"}`,
//         align: "left",
//         style: {
//           fontSize: "14px",
//           color: "#6B7280",
//         },
//       },
//       annotations: this.generateGeneralAnnotations(estadisticas),
//     }

//     this.charts.multiLineChart = new ApexCharts(element, options)
//     this.charts.multiLineChart.render()
//     console.log("✅ Gráfico multi-empresa renderizado")
//   }

//   // Renderizar gráfico de líneas con anotaciones
//   renderLineChartWithAnnotations(datosGrafico, estadisticas) {
//     const element = document.querySelector("#grafico-ventas")
//     if (!element) {
//       console.warn("⚠️ Elemento #grafico-ventas no encontrado")
//       return
//     }

//     // Calcular anotaciones dinámicas
//     const annotations = this.generateAnnotations(datosGrafico.data, datosGrafico.labels, estadisticas)

//     const options = {
//       ...this.baseConfig,
//       chart: {
//         ...this.baseConfig.chart,
//         type: "line",
//         height: 450,
//         id: "lineChart",
//         zoom: {
//           enabled: true,
//           type: "x",
//           autoScaleYaxis: true,
//         },
//       },
//       series: [
//         {
//           name: `Ventas - ${datosGrafico.empresa}`,
//           data: datosGrafico.data,
//         },
//       ],
//       xaxis: {
//         categories: datosGrafico.labels,
//         labels: {
//           style: {
//             fontSize: "12px",
//             colors: "#6B7280",
//           },
//         },
//         title: {
//           text: "Meses",
//           style: {
//             fontSize: "14px",
//             fontWeight: 600,
//             color: "#374151",
//           },
//         },
//       },
//       yaxis: {
//         labels: {
//           formatter: (val) => {
//             if (val >= 1000000) {
//               return "S/ " + (val / 1000000).toFixed(1) + "M"
//             } else if (val >= 1000) {
//               return "S/ " + (val / 1000).toFixed(0) + "K"
//             }
//             return "S/ " + val.toFixed(0)
//           },
//           style: {
//             colors: "#6B7280",
//           },
//         },
//         title: {
//           text: "Ventas (S/)",
//           style: {
//             fontSize: "14px",
//             fontWeight: 600,
//             color: "#374151",
//           },
//         },
//       },
//       stroke: {
//         curve: "smooth",
//         width: 4,
//         lineCap: "round",
//       },
//       markers: {
//         size: 6,
//         strokeWidth: 3,
//         strokeColors: "#fff",
//         hover: {
//           size: 8,
//         },
//         discrete: this.generateMarkerAnnotations(datosGrafico.data, estadisticas),
//       },
//       fill: {
//         type: "gradient",
//         gradient: {
//           shade: "light",
//           type: "vertical",
//           shadeIntensity: 0.5,
//           gradientToColors: ["#60A5FA"],
//           inverseColors: false,
//           opacityFrom: 0.8,
//           opacityTo: 0.1,
//           stops: [0, 100],
//         },
//       },
//       tooltip: {
//         y: {
//           formatter: (val) =>
//             "S/ " +
//             val.toLocaleString("es-PE", {
//               minimumFractionDigits: 2,
//               maximumFractionDigits: 2,
//             }),
//         },
//         custom: ({ series, seriesIndex, dataPointIndex, w }) => {
//           const value = series[seriesIndex][dataPointIndex]
//           const label = w.globals.labels[dataPointIndex]

//           return `
//             <div class="px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-lg">
//               <div class="font-semibold text-gray-800">${label}</div>
//               <div class="text-blue-600 font-bold">S/ ${value.toLocaleString("es-PE", {
//                 minimumFractionDigits: 2,
//                 maximumFractionDigits: 2,
//               })}</div>
//             </div>
//           `
//         },
//       },
//       annotations: annotations,
//       legend: {
//         show: true,
//         position: "top",
//         horizontalAlign: "right",
//         offsetY: -10,
//       },
//       title: {
//         text: `Evolución de Ventas - ${datosGrafico.empresa}`,
//         align: "left",
//         style: {
//           fontSize: "18px",
//           fontWeight: 600,
//           color: "#1F2937",
//         },
//       },
//       subtitle: {
//         text: `Período: ${new Date().getFullYear()} | Total: S/ ${estadisticas.total_ventas?.toLocaleString("es-PE") || "0"}`,
//         align: "left",
//         style: {
//           fontSize: "14px",
//           color: "#6B7280",
//         },
//       },
//     }

//     this.charts.lineChart = new ApexCharts(element, options)
//     this.charts.lineChart.render()
//     console.log("✅ Gráfico de líneas con anotaciones renderizado")
//   }

//   // Generar colores dinámicos
//   generateDynamicColors(count) {
//     const baseColors = [
//       "#3B82F6",
//       "#10B981",
//       "#F59E0B",
//       "#EF4444",
//       "#8B5CF6",
//       "#EC4899",
//       "#06B6D4",
//       "#84CC16",
//       "#F97316",
//       "#6366F1",
//     ]

//     const colors = []
//     for (let i = 0; i < count; i++) {
//       colors.push(baseColors[i % baseColors.length])
//     }

//     return colors
//   }

//   // Generar anotaciones generales
//   generateGeneralAnnotations(estadisticas) {
//     const annotations = {
//       yaxis: [],
//     }

//     if (!estadisticas || estadisticas.promedio_mensual <= 0) return annotations

//     // Línea de promedio general
//     annotations.yaxis.push({
//       y: estadisticas.promedio_mensual,
//       borderColor: "#F59E0B",
//       borderWidth: 2,
//       strokeDashArray: 5,
//       label: {
//         text: `Promedio: S/ ${estadisticas.promedio_mensual.toLocaleString("es-PE")}`,
//         style: {
//           color: "#F59E0B",
//           background: "#FEF3C7",
//           fontSize: "12px",
//           fontWeight: 600,
//         },
//       },
//     })

//     return annotations
//   }

//   // Generar anotaciones dinámicas
//   generateAnnotations(data, labels, estadisticas) {
//     const annotations = {
//       yaxis: [],
//       xaxis: [],
//       points: [],
//     }

//     if (!estadisticas || data.length === 0) return annotations

//     // Línea de promedio
//     if (estadisticas.promedio_mensual > 0) {
//       annotations.yaxis.push({
//         y: estadisticas.promedio_mensual,
//         borderColor: "#F59E0B",
//         borderWidth: 2,
//         strokeDashArray: 5,
//         label: {
//           text: `Promedio: S/ ${estadisticas.promedio_mensual.toLocaleString("es-PE")}`,
//           style: {
//             color: "#F59E0B",
//             background: "#FEF3C7",
//             fontSize: "12px",
//             fontWeight: 600,
//           },
//         },
//       })
//     }

//     // Encontrar el mes con mayor venta
//     const maxValue = Math.max(...data)
//     const maxIndex = data.indexOf(maxValue)

//     if (maxIndex !== -1 && maxValue > 0) {
//       annotations.points.push({
//         x: labels[maxIndex],
//         y: maxValue,
//         marker: {
//           size: 8,
//           fillColor: "#EF4444",
//           strokeColor: "#fff",
//           strokeWidth: 3,
//         },
//         label: {
//           text: "Pico de ventas",
//           style: {
//             color: "#fff",
//             background: "#EF4444",
//             fontSize: "11px",
//             fontWeight: 600,
//           },
//           offsetY: -15,
//         },
//       })
//     }

//     // Encontrar el mes con menor venta (excluyendo ceros)
//     const nonZeroData = data.filter((val) => val > 0)
//     if (nonZeroData.length > 0) {
//       const minValue = Math.min(...nonZeroData)
//       const minIndex = data.indexOf(minValue)

//       if (minIndex !== -1 && minValue !== maxValue) {
//         annotations.points.push({
//           x: labels[minIndex],
//           y: minValue,
//           marker: {
//             size: 8,
//             fillColor: "#10B981",
//             strokeColor: "#fff",
//             strokeWidth: 3,
//           },
//           label: {
//             text: "Menor venta",
//             style: {
//               color: "#fff",
//               background: "#10B981",
//               fontSize: "11px",
//               fontWeight: 600,
//             },
//             offsetY: 15,
//           },
//         })
//       }
//     }

//     return annotations
//   }

//   // Generar marcadores especiales
//   generateMarkerAnnotations(data, estadisticas) {
//     const markers = []

//     if (!estadisticas || data.length === 0) return markers

//     // Marcar el punto más alto
//     const maxValue = Math.max(...data)
//     const maxIndex = data.indexOf(maxValue)

//     if (maxIndex !== -1) {
//       markers.push({
//         seriesIndex: 0,
//         dataPointIndex: maxIndex,
//         fillColor: "#EF4444",
//         strokeColor: "#fff",
//         size: 10,
//       })
//     }

//     return markers
//   }

//   // Mostrar mensaje cuando no hay datos
//   showNoDataMessage(elementId) {
//     const element = document.getElementById(elementId)
//     if (element) {
//       element.innerHTML = `
//         <div class="flex flex-col items-center justify-center h-64 text-gray-500">
//           <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
//           </svg>
//           <p class="text-lg font-medium">No hay datos disponibles</p>
//           <p class="text-sm">Seleccione filtros y presione "Buscar Datos"</p>
//         </div>
//       `
//     }
//   }

//   // Destruir gráfico específico
//   destroyChart(chartKey) {
//     if (this.charts[chartKey] && typeof this.charts[chartKey].destroy === "function") {
//       this.charts[chartKey].destroy()
//       delete this.charts[chartKey]
//     }
//   }

//   // Destruir todos los gráficos
//   destroyAll() {
//     Object.values(this.charts).forEach((chart) => {
//       if (chart && typeof chart.destroy === "function") {
//         chart.destroy()
//       }
//     })
//     this.charts = {}
//     console.log("🗑️ Todos los gráficos destruidos")
//   }

//   // Exportar gráfico específico
//   exportChart(chartKey, format = "png") {
//     const chart = this.charts[chartKey]
//     if (chart) {
//       chart.dataURI().then(({ imgURI }) => {
//         const link = document.createElement("a")
//         link.href = imgURI
//         link.download = `${chartKey}_${new Date().toISOString().split("T")[0]}.${format}`
//         link.click()
//       })
//     }
//   }
// }

// // Instancia global
// window.dashboardCharts = new DashboardCharts()

// // Funciones globales para los botones
// window.exportarGrafico = (chartId) => {
//   window.dashboardCharts.exportChart("lineChart")
// }

// window.toggleFullscreen = (chartId) => {
//   const element = document.getElementById(chartId)
//   if (element && element.requestFullscreen) {
//     element.requestFullscreen()
//   }
// }

// window.actualizarGrafico = () => {
//   if (window.Livewire) {
//     window.Livewire.dispatch("buscarDatos")
//   }
// }

// console.log("📊 Dashboard Charts module loaded")
