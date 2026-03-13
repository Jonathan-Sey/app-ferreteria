<?php

namespace App\Filament\Resources\InventarioStockResource\Pages;

use App\Filament\Resources\InventarioStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInventarioStock extends CreateRecord
{
    protected static string $resource = InventarioStockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
