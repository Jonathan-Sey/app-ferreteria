<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use App\Models\Producto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('proueba', Producto::query()->where('id_categorias', '1')->count()),
            Stat::make('Dogs', Producto::query()->where('id_categorias', '2')->count()),
            Stat::make('Rabbits', Producto::query()->where('id_categorias', '3')->count()),
        ];
    }
}
