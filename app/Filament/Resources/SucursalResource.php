<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SucursalResource\Pages;
use App\Filament\Resources\SucursalResource\RelationManagers;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Sucursal;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class SucursalResource extends Resource
{
    protected static ?string $model = Sucursal::class;

    protected static ?string $navigationIcon = 'custom-storefront';
    protected static ?int $navigationSort = 13;


    //protected static ?string $navigationGroup = 'Administracion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre'),
                TextInput::make('direccion'),
                TextInput::make('telefono')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                Select::make('tipo')
                    ->options([
                        '1' => 'Tienda',
                        '2' => 'Bodega',
                    ]),
                Select::make('pais_id')
                    ->label('País')
                    ->options(Pais::all()->pluck('nombre', 'id')) // Cargar opciones de países
                    ->reactive() // Escuchar cambios
                    ->afterStateUpdated(function (callable $set) {
                        Log::info('País relacionado458:', ['pais_id' => 0]);
                        $set('departamento_id', null);
                        $set('id_municipio', null);
                    })
                    ->default(function (?Sucursal $record) {
                        Log::info('País relacionado1:', ['pais_id' => $record]);
                        if ($record && $record->municipio) {
                            Log::info('País relacionado2:', ['pais_id' => $record->municipio]);
                            return $record->municipio->departamento->pais_id;
                        }
                        Log::info('País relacionado3:', ['pais_id' => 0]);
                        return null;
                    })
                    ->required()->searchable(),

                // Campo para seleccionar el departamento
                Select::make('departamento_id')
                    ->label('Departamento')
                    ->options(function (callable $get) {
                        // Cargar departamentos según el país seleccionado
                        $paisId = $get('pais_id');
                        return $paisId ? Departamento::where('pais_id', $paisId)->pluck('nombre', 'id') : [];
                    })
                    ->reactive() // Escuchar cambios
                    ->afterStateUpdated(fn(callable $set) => $set('id_municipio', null)) // Limpiar selección del municipio
                    ->default(function ($record) {
                        // Cargar el departamento basado en el municipio del cliente
                        return $record?->municipio?->departamento?->id;
                    })
                    ->required()->searchable(),
                Select::make('id_municipio')
                    ->label('Municipio')
                    ->options(function (callable $get) {
                        // Cargar municipios según el departamento seleccionado
                        $departamentoId = $get('departamento_id');
                        return $departamentoId ? Municipio::where('departamento_id', $departamentoId)->pluck('nombre', 'id') : [];
                    })
                    ->default(fn($record) => $record?->id_municipio)
                    ->required()->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('direccion')->searchable(),
                TextColumn::make('telefono'),

                TextColumn::make('tipo')
                    ->label('Tipo de Entidad')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => 'Tienda',
                            2 => 'Bodega',
                            default => 'No especifica',
                        };
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSucursals::route('/'),
            'create' => Pages\CreateSucursal::route('/create'),
            'edit' => Pages\EditSucursal::route('/{record}/edit'),
            'change' => Pages\CambiarSucursal::route('/change'),
        ];
    }
}
