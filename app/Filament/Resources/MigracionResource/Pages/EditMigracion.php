<?php

namespace App\Filament\Resources\MigracionResource\Pages;

use App\Filament\Resources\MigracionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMigracion extends EditRecord
{
    protected static string $resource = MigracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
