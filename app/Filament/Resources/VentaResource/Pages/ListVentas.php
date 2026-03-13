<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListVentas extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Venta')
                ->Icon('heroicon-o-plus'),

            
            // Botón "Libro de Ventas" - Solo PDF
            Action::make('libroVentas')
                ->label('Libro de Ventas')
                ->icon('heroicon-o-book-open')
                ->color('secondary')
                ->modalWidth('xl')
                ->modalHeading('Generar Libro de Ventas - PDF')
                ->modalDescription('Seleccione los filtros para generar el libro de ventas en PDF')
                ->form([
                    \Filament\Forms\Components\Select::make('sucursal')
                        ->label('Sucursal')
                        ->options(\App\Models\Sucursal::pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha Inicio')
                        ->default(now()->startOfMonth())
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('fecha_fin')
                        ->label('Fecha Fin')
                        ->default(now()->endOfMonth())
                        ->required(),
                    \Filament\Forms\Components\Select::make('tipo_producto')
                        ->label('Tipo de Producto')
                        ->options([
                            'bien' => 'Solo Bienes',
                            'servicio' => 'Solo Servicios',
                            'todos' => 'Todos (Bienes y Servicios)'
                        ])
                        ->default('todos')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Generar PDF')
                ->action(function (array $data) {
                    return redirect()->route('libro.ventas.pdf', [
                        'fecha_inicio' => $data['fecha_inicio'],
                        'fecha_fin' => $data['fecha_fin'],
                        'sucursal' => $data['sucursal'],
                        'tipo_producto' => $data['tipo_producto']
                    ]);
                }),
            
            // Botón "Corte de Caja" (actualizado)
            Action::make('corteCaja')
                ->label('Corte de Caja')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalHeading('Generar Reporte de Corte de Caja')
                ->modalDescription('Selecciona un rango de fechas y opcionalmente un vendedor para generar el reporte de corte de caja.')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->default(now())
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->default(now())
                        ->required(),
                    \Filament\Forms\Components\Select::make('sucursal')
                        ->label('Sucursal')
                        ->options(function () {
                            return \App\Models\Sucursal::pluck('nombre', 'id');
                        })
                        ->nullable(),
                    \Filament\Forms\Components\Select::make('vendedor')
                        ->label('Vendedor')
                        ->relationship('creador', 'nombre1') // Ajusta según el modelo y relación
                        ->preload()
                        ->searchable()
                        ->nullable(), // Hace que el campo sea opcional
                ])
                ->action(function (array $data) {
                    return redirect()->route('corte.caja.pdf', [
                        'fecha_inicio' => $data['fecha_inicio'],
                        'fecha_fin' => $data['fecha_fin'],
                        'sucursal' => $data['sucursal'],
                        'vendedor' => $data['vendedor'] ?? null, // Si no se selecciona vendedor, pasa null
                    ]);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return VentaResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Todas')->query(fn ($query) => $query->where('estado', '!=', '0')),
            'pendientes' => Tab::make()->query(fn ($query) => $query->where('estado', '1')),
            'pagadas' => Tab::make()->query(fn ($query) => $query->where('estado', '2')),
            'canceladas' => Tab::make()->query(fn ($query) => $query->where('estado', '3')),
            'devueltas' => Tab::make()->query(fn ($query) => $query->where('estado', '4')),
        ];
    }
}