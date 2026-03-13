<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ReporteDinamicoResource\Pages;
use App\Filament\Resources\ReporteDinamicoResource\RelationManagers;
use App\Models\ReporteDinamico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Button;

class ReporteDinamicoResource extends Resource
{
    protected static ?string $model = ReporteDinamico::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 14;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección para seleccionar el tipo de documento
                Section::make('Tipo de Documento')
                    ->schema([
                        Select::make('tipo_documento')
                        ->label('Selecciona el tipo de documento')
                        ->options([
                            'factura' => 'Factura',
                            'reporte' => 'Reporte',
                            'contrato' => 'Contrato',
                            'docvario' => 'Documentos Varios', // Opción para crear un nuevo tipo
                        ])
                        ->required()
                        ->reactive(),
                    TextInput::make('docvario')
                        ->label('Nuevo Tipo de Documento')
                        ->required()
                        ->visible(fn (callable $get) => $get('tipo_documento') === 'docvario'),
                    Textarea::make('nuevo_tipo_descripcion')
                        ->label('Descripción del nuevo tipo de reporte')
                        ->rows(3)
                        ->required()
                        ->visible(fn (callable $get) => $get('tipo_documento') === 'docvario'),
                    FileUpload::make('archivo_documento')
                        ->label('Cargar Archivo del Documento')
                        ->disk('s3')
                        ->directory('documentos_varios')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(2048) // 2MB
                        ->visible(fn (callable $get) => $get('tipo_documento') === 'docvario'),

                    ]),

                // Campos para Facturas
                Section::make('Información de la Factura')
                    ->schema([
                        TextInput::make('numero_factura')
                            ->label('Número de Factura')
                            ->required()
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'factura'),
                        Textarea::make('descripcion_factura')
                            ->label('Descripción De Factura')
                            ->rows(3)
                            ->required()
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'docvario'),
                        FileUpload::make('archivo_factura')
                            ->label('Cargar Factura')
                            ->disk('s3')
                            ->directory('facturas')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/pdf', // PDF
                                'application/msword', // Word (.doc)
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word (.docx)
                                'application/vnd.ms-excel', // Excel (.xls)
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel (.xlsx)
                            ])
                            ->maxSize(1024) // 1MB
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'factura'),
                    ])
                    ->visible(fn (callable $get) => $get('tipo_documento') === 'factura'),

                // Campos para Reportes
                Section::make('Información del Reporte')
                    ->schema([
                        TextInput::make('nombre_reporte')
                            ->label('Nombre del Reporte')
                            ->required()
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'reporte'),
                        Textarea::make('descripcion_reporte')
                            ->label('Descripción del Reporte')
                            ->rows(3)
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'reporte'),
                        FileUpload::make('archivo_reporte')
                            ->label('Cargar Reporte')
                            ->disk('s3')
                            ->directory('reportes')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/pdf', // PDF
                                'application/msword', // Word (.doc)
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word (.docx)
                                'application/vnd.ms-excel', // Excel (.xls)
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel (.xlsx)
                            ])
                            ->maxSize(2048) // 2MB
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'reporte'),
                    ])
                    ->visible(fn (callable $get) => $get('tipo_documento') === 'reporte'),

                // Campos para Contratos
                Section::make('Información del Contrato')
                    ->schema([
                        TextInput::make('nombre_contrato')
                            ->label('Nombre del Contrato')
                            ->required()
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'contrato'),
                        Textarea::make('descripcion_contrato')
                            ->label('Descripción del Contrato')
                            ->rows(3)
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'contrato'),
                        FileUpload::make('archivo_contrato')
                            ->label('Cargar Contrato')
                            ->disk('s3')
                            ->directory('contratos')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/pdf', // PDF
                                'application/msword', // Word (.doc)
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word (.docx)
                                'application/vnd.ms-excel', // Excel (.xls)
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel (.xlsx)
                            ])
                            ->maxSize(2048) // 2MB
                            ->visible(fn (callable $get) => $get('tipo_documento') === 'contrato'),
                    ])
                    ->visible(fn (callable $get) => $get('tipo_documento') === 'contrato'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_factura')
                    ->label('Número de Factura')
                    ->visible(fn ($record) => optional($record)->tipo_documento === 'factura'),
                Tables\Columns\TextColumn::make('nombre_reporte')
                    ->label('Nombre del Reporte')
                    ->visible(fn ($record) => optional($record)->tipo_documento === 'reporte'),
                Tables\Columns\TextColumn::make('nombre_contrato')
                    ->label('Nombre del Contrato')
                    ->visible(fn ($record) => optional($record)->tipo_documento === 'contrato'),
                Tables\Columns\TextColumn::make('nombre_bono')
                    ->label('Nombre del Bono')
                    ->visible(fn ($record) => optional($record)->tipo_documento === 'bono'),
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
            'index' => Pages\ListReporteDinamicos::route('/'),
            'create' => Pages\CreateReporteDinamico::route('/create'),
            'edit' => Pages\EditReporteDinamico::route('/{record}/edit'),
        ];
    }
}