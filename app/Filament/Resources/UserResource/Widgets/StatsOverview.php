<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaItem;
use Carbon\Carbon;
use App\Enums\FacturasStatus;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $sucursal = $user->sucursales()->first();
        $now = Carbon::now();
        $mesActual = $now->format('F Y');
        $mesPasado = $now->copy()->subMonth()->format('F Y');

        // Obtener IDs de ventas del mes actual
        $ventasMesActualIds = Venta::where('id_sucursal', $sucursal->id ?? null)
            ->where('estado', FacturasStatus::Pagada->value)
            ->whereMonth('fechahora_emision', $now->month)
            ->pluck('id');

        // Calcular total de ventas y items vendidos del mes actual
        $totalVentas = 0;
        $totalItemsVendidos = 0;
        
        if ($ventasMesActualIds->isNotEmpty()) {
            $totalVentas = VentaItem::whereIn('id_venta', $ventasMesActualIds)
                ->sum('total');
                
            $totalItemsVendidos = VentaItem::whereIn('id_venta', $ventasMesActualIds)
                ->sum('cantidad');
        }

        // Obtener IDs de ventas del mes pasado
        $ventasMesPasadoIds = Venta::where('id_sucursal', $sucursal->id ?? null)
            ->where('estado', FacturasStatus::Pagada->value)
            ->whereMonth('fechahora_emision', $now->copy()->subMonth()->month)
            ->pluck('id');

        // Calcular total de ventas del mes pasado
        $ventasMesPasadoTotal = 0;
        if ($ventasMesPasadoIds->isNotEmpty()) {
            $ventasMesPasadoTotal = VentaItem::whereIn('id_venta', $ventasMesPasadoIds)
                ->sum('total');
        }

        // Calcular diferencia porcentual
        $diferenciaVentas = $ventasMesPasadoTotal ? 
            round(($totalVentas - $ventasMesPasadoTotal) / $ventasMesPasadoTotal * 100, 2) : 0;

        // Obtener ventas con items para el producto destacado
        $ventasParaProductoDestacado = Venta::whereIn('id', $ventasMesActualIds)
            ->with('items.producto')
            ->get();

        return [
            Stat::make('Usuario Conectado', $user->getFilamentName())
                ->description($sucursal ? 'Sucursal: '.$sucursal->nombre : 'Sin sucursal asignada')
                ->icon('heroicon-o-user-circle')
                ->color('info'),
            
            Stat::make('Ventas ' . $mesActual, 'Q' . number_format($totalVentas, 2))
                ->description(($diferenciaVentas >= 0 ? '↑ ' : '↓ ') . abs($diferenciaVentas) . '% vs ' . $mesPasado)
                ->descriptionIcon($diferenciaVentas >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($diferenciaVentas >= 0 ? 'success' : 'danger'),
            
            Stat::make('Producto Destacado', $this->getTopProduct($ventasParaProductoDestacado))
                ->description('Más vendido este mes')
                ->icon('heroicon-s-star')
                ->color('warning'),
        ];
    }

    protected function getTopProduct($ventas): string
    {
        $productos = [];
        foreach ($ventas as $venta) {
            foreach ($venta->items as $item) {
                if ($item->producto) {
                    $productos[$item->producto->nombre] = ($productos[$item->producto->nombre] ?? 0) + $item->cantidad;
                }
            }
        }
        
        arsort($productos);
        return $productos ? array_key_first($productos) : 'N/A';
    }
}