<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseInvoiceResource\Pages;
use App\Filament\Resources\PurchaseInvoiceResource\RelationManagers;
use App\Models\Certificador;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\PurchaseInvoice;
use App\Models\TipoDte;
use App\Models\Entidad;
use App\Models\Sucursal;


use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle; 
use Filament\Forms\Components\FileUpload;


class PurchaseInvoiceResource extends Resource
{
    protected static ?string $model = PurchaseInvoice::class;

    protected static ?string $navigationIcon = 'custom-shopping-cart-simple';

    protected static ?string $navigationLabel = 'Compras';

    protected static ?string $title = 'Compras';

    protected static ?string $heading = 'Custom Page Heading';

    protected static ?string $subheading = 'Custom Page Subheading';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de Factura')
                    ->schema([
                        Select::make('id_moneda')
                            ->relationship('monedas', 'nombre')
                            ->default('1')
                            ->required()
                            ->columnSpan(4)
                            ->disabled(true)
                            ->label('Moneda'),
                        Select::make('tipoComprobante')
                            ->options([
                                '1' => 'FEL',
                                '2' => 'Nota',
                            ])
                            ->default('2')
                            ->columnSpan(4)
                            ->preload()
                            ->reactive()
                            ->required(),
                        Select::make('id_tipoDte')
                            ->label('Tipo de Factura')
                            ->options(TipoDte::all()->pluck('nombre', 'id'))
                            ->nullable()
                            ->columnSpan(4)
                            ->visible(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->required(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->searchable(),
                        TextInput::make('no_autorizacion')
                            ->required()
                            ->columnSpan(4)
                            ->unique(ignoreRecord: true)
                            ->label(fn(callable $get) => $get('tipoComprobante') === '1' ? 'Número de Factura (Autorización)' : 'Número de Nota')
                            ->default(fn() => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT)),
                        TextInput::make('serie')
                            ->nullable()
                            ->columnSpan(4)
                            ->visible(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->required(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->label('Serie'),
                        TextInput::make('codigo_autorizacion')
                            ->nullable()
                            ->columnSpan(4)
                            ->visible(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->required(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->label('Codigo de Autorización'),
                        Select::make('id_sucursal')
                            ->prefixIcon('heroicon-o-building-storefront')
                            ->label('Sucursal')
                            ->options(function () {
                                // Obtener el usuario autenticado
                                $user = Auth::user();

                                // Obtener las sucursales asignadas al usuario
                                return $user->sucursales->pluck('nombre', 'id');
                            })
                            ->required()
                            //->disabled(fn () => Auth::user()->sucursales->count() === 1)
                            ->default(function () {
                                // Si el usuario solo tiene una sucursal, seleccionarla por defecto
                                $user = Auth::user();
                                return $user->sucursales->count() === 1 ? $user->sucursales->first()->id : null;
                            })
                            ->columnSpan(4),
                        Select::make('id_certificador')
                            ->label('Certificador')
                            ->prefixIcon('heroicon-o-building-library')
                            ->options(Certificador::all()->pluck('nombre', 'id'))
                            ->nullable()
                            ->columnSpan(8)
                            ->visible(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->required(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->searchable(),
                        DatePicker::make('fechahora_certificacion')
                            ->nullable()
                            ->columnSpan(4)
                            ->visible(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->required(fn(callable $get) => $get('tipoComprobante') === '1')
                            ->default(now())
                            ->label('Fecha de Certificación'),
                        Select::make('id_proveedor')
                            ->relationship('proveedores', 'nombre')
                            ->prefixIcon('heroicon-o-user')
                            ->preload()
                            // ->createOptionForm([
                            //     Forms\Components\TextInput::make('nombre')
                            //         ->required()
                            //         ->maxLength(255),
                            //     Forms\Components\TextInput::make('telefono')
                            //         ->required()
                            //         ->maxLength(255),
                            //     Forms\Components\TextInput::make('correo')
                            //         ->email()
                            //         ->required()
                            //         ->maxLength(255),
                            // ])
                            ->required()
                            ->columnSpan(8)
                            ->searchable()
                            ->label('Proveedor')
                            ->options(function () {
                                return Entidad::where('es_proveedor', 1)->pluck('nombre', 'id');
                            }),

                        DatePicker::make('fechahora_emision')
                            ->required()
                            ->columnSpan(4)
                            ->default(now())
                            ->label('Fecha de Factura'),
                        // Select::make('estado')
                        //     ->options([
                        //         '1' => 'Pendiente',
                        //         '2' => 'Aprobada',
                        //         '3' => 'Cancelada',
                        //     ])
                        //     ->default('1')
                        //     ->required(),
                    ])->columns(12),

                    Section::make('Detalles de Productos')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                // Select::make('tipo')
                                //     ->options([
                                //         'B' => 'Bien',
                                //         'S' => 'Servicio',
                                //     ])
                                //     ->default('B')
                                //     ->columnSpan(1)
                                //     ->required(),
                                TextInput::make('codigo')
                                    ->label('Código de Barras')
                                    ->placeholder('Escanea o ingresa el código de barras')
                                    ->reactive()
                                    ->hiddenOn('view')
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $producto = Producto::where('codigo', $state)->first();
                                        if ($producto) {
                                            $set('producto_id', $producto->id);
                                        }
                                    }),
                                Select::make('producto_id')
                                    ->options(function () {
                                        return Producto::all()->mapWithKeys(function ($producto) {
                                            return [$producto->id => "{$producto->nombre} ({$producto->descripcion})"];
                                        });
                                    })
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(4)
                                    ->searchable()
                                    ->distinct()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $producto = Producto::where('id', $state)->first();
                                        if ($producto) {
                                            $set('codigo', $producto->codigo);
                                            if ($producto->tipo === 'servicio') {
                                                $set('cantidad', 1); // Fuerza cantidad = 1 si es servicio
                                            }
                                        }
                                    })
                                    ->createOptionForm([
                                        Grid::make(4)->schema([
                                            TextInput::make('nombre')
                                                ->required()
                                                ->ColumnSpan(2)
                                                ->maxLength(255)
                                                ->label('Nombre del Producto'),
                                            TextInput::make('codigo')
                                                ->required()
                                                ->disabled(fn (callable $get) => $get('generar_correlativo'))
                                                ->dehydrated()
                                                ->default('')
                                                ->unique(table: Producto::class, column: 'codigo', ignoreRecord: true),
                                            Toggle::make('generar_correlativo')
                                                ->label('Generar automático')
                                                ->reactive()
                                                ->inline(false)
                                                ->default(false)
                                                ->afterStateUpdated(function ($set, $get) {
                                                    if ($get('generar_correlativo')) {
                                                        $ultimoCodigo = Producto::query()
                                                            ->where('codigo', 'REGEXP', '^[0-9]{6}$')
                                                            ->orderByRaw('CAST(codigo AS UNSIGNED) DESC')
                                                            ->value('codigo');
                                                        
                                                        $nuevoNumero = $ultimoCodigo ? ((int)$ultimoCodigo + 1) : 1;
                                                        $nuevoCodigo = str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);
                                                        
                                                        $set('codigo', $nuevoCodigo);
                                                    } else {
                                                        $set('codigo', '');
                                                    }
                                                }),
                                            ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('descripcion')
                                                ->required()
                                                ->maxLength(255)
                                                ->label('Descripción del Producto'),
                                            FileUpload::make('imagen')->image()
                                                ->imageEditor() //habilita la edición de la imagen
                                                ->uploadingMessage('Cargando Imagen...') //mensaje mientras se sube la imagen
                                                ->disk('images_external')
                                                ->directory('productos')
                                                ->visibility('public')
                                                ->previewable(true)
                                                ->placeholder('Sube una imagen')
                                                // ->previewUsing(function ($file) {
                                                //     // return Storage::disk('images')->url($file);
                                                // })
                                                ->helperText('Elige una imagen en formato JPG o PNG'),
                                        ]),
                                        Grid::make(3)->schema([
                                            DatePicker::make('fecha')
                                                ->default(now())
                                                ->required(),
                                            Select::make('tipo')
                                                ->label('Tipo ')
                                                ->options([
                                                    'bien' => 'Bien',
                                                    'servicio' => 'Servicio',
                                                ])
                                                ->default('bien')
                                                ->required(),
                                            Select::make('impuesto')
                                                ->label('Impuestos')
                                                ->multiple()
                                                ->required()
                                                ->options(\App\Models\ImpuestoUnidadGravable::pluck('nombre_corto', 'id'))
                                                ->preload()
                                                ->searchable(),

                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('id_marca')
                                                ->label('Marca')
                                                ->required()
                                                ->options(\App\Models\Marca::pluck('nombre', 'id'))
                                                ->preload()
                                                ->searchable(),
                                            Select::make('id_categorias')
                                                ->required()
                                                ->label('Categoria')
                                                ->options(\App\Models\Categoria::pluck('nombre', 'id'))
                                                ->preload()
                                                ->searchable(),
                                        ]),

                                        Grid::make(3)->schema([
                                            TextInput::make('precio_compra')
                                                ->label('Precio de Compra')
                                                ->numeric()
                                                ->prefix('Q.')
                                                ->required()
                                                ->reactive()
                                                ->live(onBlur: true),
                                            TextInput::make('precio_venta')
                                                ->label('Precio de Venta')
                                                ->numeric()
                                                ->prefix('Q.')
                                                ->required()
                                                ->reactive()
                                                ->live(onBlur: true),
                                            TextInput::make('precio_mayoreo')
                                                ->label('Precio por Mayoreo')
                                                ->numeric()
                                                ->prefix('Q.')
                                                ->required(),
                                        ]),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        // Aquí creas el producto y devuelves el ID
                                        $producto = \App\Models\Producto::create($data);
                                        return $producto->id;
                                    }),
                                TextInput::make('cantidad')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->reactive()
                                    ->columnSpan(2)
                                    ->label('Cantidad')
                                    ->live(debounce: 500)
                                    ->minValue(0)
                                    ->step('0.01')
                                    ->live(onBlur: true)
                                    // Deshabilita el campo si es un servicio (pero sigue enviando el valor)
                                    ->disabled(fn (callable $get): bool => 
                                        $get('producto_id') ? Producto::find($get('producto_id'))?->tipo === 'servicio' : false
                                    )
                                    // Fuerza el envío del campo aunque esté deshabilitado
                                    ->dehydrated(true)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Si es servicio, forzar cantidad = 1 (y evitar valores nulos/cero)
                                        if ($get('producto_id') && Producto::find($get('producto_id'))?->tipo === 'servicio') {
                                            $set('cantidad', 1);
                                            $state = 1; // Sobrescribir el estado actual
                                        }
                                
                                        $quantity = floatval($state ?? 1); // Default 1 si es null
                                        $unitPrice = floatval($get('precio_unitario') ?? 0);
                                        $subtotal = $quantity * $unitPrice;
                                        
                                        $set('total', number_format($subtotal, 2, '.', ''));
                                
                                        // Cálculos generales (suma de todos los items)
                                        $items = $get('../../items') ?? [];
                                        $sumaTotal = 0;
                                        foreach ($items as $item) {
                                            $sumaTotal += floatval($item['total'] ?? 0);
                                        }
                                        
                                        // Calcular IVA y subtotal
                                        $iva = $sumaTotal / 1.12 * 0.12;
                                        $subtotalGeneral = $sumaTotal - $iva;
                                        
                                        $set('../../totales', number_format($sumaTotal, 2, '.', ''));
                                        $set('../../subtotal', number_format($subtotalGeneral, 2, '.', ''));
                                        $set('../../impuesto', number_format($iva, 2, '.', ''));
                                    })
                                    ->dehydrateStateUsing(function ($state, callable $get) {
                                        // Garantizar que siempre se guarde un valor (1 para servicios o el valor ingresado)
                                        return $get('producto_id') && Producto::find($get('producto_id'))?->tipo === 'servicio' 
                                            ? 1 
                                            : ($state ?? 1); // Fallback a 1 si es null
                                    }),
                
                                TextInput::make('precio_unitario')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(2)
                                    ->label('Precio Unitario')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $unitPrice = floatval($state ?? 0);
                                        $quantity = floatval($get('cantidad') ?? 0);
                                        $subtotal = $quantity * $unitPrice;
                                        $set('total', number_format($subtotal, 2, '.', ''));
                
                                        // Cálculos generales (suma de todos los items)
                                        $items = $get('../../items') ?? [];
                                        $sumaTotal = 0;
                                        foreach ($items as $item) {
                                            $sumaTotal += floatval($item['total'] ?? 0);
                                        }
                                        
                                        // Calcular IVA y subtotal
                                        $iva = $sumaTotal / 1.12 * 0.12;
                                        $subtotalGeneral = $sumaTotal - $iva;
                                        
                                        $set('../../totales', number_format($sumaTotal, 2, '.', ''));
                                        $set('../../subtotal', number_format($subtotalGeneral, 2, '.', ''));
                                        $set('../../impuesto', number_format($iva, 2, '.', ''));
                                    }),
                
                                TextInput::make('total')
                                    ->readonly()
                                    ->numeric()
                                    ->dehydrated()
                                    ->label('Subtotal')
                                    ->reactive()
                                    ->columnSpan(2),
                            ])
                            ->columns(12)
                            ->addActionLabel('Añadir Producto')
                            ->columnSpan('full')
                            ->itemLabel(function (array $state): ?string {
                                $productoNombre = '';
                                $productoDescripcion = '';
                                if (!empty($state['producto_id'])) {
                                    $producto = Producto::find($state['producto_id']);
                                    if ($producto) {
                                        $productoNombre = $producto->nombre;
                                        $productoDescripcion = $producto->descripcion ?? '';
                                    }
                                }
                            
                                return sprintf(
                                    '📦 %s | 📝 %s | 💵 Q %s',
                                    $productoNombre ?: ' ',
                                    $productoDescripcion ?: ' ',
                                    number_format($state['total'] ?? 0, 2)
                                );
                            })
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Recalcular cuando se elimina un item
                                $items = $get('items') ?? [];
                                $sumaTotal = 0;
                                foreach ($items as $item) {
                                    $sumaTotal += floatval($item['total'] ?? 0);
                                }
                                
                                // Calcular IVA y subtotal
                                $iva = $sumaTotal / 1.12 * 0.12;
                                $subtotalGeneral = $sumaTotal - $iva;
                                
                                $set('totales', number_format($sumaTotal, 2, '.', ''));
                                $set('subtotal', number_format($subtotalGeneral, 2, '.', ''));
                                $set('impuesto', number_format($iva, 2, '.', ''));
                            }),
                    ]),
                
                    Section::make('Totales')
                    ->schema([
                        TextInput::make('totales')
                            ->label('Total')
                            ->readonly()
                            ->numeric()
                            ->default(0),
                           
                        TextInput::make('subtotal')
                            ->readonly()
                            ->numeric()
                            ->label('Subtotal')
                            ->default(0),
                    
                        TextInput::make('impuesto')
                            ->readonly()
                            ->numeric()
                            ->label('IVA')
                            ->default(0),
                    
                        Textarea::make('notes')
                            ->label('Notas')
                            ->columnSpan('full'),
                    ])->columns(3),
                ]);
            }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_autorizacion')
                    ->searchable()
                    ->sortable()
                    ->label('# Factura'),

                TextColumn::make('proveedores.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Proveedor'),

                TextColumn::make('fechahora_emision')
                    ->date()
                    ->sortable()
                    ->label('Fecha'),

                TextColumn::make('total')
                    ->money()
                    ->sortable()
                    ->label('Total'),

                IconColumn::make('estado')
                    ->label('Estado')
                    ->icon(fn(string $state): string => match ($state) {
                        '1' => 'heroicon-o-clock',
                        '2' => 'heroicon-o-check-circle',
                        '3' => 'heroicon-o-x-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'warning',
                        '2' => 'success',
                        '3' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        '1' => 'Pendiente',
                        '2' => 'Aprobada',
                        '3' => 'Cancelada',
                    ]),

                // Tables\Filters\DateFilter::make('invoice_date'),
            ])
            ->defaultSort('fechahora_emision', 'desc')
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->url(fn(PurchaseInvoice $record) => route('compra.individual', $record))
                    ->openUrlInNewTab(),
                    
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                

            ])
            ->headerActions([
                Tables\Actions\Action::make('productoComprado')
                    ->icon('heroicon-o-document-text')
                    ->label('Productos Comprados')
                    ->form([   
                        Select::make('sucursal')
                            ->label('Sucursal:')
                            ->relationship('sucursal', 'nombre')
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable()
                            ->required(), // Permite buscar entre las opciones                   
                        Select::make('id_proveedor')
                            ->label('Proveedor:')
                            ->relationship('proveedores', 'nombre')
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable()
                            //->required() // Permite buscar entre las opciones
                            ->options(function () {
                                return Entidad::where('es_proveedor', 1)->pluck('nombre', 'id');
                            }),
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio'),
                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),
                        
                    ])
                    ->action(function (array $data) {
                        // Redirigir al controlador con los datos del formulario
                        return redirect()->route('productos.comprados', $data);
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
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio'),
                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),
                        
                    ])
                    ->action(function (array $data) {
                        // Redirigir al controlador con los datos del formulario
                        return redirect()->route('reporte.compras', $data);
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
                            ->label('Fecha de Inicio'),
                            
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),
                        
                    ])
                    ->action(function (array $data) {
                        // Redirigir al controlador con los datos del formulario
                        return redirect()->route('reporte.compras.excel', $data);
                    })
                    
                    
                   
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function view(PurchaseInvoice $view): PurchaseInvoice
    {
        return $view
            ->schema([
                TextEntry::make('tipo')->label('N° Factura'),
                TextEntry::make('proveedor.nombre')->label('Cliente'),
                TextEntry::make('id_moneda')->label('Total')->money('GTQ'),
                // DateEntry::make('created_at')->label('Fecha de Venta'),
            ]);
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
            'index' => Pages\ListPurchaseInvoices::route('/'),
            'create' => Pages\CreatePurchaseInvoice::route('/create'),
            'edit' => Pages\EditPurchaseInvoice::route('/{record}/edit'),
        ];
    }
}