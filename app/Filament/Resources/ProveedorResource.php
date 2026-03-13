<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Filament\Resources\ProveedorResource\RelationManagers;
use App\Models\AfiliacionIva;
use App\Models\Departamento;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
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

class ProveedorResource extends Resource
{
    protected static ?string $model = Entidad::class;

    protected static ?string $navigationIcon = 'custom-truck';

   // protected static ?string $navigationGroup = 'Personas/Entidades';

    protected static ?string $navigationParentItem = 'Entidades';


    protected static string $title = 'Proveedores';

    protected static ?string $modelLabel = 'proveedor';



    protected static ?string $pluralModelLabel = 'proveedores';

    protected static ?string $navigationLabel = 'Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('es_proveedor')
                    ->default(true),
                Select::make('tipo_entidad')
                    ->options([
                        '1' => 'Natural',
                        '2' => 'Jurídico',
                    ])
                    ->default('1')
                    ->required()
                    ->columnSpan(3),
                TextInput::make('codigo_interno')
                    ->columnSpan(3),
                Select::make('id_afiliacion_iva')
                    ->label('Afiliacion Iva')
                    ->options(AfiliacionIva::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->columnSpan(3),
                TextInput::make('cod_establecimiento')
                    ->columnSpan(3),
                TextInput::make('correo')
                    ->email()
                    ->columnSpan(3),
                TextInput::make('nit')
                    ->columnSpan(3)
                    ->required(),
                TextInput::make('telefono')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->columnSpan(3),
                TextInput::make('nombre')
                    ->required()
                    ->columnSpan(8),
                TextInput::make('nombre_comercial')
                    ->columnSpan(4),
                TextInput::make('direccion')
                    ->columnSpan(8),
                TextInput::make('codigo_postal')
                    ->columnSpan(4),
                Select::make('pais_id')
                    ->label('País')
                    ->options(Pais::all()->pluck('nombre', 'id'))
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        Log::info('País relacionado:', ['pais_id' => 0]);
                        $set('departamento_id', null);
                        $set('id_municipio', null);
                    })
                    ->default(function (?Entidad $record) {
                        Log::info('País relacionado:', ['pais_id' => $record]);
                        if ($record && $record->municipio) {
                            Log::info('País relacionado:', ['pais_id' => $record->municipio]);
                            return $record->municipio->departamento->pais_id;
                        }
                        Log::info('País relacionado:', ['pais_id' => 0]);
                        return null;
                    })
                    ->nullable()
                    ->searchable()
                    ->columnSpan(4),
                Select::make('departamento_id')
                    ->label('Departamento')
                    ->options(function (callable $get) {
                        $paisId = $get('pais_id');
                        return $paisId ? Departamento::where('pais_id', $paisId)->pluck('nombre', 'id') : [];
                    })
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('id_municipio', null))
                    ->default(function ($record) {
                        return $record?->municipio?->departamento?->id;
                    })
                    ->nullable()
                    ->searchable()
                    ->columnSpan(4),
                Select::make('id_municipio')
                    ->label('Municipio')
                    ->options(function (callable $get) {
                        $departamentoId = $get('departamento_id');
                        return $departamentoId ? Municipio::where('departamento_id', $departamentoId)->pluck('nombre', 'id') : [];
                    })
                    ->default(fn($record) => $record?->id_municipio)
                    ->nullable()
                    ->searchable()
                    ->columnSpan(4),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('tipo_entidad')
                    ->label('Tipo de Entidad')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            "1" => 'Natural',
                            "2" => 'Jurídico',
                            default => 'No especifica',
                        };
                    }),
                TextColumn::make('correo'),
                TextColumn::make('nit')->searchable(),
                TextColumn::make('telefono'),
                TextColumn::make('nombre_comercial')->searchable(),
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
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('es_proveedor', true);
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
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
