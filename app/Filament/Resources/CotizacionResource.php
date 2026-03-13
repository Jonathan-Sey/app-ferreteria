<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotizacionResource\Pages;
use App\Filament\Resources\CotizacionResource\RelationManagers;
use App\Models\Cotizacion;
use App\Models\Entidad;
use App\Models\Producto;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CotizacionResource extends Resource
{
    protected static ?string $model = Cotizacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Icono del menú
    protected static ?string $navigationLabel = 'Cotizaciones'; // Etiqueta del menú
    protected static ?string $modelLabel = 'Cotización'; // Etiqueta singular
    protected static ?string $pluralModelLabel = 'Cotizaciones'; // Etiqueta plural
    //protected static ?string $navigationGroup = 'Ventas'; // Grupo del menú (opcional)
    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Información de Cotización')
                ->schema([
                    // Primera fila - Cliente y CF
                    Grid::make()
                        ->schema([
                            Select::make('id_cliente')
                                ->label('Cliente')
                                // ->createOptionForm([
                                //     TextInput::make('nombre')
                                //         ->required()
                                //         ->maxLength(255),
                                // ])
                                ->relationship('cliente', 'nombre')
                                ->options(function () {
                                    return Entidad::where('es_cliente', 1)->pluck('nombre', 'id');
                                })
                                ->required(function (callable $get) {
                                    return !$get('es_consumidor_final');
                                })
                                ->searchable()
                                ->hidden(fn(callable $get) => $get('es_consumidor_final'))
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
                        ->default('Consumidor Final')
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

                    // Segunda fila - Sucursal y Fecha
                    Grid::make()
                        ->schema([
                            Select::make('id_sucursal')
                                ->label('Sucursal')
                                ->options(function () {
                                    $user = Auth::user();
                                    return $user->sucursales->pluck('nombre', 'id');
                                })
                                ->default(function () {
                                    $user = Auth::user();
                                    return $user->sucursales->count() === 1 ? $user->sucursales->first()->id : null;
                                })
                                ->required()
                                ->columnSpan(1),

                            DatePicker::make('fecha_emision')
                                ->label('Fecha de Emisión')
                                ->default(now())
                                ->required()
                                ->columnSpan(1),
                        ])
                        ->columns(2),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->columnSpanFull(),

                    // Sección de Items
                    Forms\Components\Repeater::make('items')
                        ->columnSpan('full')
                        ->itemLabel(function (array $state): ?string {
                            $productoNombre = '';
                            if (!empty($state['id_producto'])) {
                                $producto = Producto::find($state['id_producto']);
                                if ($producto) {
                                    $productoNombre = $producto->nombre;
                                }
                            }
                        
                            return sprintf(
                                '📦 %s | 💵 Q  %s',
                                $productoNombre ?? 'N/A',
                                number_format($state['total'] ?? 0, 2)
                            );
                        })
                        ->relationship('items')
                        ->schema([
                            Forms\Components\TextInput::make('codigo')
                                ->label("Codigo")
                                ->placeholder('Escanea o ingresa el código de barras')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    $producto = Producto::where('codigo', $state)->first();
                                    if ($producto) {
                                        $set('id_producto', $producto->id);
                                    }
                                })
                                ->columnSpan(1),
                            Forms\Components\Select::make('id_producto')
                                ->label('Producto')
                                ->columnSpan(2)
                                ->relationship('producto', 'nombre')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    $producto = Producto::where('id', $state)->first();
                                    if ($producto) {
                                        $set('codigo', $producto->codigo);
                                    }
                                })
                                ->searchable(),
                            Forms\Components\TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $precioUnitario = $get('precio_unitario') ?? 0;
                                    $descuento = $get('descuento') ?? 0;
                                    $total = ($state * $precioUnitario) - $descuento;
                                    $set('total', $total);
                                }),
                            Forms\Components\TextInput::make('precio_unitario')
                                ->label('Precio Unitario')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $cantidad = $get('cantidad') ?? 0;
                                    $descuento = $get('descuento') ?? 0;
                                    $total = ($cantidad * $state) - $descuento;
                                    $set('total', $total);
                                }),
                            Forms\Components\TextInput::make('descuento')
                                ->label('Descuento')
                                ->numeric()
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $cantidad = $get('cantidad') ?? 0;
                                    $precioUnitario = $get('precio_unitario') ?? 0;
                                    $total = ($cantidad * $precioUnitario) - $state;
                                    $set('total', $total);
                                }),
                            Forms\Components\TextInput::make('total')
                                ->label('Total')
                                ->numeric()
                                ->disabled(),
                        ])
                        ->columns(2)
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $cantidad = $data['cantidad'] ?? 0;
                            $precioUnitario = $data['precio_unitario'] ?? 0;
                            $descuento = $data['descuento'] ?? 0;
                            $data['total'] = ($cantidad * $precioUnitario) - $descuento;
                            return $data;
                        }),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columnas de la tabla
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Fecha de Emisión')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('GTQ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                
                // Tables\Columns\TextColumn::make('estado')
                //     ->label('Estado')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'pendiente' => 'warning',
                //         'aprobada' => 'success',
                //         'rechazada' => 'danger',
                //     }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                // Filtros de la tabla
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label("PDF")
                    ->color("danger")
                    
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Cotizacion $record) => route('download.cotizacion.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relaciones (opcional)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCotizacions::route('/'),
            'create' => Pages\CreateCotizacion::route('/create'),
            'edit' => Pages\EditCotizacion::route('/{record}/edit'),
        ];
    }
}