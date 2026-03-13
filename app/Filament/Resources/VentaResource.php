<?php

namespace App\Filament\Resources;

use App\Enums\FacturasStatus;
use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Filament\Resources\VentaResource\Widgets\StatsVentas;
use App\Models\Certificador;
use App\Models\Producto;
use App\Models\TipoDte;
use App\Models\Venta;
use App\Models\Entidad;
use App\Models\Sucursal;
use App\Models\MetodoPago;
use App\Models\InventarioStock;
use App\Services\FelCertificationService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class VentaResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'custom-shopping-bag';

    protected static ?int $navigationSort = 3;

    public static function calculateTotals($state, $set, $get)
    {
        // Calcula totales del item actual
        $cantidad = $get('cantidad') ?? 0;
        $precio_unitario = $get('precio_unitario') ?? 0;
        $descuento = $get('descuento') ?? 0;

        $precio_parcial = $cantidad * $precio_unitario;
        $descuento = is_numeric($descuento) ? $descuento : 0;
        $total_item = $precio_parcial - $descuento;

        // Actualizar valores del item
        $set('precio_parcial', $precio_parcial);
        $set('total', $total_item);

        // Obtener y calcular totales generales
        $items = $get('../../items') ?? [];
        $total_general = array_reduce($items, function ($carry, $item) {
            return $carry + ($item['total'] ?? 0);
        }, 0);

        $descuentos_general = array_reduce($items, function ($carry, $item) {
            return $carry + (float)($item['descuento'] ?? 0);
        }, 0);

        // Actualizar totales generales
        $set('../../total_sum', $total_general);
        $set('../../descuentos', $descuentos_general);
    }

    public static function updateImpuestos($state, $set, $get)
    {
        $productoId = $get('id_producto');
        $precioVenta = $get('precio_unitario');
        if ($productoId) {
            $producto = Producto::find($productoId);
            if ($producto) {
                $impuestos = [];
                $total = ($get('cantidad') ?? 1) * $precioVenta;

                foreach ($producto->impuestos as $impuesto) {
                    $montoGravable = $total / (1 + ($impuesto->tasa_monto / 100));
                    $montoImpuesto = $total - $montoGravable;

                    $montoGravable = round($montoGravable, 5);
                    $montoImpuesto = round($montoImpuesto, 5);

                    $impuestos[] = [
                        'id_impuesto' => $impuesto->id,
                        'nombre_impuesto' => $impuesto->nombre,
                        'tasa_monto' => $impuesto->tasa_monto,
                        'codigo' => $impuesto->codigo,
                        'monto_gravable' => $montoGravable,
                        'monto_impuesto' => $montoImpuesto,
                    ];
                }

                $set('impuestos', $impuestos);
            }
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('created_by')
                    ->default(auth()->id()),

                Section::make('Información de Venta')
                    ->schema([
                        // Primera fila - Sucursal, Moneda, Tipo Comprobante
                        Grid::make()
                            ->schema([
                                // En tu VentaResource.php
                                TextInput::make('creador.nombre1')
                                    ->label('Vendedor')
                                    ->disabled()
                                    ->visibleOn('edit')
                                    ->columnspan(1),
                                Select::make('id_sucursal')
                                    ->prefixIcon('heroicon-o-building-storefront')
                                    ->label('Sucursal')
                                    ->options(function () {
                                        $user = Auth::user();
                                        return $user->sucursales->pluck('nombre', 'id');
                                    })
                                    ->required()
                                    ->default(function () {
                                        $user = Auth::user();
                                        return $user->sucursales->count() === 1 ? $user->sucursales->first()->id : null;
                                    })
                                    ->columnSpan(1),

                                Select::make('id_moneda')
                                    ->relationship('moneda', 'nombre')
                                    ->default('1')
                                    ->required()
                                    ->disabled(true)
                                    ->label('Moneda')
                                    ->columnSpan(1),

                                Select::make('tipoComprobante')
                                    ->options([
                                        '1' => 'FEL',
                                        '2' => 'Nota',
                                    ])
                                    ->default('2')
                                    ->preload()
                                    ->reactive()
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3),

                        // Segunda fila - Cliente y CF
                        Grid::make()
                            ->schema([
                                Select::make('id_cliente')
                                    ->relationship('cliente', 'nombre')
                                    ->preload()
                                    // ->createOptionForm([
                                    //     TextInput::make('nombre')->required()->maxLength(255),
                                    //     TextInput::make('telefono')->required()->maxLength(255),
                                    //     TextInput::make('correo')->email()->required()->maxLength(255),
                                    // ])
                                    ->required(function (callable $get) {
                                        return !$get('es_consumidor_final');
                                    })
                                    ->searchable()
                                    ->label('Cliente')
                                    ->hidden(fn(callable $get) => $get('es_consumidor_final'))
                                    ->options(function () {
                                        return Entidad::where('es_cliente', 1)->pluck('nombre', 'id');
                                    })
                                    ->columnSpan(2),

                                Toggle::make('es_consumidor_final')
                                    ->label('Usar Consumidor Final')
                                    ->inline()
                                    ->reactive()
                                    ->columnSpan(1)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('id_cliente', $state ? null : '');
                                    }),
                            ])
                            ->columns(3),

                        // Campo Consumidor Final (aparece solo cuando el toggle está activo)
                        TextInput::make('consumidor_final')
                            ->label('Nombre Consumidor Final')
                            ->default('Consumidor Final') // Valor por defecto
                            ->placeholder('Ingrese nombre o "CF" para predeterminado')
                            ->maxLength(255)
                            ->required(function (callable $get) {
                                return $get('es_consumidor_final');
                            })
                            ->hidden(fn(callable $get) => !$get('es_consumidor_final'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (strtoupper(trim($state)) === 'CF') {
                                    $set('consumidor_final', 'Consumidor Final');
                                }
                            })
                            ->columnSpanFull(),

                        // Tercera fila - Estados y Fecha
                        Grid::make()
                            ->schema([
                                ToggleButtons::make('estado')
                                    ->inline()
                                    ->options(FacturasStatus::class)
                                    ->required()
                                    ->columnSpan(2),

                                    DateTimePicker::make('fechahora_emision')
                                    ->required()
                                    ->default(now())
                                    ->label('Fecha y Hora Factura')
                                    ->displayFormat('d/m/Y H:i:s') // Formato de 24 horas
                                    ->timezone('America/Guatemala') // Zona horaria específica
                                    ->seconds(true) // Muestra los segundos
                                    ->columnSpan(1),
                            ])
                            ->columns(3),
                    ])
                    ->columns(1),
                Section::make('Informacion de productos')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Hidden::make('id_sucursal') // Campo oculto para almacenar la sucursal seleccionada
                                    ->default(function (callable $get) {
                                        return $get('../../id_sucursal'); // Obtener la sucursal seleccionada del formulario principal
                                    }),
                                TextInput::make('codigo')
                                    ->label('Código de Barras')
                                    ->placeholder('Escanea o ingresa el código de barras')
                                    ->reactive()
                                    ->hiddenOn('view')
                                    ->columnSpan(3)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $producto = Producto::where('codigo', $state)->first();
                                        if ($producto) {
                                            $set('producto_id', $producto->id);
                                            $set('precio_unitario', $producto->precio_venta);
                                        }
                                        static::updateImpuestos($state, $set, $get);
                                        static::calculateTotals($state, $set, $get);
                                    }),
                                Select::make('producto_id')
                                    ->label('Producto')
                                    ->options(function () {
                                        return Producto::all()->mapWithKeys(function ($producto) {
                                            return [$producto->id => "{$producto->nombre} ({$producto->descripcion})"];
                                        });
                                    })
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(fn($context) => $context === 'view' ? 10 : 8)
                                    ->searchable()
                                    ->distinct()
                                    ->columnSpan(5)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $producto = Producto::where('id', $state)->first();
                                        if ($producto) {
                                            $set('codigo', $producto->codigo);
                                            $set('precio_unitario', $producto->precio_venta);

                                            // Verificar si el producto es un servicio
                                            if ($producto->tipo === 'servicio') {
                                                $set('cantidad', 1); // Establecer cantidad en 1
                                                $set('../../mostrar_cantidad', true); // Mostrar el campo "Cantidad"
                                            } else {
                                                $set('../../mostrar_cantidad', true); // Mostrar el campo "Cantidad"
                                            }
                                        }
                                        static::updateImpuestos($state, $set, $get);
                                        static::calculateTotals($state, $set, $get);
                                    }),
                                TextInput::make('cantidad')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Recalcular totales
                                        static::calculateTotals($state, $set, $get);
                                    }),
                                
                                Select::make('precio_tipo')
                                    ->required()
                                    ->label('Tipo de precio')
                                    ->options(function (callable $get) {
                                        $productoId = $get('producto_id');
                                        if (!$productoId) return [];
                                        
                                        $producto = Producto::with('precios')->find($productoId);
                                        if (!$producto) return [];
                                        
                                        $options = [
                                            'precio_compra' => 'Precio compra: Q' . number_format($producto->precio_compra, 2),
                                            'precio_venta' => 'Precio venta: Q' . number_format($producto->precio_venta, 2),
                                            'precio_mayoreo' => 'Precio mayoreo: Q' . number_format($producto->precio_mayoreo, 2),
                                        ];
                                        
                                        foreach ($producto->precios as $precio) {
                                            $options['precio_'.$precio->id] = $precio->nombre . ': Q' . number_format($precio->precio, 2);
                                        }
                                        
                                        $options['manual'] = 'Ingresar precio manual';
                                        
                                        return $options;
                                    })
                                    ->reactive()
                                    ->columnSpan(3)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $productoId = $get('producto_id');
                                        if (!$productoId) return;

                                        $producto = Producto::with('precios')->find($productoId);
                                        $precio = 0;

                                        if (str_starts_with($state, 'precio_')) {
                                            $tipo = str_replace('precio_', '', $state);

                                            if ($tipo === 'compra') $precio = $producto->precio_compra;
                                            elseif ($tipo === 'venta') $precio = $producto->precio_venta;
                                            elseif ($tipo === 'mayoreo') $precio = $producto->precio_mayoreo;
                                            elseif (is_numeric($tipo)) {
                                                $precioPersonalizado = $producto->precios->find($tipo);
                                                if ($precioPersonalizado) {
                                                    $precio = $precioPersonalizado->precio;
                                                }
                                            }
                                        }

                                        $set('precio_unitario', $precio);

                                        // Recalcular totales
                                        static::calculateTotals($state, $set, $get);
                                    }),

                                TextInput::make('precio_unitario')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(3)
                                    ->label('Precio Unitario')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->disabled(fn (callable $get): bool => $get('precio_tipo') !== 'manual')
                                    ->dehydrated(true) // Asegura que el valor se envíe al servidor incluso si está deshabilitado
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Recalcular totales
                                        static::calculateTotals($state, $set, $get);
                                    }),

                                TextInput::make('descuento')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->label('Desc.')
                                    ->columnSpan(2)
                                    ->nullable()
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Recalcular totales
                                        static::calculateTotals($state, $set, $get);
                                    }),
                                TextInput::make('total')
                                    ->numeric()
                                    ->label('Subtotal')
                                    ->columnSpan(3)
                                    ->readOnly()
                                    ->nullable(),
                                Repeater::make('impuestos')
                                    ->relationship('impuestos')
                                    ->schema([
                                        Hidden::make('id_impuesto'),  // Campo oculto para el ID
                                        TextInput::make('nombre_impuesto')  // Campo visible para mostrar el nombre
                                            ->label('Impuesto')
                                            ->disabled()
                                            ->dehydrated(false)  // No se enviará al servidor
                                            ->columnSpan(6)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                // Mostrar el nombre del impuesto si existe el registro
                                                if ($record && $record->impuesto) {
                                                    $component->state($record->impuesto->nombre);
                                                }
                                            }),
                                        TextInput::make('monto_gravable')
                                            ->numeric()
                                            ->label('Gravable')
                                            ->required()
                                            ->readOnly()
                                            ->columnSpan(3),
                                        TextInput::make('monto_impuesto')
                                            ->numeric()
                                            ->label('Impuesto')
                                            ->required()
                                            ->readOnly()
                                            ->columnSpan(3),
                                    ])
                                    ->columns(12)
                                    ->grid(2)
                                    ->dehydrated(true)
                                    ->columnSpan('full')
                                    ->addable(false) // Deshabilitar la adición manual
                                    ->deletable(false) // Deshabilitar la eliminación
                                    ->reorderable(false) // Deshabilitar el reordenamiento
                                    ->visible(function (callable $get) {
                                        $productoId = $get('id_producto');
                                        if ($productoId) {
                                            $producto = Producto::find($productoId);
                                            return $producto && $producto->impuestos->count() > 0;
                                        }
                                        return false;
                                    })
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                Log::info('Datos recibidos en mutateRelationshipDataBeforeCreateUsing:', $data);

                                if (!isset($data['cantidad'])) {
                                    Log::warning('Campo "cantidad" no está definido en el array $data.');
                                    $data['cantidad'] = 1; // Asignar un valor predeterminado
                                }

                                // Obtener el stock disponible para el producto en la sucursal seleccionada
                                $sucursalId = $data['id_sucursal']; // Asegúrate de que este campo esté disponible
                                $stockTotal = InventarioStock::where('id_producto', $data['producto_id'])
                                    ->where('id_sucursal', $sucursalId)
                                    ->sum('cantidad_actual');

                                // Verificar si hay suficiente stock
                                if ($data['cantidad'] > $stockTotal) {
                                    throw new \Exception("No hay suficiente stock para el producto. Stock disponible: $stockTotal");
                                }

                                // Calcular totales del ítem
                                $data['precio_parcial'] = $data['cantidad'] * $data['precio_unitario'];
                                $data['total'] = $data['precio_parcial'] - $data['descuento'];

                                return $data;
                            })
                            ->required()
                            ->columns(22)
                            ->dehydrated(true)
                            ->addActionLabel('Añadir Producto')
                            ->reorderableWithDragAndDrop(true)
                            ->columnSpan('full')
                            ->itemLabel(function (array $state): ?string {
                                $productoNombre = '';
                                if (!empty($state['producto_id'])) {
                                    $producto = Producto::find($state['producto_id']);
                                    if ($producto) {
                                        $productoNombre = $producto->nombre;
                                    }
                                }

                                return sprintf(
                                    '📦 %s | 💵 Q  %s',
                                    $productoNombre ?? 'N/A',
                                    $productoDescripcion ?? 'N/A',
                                    number_format($state['total'] ?? 0, 2)
                                );
                            })
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                Log::info('Mutando relación antes de crear', ['data' => $data]);

                                // Verificar si la clave 'cantidad' está definida
                                if (!isset($data['cantidad'])) {
                                    Log::warning('Campo "cantidad" no está definido en el array $data.');
                                    $data['cantidad'] = 1; // Establecer un valor predeterminado
                                }

                                // Verificar si la clave 'precio_unitario' está definida
                                if (!isset($data['precio_unitario'])) {
                                    Log::warning('Campo "precio_unitario" no está definido en el array $data.');
                                    $data['precio_unitario'] = 0; // Establecer un valor predeterminado
                                }

                                // Verificar si la clave 'descuento' está definida
                                if (!isset($data['descuento'])) {
                                    Log::warning('Campo "descuento" no está definido en el array $data.');
                                    $data['descuento'] = 0; // Establecer un valor predeterminado
                                }

                                // Calcular totales del ítem
                                $data['precio_parcial'] = $data['cantidad'] * $data['precio_unitario'];
                                $data['total'] = $data['precio_parcial'] - $data['descuento'];

                                return $data;
                            })
                    ]),
                    Section::make('Totales')
                    ->schema([
                        TextInput::make('descuentos')
                            ->label('Descuentos')
                            ->numeric()
                            ->readOnly()
                            ->columnSpan(4)
                            ->default(0),
                        TextInput::make('total_sum')
                            ->label('Total')
                            ->numeric()
                            ->readOnly()
                            ->columnSpan(4)
                            ->default(0)
                            ->afterStateHydrated(function ($set, $get) {
                                $items = $get('../items') ?? [];
                                $total = collect($items)->sum(function ($item) {
                                    return is_numeric($item['total'] ?? 0) ? $item['total'] : 0;
                                });
                                $set('total_sum', $total); // Guardar como número puro
                            }),
                    ])
                    ->columns(12),

                // SECCIÓN DE MÉTODOS DE PAGO (NUEVA)
                Section::make('Métodos de Pago')
                ->schema([
                Repeater::make('paymentMethods')
                ->relationship('paymentMethods')
                ->label('Métodos de Pago')
                ->schema([
                    Select::make('metodo_pago_id')
                        ->label('Método')
                        ->default('1')
                        ->prefixIcon('heroicon-o-banknotes')
                        ->options(MetodoPago::pluck('nombre', 'id'))
                        ->required()
                        ->reactive()
                        ->columnSpan(5),

                    TextInput::make('monto')
                        ->label('Monto')
                        ->numeric()
                        ->required()
                        ->minValue(0.01)
                        ->columnSpan(4)
                        ->prefix('Q')
                        ->reactive()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            // Obtener el total de la venta como número
                            $totalVenta = is_numeric($get('../../total_sum')) 
                                ? $get('../../total_sum') 
                                : floatval(str_replace(['Q', ',', ' '], '', $get('../../total_sum')));
                            
                            // Sumar montos asegurando que son números
                            $pagos = $get('../../paymentMethods') ?? [];
                            $sumaMontos = collect($pagos)->sum(function ($item) {
                                return is_numeric($item['monto'] ?? 0) ? $item['monto'] : 0;
                            });
                            
                            // Actualizar campos
                            $set('../../total_pagado', number_format($sumaMontos, 2));
                            $set('../../cambio', number_format(max(0, $sumaMontos - $totalVenta), 2));
                        }),

                    TextInput::make('referencia')
                        ->label('Referencia')
                        ->columnSpan(3),
                ])
                ->columns(12)
                ->defaultItems(1)
                ->required()
                ->live(),

            // Resumen de pagos (simplificado)
            Grid::make(12)
                ->schema([
                    TextInput::make('total_pagado')
                        ->label('Total Pagado')
                        ->inputMode('decimal')
                        ->readOnly()
                        ->prefix('Q')
                        ->columnSpan(6)
                        ->default(0),
                        

                    TextInput::make('cambio')
                        ->label('Cambio a Devolver')
                        ->inputMode('decimal')
                        ->readOnly()
                        ->prefix('Q')
                        ->columnSpan(6)
                        ->default(0),
                ])
            ])
                ->columns(1)
                ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('tipoComprobante')
                    ->sortable()
                    ->label('Doc')
                    ->formatStateUsing(fn($state) => $state === 1 ? 'FEL' : ($state === 2 ? 'NOTA' : $state)),
                TextColumn::make('cliente.nombre')->sortable(),
                TextColumn::make('fechahora_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('creador.nombre1')
                    ->label('Vendedor')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('GTQ')
                    ->sortable(),
                TextColumn::make('certificada')
                    ->sortable()
                    ->badge()
                    ->color(function (Venta $record) {
                        if ($record->tipoComprobante === 2) {
                            return 'primary';
                        }
                        return $record->certificada ? 'success' : 'danger';
                    })
                    ->icon(function (Venta $record) {
                        if ($record->tipoComprobante === 2) {
                            return 'heroicon-m-sparkles';
                        }
                        return $record->certificada ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    })
                    ->formatStateUsing(fn($record) => $record->tipoComprobante === 2 ? 'No aplica' : ($record->certificada ? 'Sí' : 'No')),
                TextColumn::make('estado')
                    ->badge()
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])

            ->headerActions([
                Tables\Actions\Action::make('resumenventas')
                    ->icon('heroicon-o-document-text')
                    ->label('Resumen Ventas')
                    ->form([
                        Select::make('sucursal')
                            ->label('Sucursal:')
                            ->relationship('sucursal', 'nombre')
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
                        return redirect()->route('resumen.ventas', $data);
                    }),
                Tables\Actions\Action::make('reporteComprasPDF')
                    ->icon('heroicon-o-document-text')
                    ->label('Reporte PDF')
                    ->color('danger')
                    ->form([
                        Select::make('sucursal')
                            ->label('Sucursal:')
                            ->relationship('sucursal', 'nombre')
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable()
                            ->required(), // Permite buscar entre las opciones
                        Select::make('vendedor')
                            ->label('Vendedor:')
                            ->relationship('creador', 'nombre1')
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(),
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->default(now()->startOfMonth()),


                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->default(now()->endOfMonth()),


                    ])
                    ->action(function (array $data) {
                        // Redirigir al controlador con los datos del formulario
                        return redirect()->route('reporte.ventas', $data);
                    }),

                Tables\Actions\Action::make('reporteComprasEXCEL')
                    ->icon('heroicon-o-table-cells')
                    ->label('Reporte EXCEL')
                    ->color('success')
                    ->form([
                        Select::make('sucursal')
                            ->label('Sucursal:')
                            ->relationship('sucursal', 'nombre')
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
                        return redirect()->route('reporte.ventas.excel', $data);
                    })



            ])
            ->actions([
                Action::make('certificar')
                    ->label('Certificar')
                    ->icon('heroicon-o-check')
                    ->action(function (Venta $record) {
                        // Lógica para intentar realizar la certificación
                        // Por ejemplo, llamar a un servicio externo para certificar la venta
                        // y actualizar el estado de la venta en la base de datos

                        Log::info('Certificando factura FEL', ['venta' => $record]);
                        $felService = new FelCertificationService();

                        try {
                            $felService->certifyVenta($record);
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
                    })
                    ->visible(fn(Venta $record) => !$record->certificada && $record->tipoComprobante === 1),
                Action::make('Pdf download')
                    ->label("Factura")
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Venta $record) => route('download.ventas.pdf', $record))
                    ->openUrlInNewTab()
                    ->color('danger'),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('estado', '1')->count();
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         StatsVentas::class,
    //     ];
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
