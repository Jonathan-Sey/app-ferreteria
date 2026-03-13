<?php

namespace App\Filament\Resources\ReporteUtilidadesResource\Pages;

use App\Filament\Resources\ReporteUtilidadesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReporteUtilidades extends ManageRecords
{
    protected static string $resource = ReporteUtilidadesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
