<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Sucursal;
use App\Models\User;
use Doctrine\DBAL\Schema\View;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'custom-user-circle-plus';
    protected static ?int $navigationSort = 13;


    //protected static ?string $navigationGroup = 'Administracion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->schema([
                        TextInput::make('nombre1')
                            ->label('Primer Nombre')
                            ->required()
                            ->columnSpan(4)
                            ->placeholder('John'),
                        TextInput::make('nombre2')
                            ->label('Segundo Nombre')
                            ->columnSpan(4)
                            ->placeholder('Sean'),
                        TextInput::make('nombre3')
                            ->label('Tercer Nombre')
                            ->columnSpan(4)
                            ->placeholder('Doe'),
                        TextInput::make('apellido1')
                            ->label('Primer Apellido')
                            ->columnSpan(4)
                            ->required()
                            ->placeholder('Doe'),
                        TextInput::make('apellido2')
                            ->label('Segundo Apellido')
                            ->columnSpan(4)
                            ->placeholder('Appleseed'),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->columnSpan(4)
                            ->placeholder('example@dominio.com')
                            ->unique(ignoreRecord: true),
                        Select::make('sucursales')
                            ->label('Sucursales')
                            ->multiple()
                            ->required()
                            ->columnSpan(6)
                            ->relationship('sucursales', 'nombre')
                            ->preload()
                            ->searchable(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->columnSpan(6)
                            ->required()
                            ->placeholder('********')
                            ->hiddenOn('edit'),
                    ])
                    ->columns(12),
                Section::make('Roles y Permisos')
                    ->schema([
                        // Select::make('roles')
                        //     ->multiple()
                        //     ->relationship('roles', 'name')
                        //     ->preload()
                        //     ->searchable()
                        //     ->label('Roles')
                        //     ->helperText('Los permisos del rol se asignarán automáticamente'),

                        // Select::make('direct_permissions')
                        //     ->multiple()
                        //     ->relationship('permissions', 'name')
                        //     ->preload()
                        //     ->searchable()
                        //     ->label('Permisos Adicionales')
                        //     ->helperText('Estos permisos se agregan a los del rol'),
                        // Using CheckboxList Component
                        Forms\Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable(),
                    ]),

                Section::make('Estado')
                    ->schema([
                        Select::make('estado')
                            ->options([
                                1 => 'Activo',
                                0 => 'Inactivo',
                            ])
                            ->default(1)
                            ->required(),
                    ]),
            ]);
    }
    // Método para manejar la creación y actualización de usuarios con sus sucursales
    public function create(array $data): User
    {
        $user = User::create([
            'nombre1' => $data['nombre1'],
            'nombre2' => $data['nombre2'] ?? null,
            'nombre3' => $data['nombre3'] ?? null,
            'apellido1' => $data['apellido1'],
            'apellido2' => $data['apellido2'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Sincroniza las sucursales
        if (isset($data['sucursales'])) {
            $user->sucursales()->sync($data['sucursales']);
        }

        return $user;
    }
    // Método para manejar la actualización
    public function update(array $data): User
    {
        $user = $this->getRecord();

        $user->update([
            'nombre1' => $data['nombre1'],
            'nombre2' => $data['nombre2'] ?? null,
            'nombre3' => $data['nombre3'] ?? null,
            'apellido1' => $data['apellido1'],
            'apellido2' => $data['apellido2'] ?? null,
            'email' => $data['email'],
        ]);

        // Sincroniza las sucursales
        if (isset($data['sucursales'])) {
            $user->sucursales()->sync($data['sucursales']);
        }

        return $user;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre1')
                    ->searchable(isIndividual: true)
                    ->label('Primer Nombre'),
                TextColumn::make('nombre2')
                    ->searchable()
                    ->label('Segundo Nombre'),
                // TextColumn::make('nombre3')
                //     ->searchable()
                //     ->label('Tercer Nombre'),
                TextColumn::make('apellido1')
                    ->searchable()
                    ->label('Primer Apellido'),
                // TextColumn::make('apellido2')
                //     ->searchable()
                //     ->label('Segundo Apellido'),
                TextColumn::make('email')
                    ->searchable(isIndividual: true)
                    ->label('Email'),
                IconColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->boolean()
                    ->state(fn($record) => ! is_null($record->email_verified_at))
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('sucursales.nombre')
                    ->label('Sucursales')
                    ->badge()
                    ->separator(','),
                TextColumn::make('roles.name')
                    ->badge()
                    ->label('Roles')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                //ver
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    protected function afterCreate(): void
    {
        $user = $this->getRecord();

        // Envía el correo de verificación
        $user->sendEmailVerificationNotification();
    }
}
