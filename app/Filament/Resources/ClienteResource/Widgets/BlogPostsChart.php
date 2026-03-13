<?php

namespace App\Filament\Resources\ClienteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Enums\FacturasStatus;
use Carbon\Carbon;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas Mensuales (Últimos 12 meses)';
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'bar'; // Puedes cambiar a 'line' si prefieres
    }

    protected function getData(): array
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subMonths(11)->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        // Obtener ventas agrupadas por mes
        $ventasPorMes = Venta::where('estado', FacturasStatus::Pagada->value)
            ->whereBetween('fechahora_emision', [$startDate, $endDate])
            ->selectRaw('
                DATE_FORMAT(fechahora_emision, "%Y-%m") as mes,
                SUM((SELECT SUM(total) FROM ventas_item WHERE id_venta = ventas.id)) as total
            ')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        // Preparar datos para los últimos 12 meses
        $labels = [];
        $data = [];
        $meses = [
            'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
        ];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $mesKey = $currentDate->format('Y-m');
            $nombreMes = $meses[$currentDate->month - 1] . ' ' . $currentDate->format('y');
            
            $labels[] = $nombreMes;
            $data[] = $ventasPorMes->has($mesKey) ? (float)$ventasPorMes[$mesKey]->total : 0;
            
            $currentDate->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventas mensuales (Q)',
                    'data' => $data,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#388E3C',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Q" + value.toLocaleString(); }'
                    ]
                ]
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "Q" + context.raw.toLocaleString(); }'
                    ]
                ]
            ]
        ];
    }
}