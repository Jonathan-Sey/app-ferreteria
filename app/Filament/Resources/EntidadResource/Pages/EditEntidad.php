<?php

namespace App\Filament\Resources\EntidadResource\Pages;

use App\Filament\Resources\EntidadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntidad extends EditRecord
{
    protected static string $resource = EntidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
