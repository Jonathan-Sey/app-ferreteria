<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\DB;

use App\Filament\Resources\ProductosResource\Pages;
use App\Models\Producto;
use App\Models\ImpuestoUnidadGravable; // Add this line
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle; 
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use IlluminateAgnostic\Arr\Support\HtmlString;
use Schema;



class ProductosResource extends Resource
{
    protected static ?string $model = Producto::class;

    // Define el ícono de navegación aquí
    protected static ?string $navigationIcon = 'custom-clipboard-text';
    public static $icon = 'heroicon-o-document-text';
    public static $icon2 = 'heroicon-o-table-cells';
    //protected static ?string $navigationGroup = 'Operaciones Comerciales';
    protected static ?int $navigationSort = 1; // Primero en el grupo


    public static function form(Form $form): Form
    {
        Log::info('form', ['form' => $form]);
        return $form
            ->schema([
                // Campos existentes...
                Grid::make(3)->schema([
                    TextInput::make('codigo')
                        ->required()
                        ->columnSpan(2)
                        ->disabled(fn (callable $get) => $get('generar_correlativo'))
                        ->dehydrated()
                        ->default('')
                        ->unique(table: Producto::class, column: 'codigo', ignoreRecord: true),
                    
                    Toggle::make('generar_correlativo')
                        ->label('Generar automático')
                        ->reactive()
                        ->inline(false)
                        ->default(false)
                        ->columnSpan(1)
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
                ])->columns(3),
                
                TextInput::make('nombre')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('descripcion')->required()
                    ->columnSpanFull(),
                DatePicker::make('fecha')
                    ->default(now())
                    ->required(),
                // Placeholder::make('Image')
                //     ->hiddenOn('create')
                //     ->content(function ($record): HtmlString {
                //         return new HtmlString("<img src= '" . env('IMAGE_DOMAIN') . '/' . $record->imagen . "')>");
                //     }),
                // FileUpload::make('imagen')->image()
                //     ->imageEditor() //habilita la edición de la imagen
                //     ->uploadingMessage('Cargando Imagen...') //mensaje mientras se sube la imagen
                //     ->disk('images_external')
                //     ->directory('productos')
                //     ->visibility('public')
                //     ->previewable(true)
                //     ->placeholder('Sube una imagen')
                //     // ->previewUsing(function ($file) {
                //     //     // return Storage::disk('images')->url($file);
                //     // })
                //     ->helperText('Elige una imagen en formato JPG o PNG'),
                Select::make('id_marca')
                    ->label('Marca')
                    ->required()
                    ->relationship('marca', 'nombre')
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('descripcion')
                            ->nullable()
                            ->required()
                            ->maxLength(255),
                    ]),
                Select::make('id_categorias')
                    ->required()
                    ->label('Categoria')
                    ->relationship('categoria', 'nombre')
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('descripcion')
                            ->nullable()
                            ->required()
                            ->maxLength(255),
                    ]),
                Select::make('tipo')
                    ->label('Tipo ')
                    ->options([
                        'bien' => 'Bien',
                        'servicio' => 'Servicio',
                    ])
                    ->default('bien')
                    ->required(),
                    TextInput::make('margen')
                    ->label('Margen de Ganancia %')
                    ->suffix('%')
                    ->readOnly()
                    ->disabled()
                    ->default(0)
                    ->dehydrated(false)
                    ->reactive(),
            TextInput::make('markup')
                    ->label('Markup de Ganancia %')
                    ->suffix('%')
                    ->readOnly()
                    ->disabled()
                    ->default(0)
                    ->dehydrated(false)
                    ->reactive(),
                Grid::make(3)->schema([
                    TextInput::make('precio_compra')
                        ->label('Precio de Compra')
                        ->numeric()
                        ->prefix('Q.')
                        ->required(),
                    TextInput::make('precio_venta')
                        ->label('Precio de Venta')
                        ->numeric()
                        ->prefix('Q.')
                        ->required(),
                    TextInput::make('precio_mayoreo')
                        ->label('Precio por Mayoreo')
                        ->numeric()
                        ->prefix('Q.')
                        ->required(),

                    Repeater::make('items')
                        ->label('Precios')
                        ->relationship('precios')
                        ->schema([
                            TextInput::make('nombre')
                                ->label('Nombre del Precio')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('precio')
                                ->label('Precio')
                                ->numeric()
                                ->prefix('Q.')
                                ->required(),
                            Textarea::make('descripcion')
                                ->label('Descripción')
                                ->nullable()
                                ->maxLength(255),
                        ])
                        ->defaultItems(0)
                        ->columns(2)
                        ->createItemButtonLabel('Agregar Precio'),

                        
                    // Select::make('impuestos')
                    //     ->label('Impuestos')
                    //     ->multiple()
                    //     ->required()
                    //     ->columnSpan(6)
                    //     ->relationship('impuestos', 'nombre_corto')
                    //     ->preload()
                    //     ->searchable(),
                ]),

                // Nuevos campos para los impuestos (usando Repeater)
                // Forms\Components\Repeater::make('impuestos')
                //     ->label('Impuestos')
                //     ->relationship('impuestos')
                //     ->schema([
                //         Select::make('tipoId')
                //             ->label('Tipo de Impuesto')
                //             ->options(function () {
                //                 return \App\Models\ImpuestoTipo::pluck('descripcion', 'id');
                //             })
                //             ->reactive()
                //             ->required(),

                //         Select::make('id_impuesto')
                //             ->label('Unidad Gravable')
                //             ->options(function (callable $get) {
                //                 $tipoImpuestoId = $get('tipoId');
                //                 if ($tipoImpuestoId) {
                //                     return \App\Models\ImpuestoUnidadGravable::where('id_cvimpuestostipo', $tipoImpuestoId)
                //                         ->pluck('nombre_corto', 'id');
                //                 }
                //                 return [];
                //             })
                //             ->required()
                //             ->hidden(fn(callable $get) => !$get('tipoId')),
                //     ])
                //     ->defaultItems(1)
                //     ->addActionLabel('Agregar otro impuesto')
                //     ->columns(2)
            ]);
    }

    // public static function create(array $data): Producto
    // {
    //     // Depuración profunda de los datos recibidos
    //     \Log::debug('Datos completos recibidos en create:', [
    //         'data' => $data,
    //         'impuestos_exist' => isset($data['impuestos']),
    //         'impuestos_count' => isset($data['impuestos']) ? count($data['impuestos']) : 0,
    //         'impuestos_content' => $data['impuestos'] ?? 'No hay impuestos'
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // Creación básica del producto
    //         $producto = Producto::create([
    //             'codigo' => $data['codigo'],
    //             'nombre' => $data['nombre'],
    //             'descripcion' => $data['descripcion'],
    //             'fecha' => $data['fecha'],
    //             'imagen' => $data['imagen'] ?? null,
    //             'id_marca' => $data['id_marca'],
    //             'id_presentacion' => $data['id_presentacion'],
    //             'id_categorias' => $data['id_categorias'],
    //             'estado' => 1,
    //             'precio_compra' => $data['precio_compra'],
    //             'precio_venta' => $data['precio_venta'],
    //             'precio_mayoreo' => $data['precio_mayoreo'],
    //             'tipo' => $data['tipo'],
    //         ]);

    //         // Manejo de impuestos con verificación estricta
    //         if (isset($data['impuestos']) && is_array($data['impuestos'])) {
    //             $impuestosIds = [];

    //             foreach ($data['impuestos'] as $impuesto) {
    //                 if (isset($impuesto['id_impuesto'])) {
    //                     $impuestosIds[] = $impuesto['id_impuesto'];
    //                 }
    //             }

    //             if (!empty($impuestosIds)) {
    //                 // Usar syncWithoutDetaching para evitar duplicados
    //                 $producto->impuestos()->syncWithoutDetaching($impuestosIds);
    //                 \Log::debug('Impuestos asignados', [
    //                     'producto_id' => $producto->id,
    //                     'impuestos_ids' => $impuestosIds
    //                 ]);
    //             }
    //         }

    //         DB::commit();
    //         return $producto;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Error al crear producto', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'full_data' => $data
    //         ]);
    //         throw $e;
    //     }
    // }

    // private static function debugImpuestos($data)
    // {
    //     Log::info('Debug impuestos:', [
    //         'impuestos_raw' => $data['impuestos'] ?? 'No hay impuestos',
    //         'tipo_datos' => gettype($data['impuestos'] ?? null),
    //         'estructura' => json_encode($data['impuestos'] ?? [], JSON_PRETTY_PRINT)
    //     ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->searchable()->sortable(),
                TextColumn::make('tipo')->searchable()->sortable(),
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('fecha')->searchable()->sortable(),
                ImageColumn::make('imagen') // Columna para mostrar la imagen
                    ->label('Imagen')
                    ->disk('images_external')
                    ->size(50) // Ajusta el tamaño de la imagen
                    ->circular(), // Opcional: redondea la imagen
            ])
            ->headerActions([
                /*Tables\Actions\Action::make("Download PDF")
                    ->label('PDF General')
                    ->url(route('productos.pdf.general'))
                    ->openUrlInNewTab()
                    ->icon($icon = 'heroicon-o-document-text')
                    ->color('danger'),
                Tables\Actions\Action::make("Download Excel")
                    ->label('Excel General')
                    ->url(route('productos.exportar'))
                    ->icon($icon2 = 'heroicon-o-table-cells')
                    ->color('success'),*/
                Tables\Actions\Action::make("codeBar")
                    ->label('Códigos de Barras')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->outlined()
                    //->url(fn (Producto $record) => route('productos.codebar', $record->id))
                    //->url(route('productos.codebar.all'))
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio'),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),


                        Select::make('marca')
                            ->label('Marca por: ')
                            ->relationship('marca', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        Select::make('categoria')
                            ->label('Categoria por: ')
                            ->relationship('categoria', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        // Campo oculto para indicar la acción (PDF o Excel)
                        Forms\Components\Hidden::make('action'),

                    ])
                    ->action(function (array $data) {
                        // Lógica para manejar los datos del formulario
                        // Por ejemplo, puedes aplicar los filtros aquí
                        //Log::info('Datos del filtro:', $data);
                        $response = redirect()->route('productos.codebar.all', $data);
                        if (session('error')) {
                            Notification::make()
                                ->title(session('error'))
                                ->danger()
                                ->send();
                        }

                        return $response;
                    })
                    ->openUrlInNewTab(),
                
                
                Tables\Actions\Action::make("FiltrosPDF")
                    ->label('PDF')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('danger')
                    ->icon($icon = 'heroicon-o-document-text')
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio'),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),


                        Select::make('marca')
                            ->label('Marca por: ')
                            ->relationship('marca', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        Select::make('categoria')
                            ->label('Categoria por: ')
                            ->relationship('categoria', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        // Campo oculto para indicar la acción (PDF o Excel)
                        Forms\Components\Hidden::make('action'),

                    ])

                    ->action(function (array $data) {
                        // Lógica para manejar los datos del formulario
                        // Por ejemplo, puedes aplicar los filtros aquí
                        //Log::info('Datos del filtro:', $data);
                        $response = redirect()->route('productos.pdf.filtrado', $data);
                        if (session('error')) {
                            Notification::make()
                                ->title(session('error'))
                                ->danger()
                                ->send();
                        }

                        return $response;
                    })

                    ->modalActions([
                        Tables\Actions\Action::make('Descargar PDF')
                            ->label('Descargar PDF')
                            ->icon($icon = 'heroicon-o-document-text')
                            ->color('danger')
                            ->submit('submit', 'action', 'pdf'),
                        Tables\Actions\Action::make('Cancelar')
                            ->color('secondary')
                            ->close(),
                    ]),

                Tables\Actions\Action::make("FiltrosExcel")
                    ->label('Excel')
                    ->icon($icon2 = 'heroicon-o-table-cells')
                    ->color('success')
                    ->form([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio'),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin'),


                        Select::make('marca')
                            ->label('Marca por: ')
                            ->relationship('marca', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        Select::make('categoria')
                            ->label('Categoria por: ')
                            ->relationship('categoria', 'nombre') // Relación con el modelo Marca
                            ->preload() // Carga las opciones dinámicamente
                            ->searchable(), // Permite buscar entre las opciones

                        // Campo oculto para indicar la acción (PDF o Excel)
                        Forms\Components\Hidden::make('action'),

                    ])

                    ->action(function (array $data) {
                        // Lógica para manejar los datos del formulario
                        // Por ejemplo, puedes aplicar los filtros aquí
                        //Log::info('Datos del filtro:', $data);
                        $response = redirect()->route('productos.excel.filtrado', $data);
                        if (session('error')) {
                            Notification::make()
                                ->title(session('error'))
                                ->danger()
                                ->send();
                        }

                        return $response;
                    })

                    ->modalActions([
                        Tables\Actions\Action::make('Descargar Excel')
                            ->label('Descargar EXCEL')
                            ->icon($icon2 = 'heroicon-o-table-cells')
                            ->color('success')
                            ->submit('submit'),
                        Tables\Actions\Action::make('Cancelar')
                            ->color('secondary')
                            ->close(),
                    ])


            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make("Descargar BARCODE")
                    ->label('CodBar')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn(Producto $record) => route('productos.codebar.individual', $record))
                    ->openUrlInNewTab()
                    ->color("primary"),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make("Descargar PDF")
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Producto $record) => route('download.one', $record))
                    ->openUrlInNewTab()
                    ->color("danger"),

            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProductos::route('/create'),
            'edit' => Pages\EditProductos::route('/{record}/edit'),
            'overview' => Pages\Reportes::route('/overview'),
        ];
    }
    // public function store(Request $request)
    // {
    //     Log::info('Solicitud recibida en store:', $request->all());

    //     DB::beginTransaction();
    //     try {
    //         // Validar los datos del producto
    //         $validatedData = $request->validate([
    //             'codigo' => 'required|string|max:255',
    //             'nombre' => 'required|string|max:255',
    //             'descripcion' => 'nullable|string',
    //             'fecha' => 'required|date',
    //             'imagen' => 'nullable|string',
    //             'id_marca' => 'required|exists:marcas,id',
    //             'id_presentacion' => 'required|exists:presentaciones,id',
    //             'id_categorias' => 'required|exists:categorias,id',
    //             'tipo' => 'required|in:bien,servicio',
    //             'precio_compra' => 'required|numeric',
    //             'precio_venta' => 'required|numeric',
    //             'precio_mayoreo' => 'required|numeric',
    //             'impuestos' => 'nullable|array', // Asegúrate de que los impuestos se envíen como un array
    //             'impuestos.*.id_impuesto' => 'required|exists:impuestos_unidad_gravable,id', // Validar cada impuesto
    //         ]);

    //         Log::info('Datos validados:', $validatedData);

    //         // Crear el producto
    //         $producto = Producto::create($validatedData);
    //         Log::info('Producto creado con éxito:', [
    //             'producto_id' => $producto->id,
    //             'producto_data' => $producto->toArray(),
    //         ]);

    //         // Guardar los impuestos en la tabla pivote
    //         if (!empty($validatedData['impuestos'])) {
    //             Log::info('Datos de impuestos recibidos:', $validatedData['impuestos']);

    //             $impuestosIds = collect($validatedData['impuestos'])->pluck('id_impuesto')->toArray();
    //             Log::info('IDs de impuestos a asignar:', $impuestosIds);

    //             $producto->impuestos()->attach($impuestosIds);
    //             Log::info('Impuestos asignados correctamente al producto ID: ' . $producto->id);
    //         } else {
    //             Log::info('No se recibieron impuestos para asignar.');
    //         }

    //         DB::commit();
    //         Log::info('Transacción completada con éxito.');

    //         return response()->json([
    //             'message' => 'Producto creado correctamente',
    //             'producto' => $producto,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error al crear el producto:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response()->json([
    //             'error' => 'Error al crear el producto',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    // public function update(Request $request, $id)
    // {
    //     Log::info('Solicitud recibida en update:', $request->all());

    //     DB::beginTransaction();
    //     try {
    //         // Validar los datos del producto
    //         $validatedData = $request->validate([
    //             'codigo' => 'required|string|max:255',
    //             'nombre' => 'required|string|max:255',
    //             'descripcion' => 'nullable|string',
    //             'fecha' => 'required|date',
    //             'imagen' => 'nullable|string',
    //             'id_marca' => 'required|exists:marcas,id',
    //             'id_presentacion' => 'required|exists:presentaciones,id',
    //             'id_categorias' => 'required|exists:categorias,id',
    //             'tipo' => 'required|in:bien,servicio',
    //             'precio_compra' => 'required|numeric',
    //             'precio_venta' => 'required|numeric',
    //             'precio_mayoreo' => 'required|numeric',
    //             'impuestos' => 'nullable|array', // Asegúrate de que los impuestos se envíen como un array
    //             'impuestos.*.id_impuesto' => 'required|exists:impuestos_unidad_gravable,id', // Validar cada impuesto
    //         ]);

    //         Log::info('Datos validados:', $validatedData);

    //         // Buscar el producto
    //         $producto = Producto::findOrFail($id);
    //         Log::info('Producto encontrado:', [
    //             'producto_id' => $producto->id,
    //             'producto_data' => $producto->toArray(),
    //         ]);

    //         // Actualizar el producto
    //         $producto->update($validatedData);
    //         Log::info('Producto actualizado con éxito:', [
    //             'producto_id' => $producto->id,
    //             'producto_data' => $producto->toArray(),
    //         ]);

    //         // Sincronizar los impuestos en la tabla pivote
    //         if (!empty($validatedData['impuestos'])) {
    //             Log::info('Datos de impuestos recibidos:', $validatedData['impuestos']);

    //             $impuestosIds = collect($validatedData['impuestos'])->pluck('id_impuesto')->toArray();
    //             Log::info('IDs de impuestos a sincronizar:', $impuestosIds);

    //             $producto->impuestos()->sync($impuestosIds);
    //             Log::info('Impuestos sincronizados correctamente al producto ID: ' . $producto->id);
    //         } else {
    //             Log::info('No se recibieron impuestos para sincronizar.');
    //         }

    //         DB::commit();
    //         Log::info('Transacción completada con éxito.');

    //         return response()->json([
    //             'message' => 'Producto actualizado correctamente',
    //             'producto' => $producto,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error al actualizar el producto:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response()->json([
    //             'error' => 'Error al actualizar el producto',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
