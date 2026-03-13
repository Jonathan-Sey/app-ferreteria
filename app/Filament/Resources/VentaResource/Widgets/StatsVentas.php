<?php

namespace App\Filament\Resources\VentaResource\Widgets;

use App\Filament\Resources\VentaResource\Pages\ListVentas;
use App\Models\Venta;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StatsVentas extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListVentas::class;
    }

    protected function getStats(): array
    {
        $orderData = Trend::model(Venta::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->dateColumn('fechahora_emision')
            ->perMonth()
            ->count();

        // Data específica para ventas con factura
        $facturasData = Trend::query(Venta::where('tipoComprobante', '1'))
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->dateColumn('fechahora_emision')
            ->perMonth()
            ->count();

        // Data específica para notas
        $notasData = Trend::query(Venta::where('tipoComprobante', '2'))
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->dateColumn('fechahora_emision')
            ->perMonth()
            ->count();


        $totalVentas = $this->getPageTableQuery()->with('items')->get()->sum('total');
        return [
            Stat::make('Ventas', $this->getPageTableQuery()->count())
                ->chart(
                    $orderData
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                ),
            Stat::make('Ventas con Factura', $this->getPageTableQuery()->whereIn('tipoComprobante', ['1'])->count())
                ->chart(
                    $facturasData
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                ),
            Stat::make('Ventas con Nota', $this->getPageTableQuery()->whereIn('tipoComprobante', ['2'])->count())
                ->chart(
                    $notasData
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                ),
            // Stat::make('Ventas', number_format($this->getPageTableQuery()->avg('total'), 2)),
            Stat::make('Total Ventas', 'Q.' . number_format($totalVentas, 2))
                ->description('Total acumulado de ventas')
                ->color('success'),

        ];
    }
}
