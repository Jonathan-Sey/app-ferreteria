<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseInvoice extends CreateRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // mostrar data en el log
        // \Log::info($data);
        // Modifica los datos antes de crear
        // $data['descripcion'] = 'Probando sonido'; //por si se necesite modificar un campo antes de guardar
        return $data;
    }

    protected function mutateRelationshipDataBeforeCreate(array $data): array
    {
        // \Log::info($data);
        // static $lineNumber = 1;
        // $data['numerolinea'] = $lineNumber++;
        return $data;
    }
}
