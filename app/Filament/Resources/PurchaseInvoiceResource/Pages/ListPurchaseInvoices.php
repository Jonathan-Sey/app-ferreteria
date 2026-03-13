<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

use Filament\Resources\Pages\ListRecords;

class ListPurchaseInvoices extends ListRecords
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Crear Compra')
            ->icon('heroicon-o-plus'),
            Action::make('libroCompras')
                ->label('Libro de Compras')
                ->color('secondary')
                ->icon('heroicon-o-book-open')
                ->form([   
                    Select::make('sucursal')
                        ->label('Sucursal:')
                        ->relationship('sucursal', 'nombre')
                        ->default('3')
                        ->preload() // Carga las opciones dinámicamente
                        ->searchable()
                        ->required(), // Permite buscar entre las opciones 
                    Select::make('tipo')
                        ->label('Tipo:')
                        ->options([
                            'bien' => 'Bien',
                            'servicio' => 'Servicio',
                        ])
                        ->default('bien')
                        ->preload() // Carga las opciones dinámicamente
                        ->required(), // Permite buscar entre las opciones                  
                    DatePicker::make('fecha_inicio')
                        ->default(now()->startOfMonth())
                        ->label('Fecha de Inicio'),
                        
                    DatePicker::make('fecha_fin')
                        ->default(now()->endOfMonth())
                        ->label('Fecha de Fin'),
                    
                ])
                ->action(function (array $data){
                    return redirect()->route('libro.compras.pdf', $data);
                })

            
        ];
    }
}
