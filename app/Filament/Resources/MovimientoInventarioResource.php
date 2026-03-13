<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientoInventarioResource\Pages;
use App\Filament\Resources\MovimientoInventarioResource\RelationManagers;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Sucursal;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Reactive;
use Maatwebsite\Excel\Facades\Excel;

use function Laravel\Prompts\select;

class MovimientoInventarioResource extends Resource
{
    protected static ?string $model = MovimientoInventario::class;

    protected static ?string $navigationIcon = 'custom-hand-withdraw';
    protected static ?int $navigationSort = 7;


    //protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('fecha')
                    ->default(now())
                    ->required(),
                Select::make('id_sucursal')
                    ->label('Sucursal')
                    ->options(function () {
                        return Sucursal::all()->pluck('nombre', 'id');
                    })
                    ->searchable()
                    ->required(),
                Select::make('tipo_movimiento')
                    ->options([
                        'ENTRADA' => 'Inventario inicial',
                        'SALIDA' => 'Salida/Ajuste',
                        'AJUSTE' => 'Entrada/Ajuste',
                        'TRASLADO' => 'Traslado',
                    ])
                    ->reactive()
                    ->required(),
                TextInput::make('numero_documento')->maxLength(50),
                Textarea::make('observaciones'),
                Select::make('sucursal_destino')
                    ->options(function () {
                        return Sucursal::all()->pluck('nombre', 'id');
                    })
                    ->nullable()
                    ->visible(fn(callable $get) => $get('tipo_movimiento') === 'TRASLADO')
                    ->required(fn(callable $get) => $get('tipo_movimiento') === 'TRASLADO'),
                Repeater::make('detalles')
                    ->relationship('detalles')
                    ->schema([
                        TextInput::make('codigo')
                            ->label('Código de Barras')
                            ->placeholder('Escanea o ingresa el código de barras')
                            ->reactive()
                            ->hiddenOn('view')
                            ->columnSpan(3)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $producto = Producto::where('codigo', $state)->first();
                                if ($producto) {
                                    $set('id_producto', $producto->id);
                                }
                            }),
                        Select::make('id_producto')
                            ->options(function () {
                                return Producto::all()->mapWithKeys(function ($producto) {
                                    return [$producto->id => "{$producto->nombre} ({$producto->codigo})"];
                                });
                            })
                            ->required()
                            ->reactive()
                            ->columnSpan(5)
                            ->searchable()
                            ->distinct()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $producto = Producto::where('id', $state)->first();
                                if ($producto) {
                                    $set('codigo', $producto->codigo);
                                }
                            }),
                        TextInput::make('cantidad')
                            ->numeric()
                            ->columnSpan(2)
                            ->required(),
                        TextInput::make('costo_unitario')
                            ->numeric()
                            ->columnSpan(2)
                            ->nullable(),
                    ])
                    ->required()
                    ->columnSpan('full')
                    ->columns(12)
                    ->reorderableWithDragAndDrop(true)
                    ->itemLabel(fn(array $state): ?string => $state['nombre'] ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->sortable()->searchable(),
                TextColumn::make('sucursal.nombre')->sortable()->searchable(),
                TextColumn::make('tipo_movimiento')
                    ->sortable()
                    ->searchable()
                    ->label('Tipo')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'ENTRADA' => 'Inventario inicial',
                            'SALIDA' => 'Salida/Ajuste',
                            'AJUSTE' => 'Entrada/Ajuste',
                            'TRASLADO' => 'Traslado',
                            default => $state,
                        };
                    }),
                TextColumn::make('numero_documento')->sortable()->searchable()->label('No. Doc'),
                TextColumn::make('observaciones')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('pdf')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha Inicio'),
                            
                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha Fin'),
                            
                        Forms\Components\Select::make('sucursal')
                            ->label('Sucursal')
                            ->options(function () {
                                return Sucursal::all()->pluck('nombre', 'id');
                            })
                            ->searchable()
                            ->nullable(), // Sucursal no es obligatorio
                        Forms\Components\Select::make('tipo_movimiento')
                            ->label('Tipo de Movimiento')
                            ->options([
                                'inventario_inicial' => 'Inventario inicial',
                                'entrada_salida' => 'Entrada y Salida (ajustes)',
                                'traslados' => 'Traslados',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Construir la URL con los parámetros del formulario
                        $url = route('movimientos.pdf.filtrado', [
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_fin' => $data['fecha_fin'],
                            'sucursal' => $data['sucursal'], // Puede ser null
                            'tipo_movimiento' => $data['tipo_movimiento'],
                        ]);
    
                        // Redirigir a la URL para generar el PDF
                        return redirect()->away($url);
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Agregar la acción de eliminar
                Tables\Actions\Action::make('Descargar PDF')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (MovimientoInventario $record) => route('movimientos.pdf.individual', $record))
                    ->openUrlInNewTab()
                    ->color('danger'),
                Tables\Actions\Action::make('Descargar Excel')
                    ->label('Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (MovimientoInventario $record) {
                        // Exportar el movimiento a Excel usando el facade Excel
                        return Excel::download(
                            new \App\Exports\MovimientoInventarioExport($record),
                            "movimiento_{$record->id}.xlsx"
                        );
                    }),
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
            'index' => Pages\ListMovimientoInventarios::route('/'),
            'create' => Pages\CreateMovimientoInventario::route('/create'),
            'edit' => Pages\EditMovimientoInventario::route('/{record}/edit'),
        ];
    }
}