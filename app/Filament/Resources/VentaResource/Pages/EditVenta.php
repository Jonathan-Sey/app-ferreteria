<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenta extends EditRecord
{
    protected static string $resource = VentaResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterUpdate(): void
    {
        // // Asumiendo que 'tipoComprobante' indica si es FEL o no
        // if ($this->record->tipoComprobante === '1') {
        //     Log::info('Certificando factura FEL', ['venta' => $this->record]);
        //     $felService = new FelCertificationService();

        //     try {
        //         $felService->certifyVenta($this->record);
        //         Notification::make()
        //             ->title('Factura FEL certificada exitosamente')
        //             ->success()
        //             ->send();
        //     } catch (\Exception $e) {
        //         Notification::make()
        //             ->title('Error al certificar factura FEL')
        //             ->body($e->getMessage())
        //             ->danger()
        //             ->send();
        //     }
        // }
    }
}
