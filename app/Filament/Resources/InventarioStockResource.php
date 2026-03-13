<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioStockResource\Pages;
use App\Filament\Resources\InventarioStockResource\RelationManagers;
use App\Models\InventarioStock;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventarioFisicoExport;
use App\Models\Sucursal;

class InventarioStockResource extends Resource
{
    protected static ?string $model = InventarioStock::class;

    protected static ?string $navigationIcon = 'custom-package';
    protected static ?int $navigationSort = 6;


    //protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('id_producto')
                                    ->relationship('producto', 'nombre')
                                    ->required()
                                    ->label('Producto'),
                                Select::make('id_sucursal')
                                    ->relationship('sucursal', 'nombre')
                                    ->required()
                                    ->label('Sucursal'),
                                TextInput::make('cantidad_actual')
                                    ->numeric()
                                    ->required()
                                    ->label('Cantidad Actual'),
                                TextInput::make('stock_minimo')
                                    ->numeric()
                                    ->required()
                                    ->label('Stock Mínimo'),
                                TextInput::make('ubicacion')
                                    ->required()
                                    ->label('Ubicación'),
                                TextInput::make('estado')
                                    ->required()
                                    ->label('Estado'),
                            ]),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->columnSpan('full'),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('cantidad_actual')
                    ->label('Cantidad Actual')
                    ->sortable()
                    ->colors([
                        'danger' => fn($state) => $state < 10,
                        'warning' => fn($state) => $state >= 10 && $state < 50,
                        'success' => fn($state) => $state >= 50,
                    ]),
                TextColumn::make('stock_minimo')
                    ->label('Stock Mínimo')
                    ->sortable(),
                TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('producto')
                    ->relationship('producto', 'nombre')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->label('Producto'),
                SelectFilter::make('sucursal')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('sucursal', 'nombre')
                    ->label('Sucursal'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('pedido')
                    ->label('Pedido')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('success')
                    ->outlined()
                    ->form([
                        Select::make('sucursal')
                            ->label('Sucursal: ')
                            ->options(function () {
                                return DB::table('inventario_stock')
                                    ->join('sucursales', 'inventario_stock.id_sucursal', '=', 'sucursales.id')
                                    ->pluck('sucursales.nombre', 'sucursales.id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required(),

                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->rule('numeric')
                            ->minValue(1) // Valor mínimo de 1
                            ->maxValue(9999) // Valor máximo de 10000
                            ->default(10) // Valor por defecto de 1
                            ->required(),


                        Select::make('marca')
                            ->label('Marca por: ')
                            ->relationship('producto.marca', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        Select::make('categoria')
                            ->label('Categoria por: ')
                            ->relationship('producto.categoria', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones
                    ])
                    ->action(function (array $data) {
                        // Lógica para manejar los datos del formulario
                        // Por ejemplo, puedes aplicar los filtros aquí
                        //Log::info('Datos del filtro:', $data);
                        $response = redirect()->route('pedido.pdf', $data);
                        if (session('error')) {
                            Notification::make()
                                ->title(session('error'))
                                ->danger()
                                ->send();
                        }

                        return $response;
                    })
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('kardex')
                    ->label('Kardex')
                    ->icon('heroicon-o-document-text')
                    ->form([
                        Select::make('producto')
                            ->label('Producto: ')
                            ->searchable()
                            ->options(function () {
                                return DB::table('inventario_stock')
                                    ->join('productos', 'inventario_stock.id_producto', '=', 'productos.id')
                                    ->pluck('productos.nombre', 'productos.id')
                                    ->toArray();
                            })
                            ->required(),
                        Select::make('sucursal')
                            ->label('Sucursal: ')
                            ->options(function () {
                                return DB::table('inventario_stock')
                                    ->join('sucursales', 'inventario_stock.id_sucursal', '=', 'sucursales.id')
                                    ->pluck('sucursales.nombre', 'sucursales.id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required(),
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio'),
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),
                    ])
                    ->action(function (array $data) {
                        $response = redirect()->route('inventario.stocks.kardex', $data);
                        if (session('error')) {
                            Notification::make()
                                ->title(session('error'))
                                ->danger()
                                ->send();
                        }
                        return $response;
                    })
                    ->modalActions([
                        Tables\Actions\Action::make('Descargar kardex')
                            ->label('Descargar PDF')
                            ->icon('heroicon-o-document-text')
                            ->color('danger')
                            ->submit('submit'),
                        Tables\Actions\Action::make('Cancelar')
                            ->color('secondary')
                            ->close(),
                    ]),

                // Nueva acción para descargar el inventario físico en Excel
                Tables\Actions\Action::make('Descargar Inventario Fisico')
                    ->label('Descargar Inventario Físico')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->nullable(), // No es obligatorio
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->nullable(), // No es obligatorio
                        Select::make('sucursal')
                            ->label('Sucursal')
                            ->options(Sucursal::all()->pluck('nombre', 'id'))
                            ->nullable(), // No es obligatorio
                    ])
                    ->action(function (array $data) {
                        // Obtener los datos filtrados
                        $fechaInicio = $data['fecha_inicio'];
                        $fechaFin = $data['fecha_fin'];
                        $sucursalId = $data['sucursal'];

                        // Consulta base
                        $query = InventarioStock::query()
                            ->with(['producto', 'sucursal']);

                        // Filtrar por fecha si se proporciona
                        if ($fechaInicio && $fechaFin) {
                            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                        }

                        // Filtrar por sucursal si se selecciona
                        if ($sucursalId) {
                            $query->where('id_sucursal', $sucursalId);
                        }

                        // Obtener los datos filtrados
                        $inventarioData = $query->get()
                            ->groupBy('id_sucursal')
                            ->map(function ($stocks, $sucursalId) {
                                $sucursalNombre = $stocks->first()->sucursal->nombre;
                                $productos = $stocks->map(function ($stock) {
                                    return [
                                        'codigo' => $stock->producto->codigo,
                                        'nombre' => $stock->producto->nombre,
                                        'computo' => $stock->cantidad_actual,
                                        'conteo_fi' => '',
                                        'diferencia' => '',
                                        'comentario' => '',
                                    ];
                                });

                                return [
                                    'sucursal' => $sucursalNombre,
                                    'productos' => $productos,
                                ];
                            })
                            ->values()
                            ->toArray();

                        // Generar el reporte de Excel
                        return Excel::download(new InventarioFisicoExport($inventarioData), 'inventario_fisico.xlsx');
                    })
                    ->modalActions([
                        Tables\Actions\Action::make('Generar')
                            ->label('Generar')
                            ->submit('submit')
                            ->color('primary'),
                        Tables\Actions\Action::make('Cancelar')
                            ->label('Cancelar')
                            ->color('secondary')
                            ->close(),
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Tables\Actions\Action::make("kardexind")
                    ->icon('heroicon-o-clipboard-document-list')
                    ->label('Kardex')
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->default(now()->startOfMonth()),

                            

                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->default(now()->endOfMonth()),


                    ])
                    ->action(function (array $data, $record) {
                        $response = redirect()->route('inventario.individual.kardex', [
                            'record' => $record->id,
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_fin' => $data['fecha_fin'],
                        ]);
                        if (session('error')) {
                            Notification::make()
                                ->title(session('error'))
                                ->danger()
                                ->send();
                        }
                        return $response;
                    })
                    ->modalActions([
                        Tables\Actions\Action::make('Descargar kardex')
                            ->label('Descargar PDF')
                            ->icon('heroicon-o-document-text')
                            ->color('danger')
                            ->submit('submit'),
                        Tables\Actions\Action::make('Cancelar')
                            ->color('secondary')
                            ->close(),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getInventarioFisicoData()
    {
        // Obtener los datos de la base de datos agrupados por sucursal
        return \App\Models\InventarioStock::query()
            ->with(['producto', 'sucursal']) // Cargar las relaciones con producto y sucursal
            ->get()
            ->groupBy('id_sucursal') // Agrupar por sucursal
            ->map(function ($stocks, $sucursalId) {
                // Obtener el nombre de la sucursal
                $sucursalNombre = $stocks->first()->sucursal->nombre;

                // Mapear los productos de la sucursal
                $productos = $stocks->map(function ($stock) {
                    return [
                        'codigo' => $stock->producto->codigo,
                        'nombre' => $stock->producto->nombre,
                        'computo' => $stock->cantidad_actual, // Cantidad actual del inventario
                        'conteo_fi' => '', // Columna vacía
                        'diferencia' => '', // Columna vacía
                        'comentario' => '', // Columna vacía
                    ];
                });

                // Retornar los datos de la sucursal
                return [
                    'sucursal' => $sucursalNombre,
                    'productos' => $productos,
                ];
            })
            ->values() // Reiniciar las claves del array
            ->toArray();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarioStocks::route('/'),
            'edit' => Pages\EditInventarioStock::route('/{record}/edit'),
        ];
    }
}