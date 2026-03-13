<?php

namespace App\Filament\Resources\ProductosResource\Pages;

use App\Filament\Resources\ProductosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductos extends CreateRecord
{
    protected static string $resource = ProductosResource::class;
    // protected function handleRecordCreation(array $data): Model
    // {
    //     // Extraer los impuestos del array
    //     $impuestos = $data['impuestos'] ?? [];
    //     unset($data['impuestos']);
        
    //     // Crear el producto
    //     $producto = parent::handleRecordCreation($data);
        
    //     // Asociar los impuestos al producto
    //     foreach ($impuestos as $impuesto) {
    //         $producto->impuestos()->attach($impuesto['id_impuesto'], [
    //             // 'tipoId' => $impuesto['tipoId']
    //         ]);
    //     }
        
    //     return $producto;
    // }
}
