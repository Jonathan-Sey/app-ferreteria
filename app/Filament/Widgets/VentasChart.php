<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use App\Models\VentaItem;
use App\Enums\FacturasStatus;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class VentasChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas Diarias';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar'; // Puedes usar 'line', 'pie', etc.
    }

    protected function getData(): array
    {
        $now = Carbon::now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        
        $ventasPorDia = Venta::where('estado', FacturasStatus::Pagada->value)
            ->whereBetween('fechahora_emision', [$startDate, $endDate])
            ->selectRaw('DATE(fechahora_emision) as fecha, SUM((SELECT SUM(total) FROM ventas_item WHERE id_venta = ventas.id)) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        
        $labels = [];
        $data = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $fechaStr = $currentDate->format('Y-m-d');
            $ventaDia = $ventasPorDia->firstWhere('fecha', $fechaStr);
            
            $labels[] = $currentDate->format('d M');
            $data[] = $ventaDia ? (float)$ventaDia->total : 0;
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventas diarias (Q)',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#1d4ed8',
                ],
            ],
        ];
    }
}