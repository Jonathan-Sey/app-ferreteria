<?php

namespace App\Filament\Resources\InventarioStockResource\Pages;

use App\Filament\Resources\InventarioStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventarioStocks extends ListRecords
{
    protected static string $resource = InventarioStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            
        ];
    }
}
