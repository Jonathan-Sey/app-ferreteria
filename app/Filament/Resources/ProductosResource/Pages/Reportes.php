<?php

namespace App\Filament\Resources\ProductosResource\Pages;

use App\Filament\Resources\ProductosResource;
use Filament\Resources\Pages\Page;

class Reportes extends Page
{
    protected static string $resource = ProductosResource::class;

    protected static string $view = 'filament.resources.productos-resource.pages.reportes';
}
