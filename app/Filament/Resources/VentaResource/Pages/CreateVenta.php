<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Services\FelCertificationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

        protected function mutateFormDataBeforeCreate(array $data): array
        {
            /**CUANDO YA SE TENGA LA TABLA DE RELACION DE VENTAS CON ESCENARIOS, LLENARLO ACA
         * ANTES DEL GUARDADO DE LA VENTA
         */
        
        // Validación para CF/cliente normal
            // Validación mutuamente excluyente
            if (empty($data['id_cliente']) && empty($data['consumidor_final'])) {
                Notification::make()
                    ->title('Se requiere cliente o consumidor final')
                    ->body('Debe seleccionar un cliente o especificar un consumidor final')
                    ->danger()
                    ->send();
                $this->halt();
            }
    
            // Limpiar campos según la selección
            if (!empty($data['consumidor_final'])) {
                $data['id_cliente'] = null;
                
                // Si el campo CF viene vacío (solo escribieron "CF"), poner "Consumidor Final"
                if (strtoupper(trim($data['consumidor_final'])) === 'CF') {
                    $data['consumidor_final'] = 'Consumidor Final';
                }
            } else {
                $data['consumidor_final'] = null;
            }
    
            // Validación FEL
            if ($data['tipoComprobante'] === '1' && !config('services.fel.emisor_id')) {
                Notification::make()
                    ->title('Error de configuración')
                    ->body('No se ha definido el ID del emisor en la configuración FEL.')
                    ->danger()
                    ->send();
                $this->halt();
            }
    
            $data['id_tipoDte'] = 1;
            $data['id_emisor'] = config('services.fel.emisor_id') ?? null;
    
            return $data;
        }
    
        protected function afterCreate(): void
        {
            if ($this->record->tipoComprobante === '1') {
                Log::info('Certificando factura FEL', ['venta' => $this->record]);
                $felService = new FelCertificationService();
    
                try {
                    $felService->certifyVenta($this->record);
                    Notification::make()
                        ->title('Factura FEL certificada exitosamente')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error al certificar factura FEL')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            }
        }
    // protected function handleRecordCreation(array $data): Model
    // {
    //     // $data['fecha'] = now()->format('Y-m-d');
    //     // return parent::handleRecordCreation($data);

    //     Log::info('Creando venta', ['data' => $data]);

    //     $items = $data['items'];

    //     Log::info('Items de la venta', ['items' => $items]);


    //     // $impuestos = collect($items)->map(function ($item) {
    //     //     return $item['impuestos'];
    //     // })->flatten();

    //     // Log::info('Impuestos de la venta', ['impuestos' => $impuestos]);

    //     $result = static::getModel()::create($data);

    //     Log::info('Venta creada', ['venta' => $result]);

    //     // //obtener los items de la venta
    //     // $items = $data['items'];

    //     // //obtener los impuestos de cada item
    //     // $impuestos = collect($items)->map(function ($item) {
    //     //     return $item['impuestos'];
    //     // })->flatten();

    //     return $result;
    // }

    // ========== NUEVO MÉTODO PARA MANEJO DE DATOS ==========
    protected function handleRecordCreation(array $data): Model
    {
        // Limpieza adicional de datos
        $data['id_cliente'] = $data['id_cliente'] ?? null;
        $data['consumidor_final'] = $data['consumidor_final'] ?? null;

        return parent::handleRecordCreation($data);
    }
   
}