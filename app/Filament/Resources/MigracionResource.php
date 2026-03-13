<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MigracionResource\Pages;
use App\Models\Migracion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class MigracionResource extends Resource
{
    protected static ?string $model = Migracion::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';
    protected static ?int $navigationSort = 14;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('attachment')
                    ->disk('public')
                    ->directory('form-attachments')
                    ->visibility('private'),
                Select::make('tabla')
                    ->label('Tabla de destino')
                    ->options(function () {
                        $dbName = config('database.connections.mysql.database');
                        $rows = DB::select("SHOW TABLES");
                        $options = [];
                        foreach ($rows as $row) {
                            $key = "Tables_in_{$dbName}";
                            $options[$row->$key] = $row->$key;
                        }

                        return $options;
                        console.log($options);
                    
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMigracions::route('/'),
            'create' => Pages\CreateMigracion::route('/create'),
            'edit' => Pages\EditMigracion::route('/{record}/edit'),
        ];
    }
}