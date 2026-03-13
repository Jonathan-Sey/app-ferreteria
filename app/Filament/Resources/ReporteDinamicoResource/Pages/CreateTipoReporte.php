<?php

namespace App\Filament\Resources\ReporteDinamicoResource\Pages;

use App\Filament\Resources\ReporteDinamicoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class CreateTipoReporte extends CreateRecord
{
    protected static string $resource = ReporteDinamicoResource::class;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('tipo')
                ->label('Tipo de Reporte')
                ->required(),
            Textarea::make('descripcion')
                ->label('Descripción')
                ->required(),
        ];
    }
}