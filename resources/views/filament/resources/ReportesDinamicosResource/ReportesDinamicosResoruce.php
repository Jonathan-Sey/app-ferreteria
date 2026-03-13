<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportesDinamicosResource\Pages;
use App\Models\ReporteDinamico;
use App\Filament\Resources\ReportesDinamicosResource\Pages\ListReportesDinamicos;
use App\Filament\Resources\ReportesDinamicosResource\Pages\CreateReportesDinamicos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;

class ReportesDinamicosResource extends Resource
{
    protected static ?string $model = ReporteDinamico::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    
    protected static ?string $navigationGroup = 'Reportes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),
                Textarea::make('contenido')
                    ->required(),
                   
 
                FileUpload::make('attachment')
                    ->disk('s3')
                    ->directory('form-attachments')
                    ->visibility('private')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('titulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i'),
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
            'index' => Pages\ListReportesDinamicos::route('/'),
            'create' => Pages\CreateReportesDinamicos::route('/create'),
            'edit' => Pages\EditReportesDinamicos::route('/{record}/edit'),
        ];
    }
}