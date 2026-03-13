<?php

namespace App\Filament\Resources\MigracionResource\Pages;

use App\Filament\Resources\MigracionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMigracions extends ListRecords
{
    protected static string $resource = MigracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
