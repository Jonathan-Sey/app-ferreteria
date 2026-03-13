<?php

namespace App\Filament\Resources\ReporteDinamicoResource\Pages;

use App\Filament\Resources\ReporteDinamicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReporteDinamicos extends ListRecords
{
    protected static string $resource = ReporteDinamicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
