<?php

namespace App\Filament\Resources\EntidadResource\Pages;

use App\Filament\Resources\EntidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntidads extends ListRecords
{
    protected static string $resource = EntidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
