<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteUtilidadesResource\Pages;
use App\Filament\Resources\ReporteUtilidadesResource\RelationManagers;
//use App\Models\ReporteUtilidades;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Venta;
use App\Models\VentaItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;


use Filament\Tables\Columns\TextColumn;



class ReporteUtilidadesResource extends Resource
{
    protected static ?string $model = VentaItem::class;
    protected static ?string $modelLabel = 'Reporte de utilidades';
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static string $title = 'Reporte de Utilidades';
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('venta.id')
                ->sortable()
                ->label('# Venta'),
                TextColumn::make('producto.nombre')->sortable(),
                TextColumn::make('venta.fechahora_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->sortable()
                    ->numeric()
                    ->alignCenter()
                    ->label('Cantidad'),
                TextColumn::make('producto.precio_venta')
                    ->label('Precio de Venta')
                    ->money('GTQ')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('producto.precio_compra')
                    ->label('Precio de Compra')
                    ->money('GTQ')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('ganancia')
                    ->label('Ganancia')
                    ->money('GTQ')
                    ->alignCenter()
                    ->sortable()
                    ->state(function (VentaItem $record) {
                        if (!$record->producto) return 0;
                        
                        $precioVenta = $record->precio_unitario ?? 0;
                        $precioCompra = $record->producto->precio_compra ?? 0;
                        
                        return ($precioVenta - $precioCompra);
                    }),
                    

                TextColumn::make('totalventa')
                    ->label('Total de Venta')
                    ->money('GTQ')
                    ->alignCenter()
                    ->state(fn (VentaItem $record) => $record->precio_unitario * $record->cantidad),
                TextColumn::make('totalcompra')
                    ->label('Total de Compra')
                    ->money('GTQ')
                    ->alignCenter()
                    ->state(fn (VentaItem $record) => $record->producto->precio_compra * $record->cantidad),

                TextColumn::make('ganaciabruta')
                    ->label('Ganancia Bruta')
                    ->money('GTQ')
                    ->alignCenter()
                    ->state(function (VentaItem $record) {
                        if (!$record->producto) return 0;
                        
                        $precioVenta = $record->precio_unitario ?? 0;
                        $precioCompra = $record->producto->precio_compra ?? 0;
                        
                        return ($precioVenta - $precioCompra) * $record->cantidad;
                    }),

                TextColumn::make('ganaciaporciento')
                    ->label('Ganancia %')
                    ->suffix('%')
                    ->state(function ($record) {
                        $ganancia = $record->precio_unitario - $record->producto->precio_compra;
                        return round(($ganancia / $record->precio_unitario) * 100, 2);
                    }),
                    
            ])
            ->defaultSort('id', 'desc')

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-document-text')
                    ->label('PDF')
                    ->color('danger')
                    ->form([
                        Select::make('sucursal')
                            ->label('Sucursal:')
                            ->relationship('venta.sucursal', 'nombre')
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable()
                            ->required(), // Permite buscar entre las opciones
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->default(now()->startOfMonth()),
                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->default(now()->endOfMonth()),
                    ])
                    ->action(function (array $data) {
                        // Redirigir al controlador con los datos del formulario
                        return redirect()->route('reporte.utilidades.pdf', $data);
                    })
            ])
            ->actions([
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReporteUtilidades::route('/'),
        ];
    }
}
