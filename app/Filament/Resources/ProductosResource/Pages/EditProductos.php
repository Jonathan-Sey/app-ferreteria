<?php

namespace App\Filament\Resources\ProductosResource\Pages;

use App\Filament\Resources\ProductosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductos extends EditRecord
{
    protected static string $resource = ProductosResource::class;

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     // Obtener los impuestos existentes
    //     $impuestos = $this->record->impuestos->map(function ($impuesto) {
    //         return [
    //             'id_impuesto' => $impuesto->id,
    //             // 'tipoId' => $impuesto->pivot->tipoId
    //         ];
    //     })->toArray();
        
    //     $data['impuestos'] = $impuestos;
        
    //     return $data;
    // }

    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    //     // Extraer los impuestos del array
    //     $impuestos = $data['impuestos'] ?? [];
    //     unset($data['impuestos']);
        
    //     // Actualizar el producto
    //     $record->update($data);
        
    //     // Eliminar todos los impuestos existentes
    //     $record->impuestos()->detach();
        
    //     // Asociar los nuevos impuestos
    //     foreach ($impuestos as $impuesto) {
    //         $record->impuestos()->attach($impuesto['id_impuesto'], [
    //             // 'tipoId' => $impuesto['tipoId']
    //         ]);
    //     }
        
    //     return $record;
    // }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
