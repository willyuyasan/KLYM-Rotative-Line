<?php

namespace App\Filament\Resources\RotativelineResource\Pages;

use App\Filament\Resources\RotativelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRotativeline extends EditRecord
{
    protected static string $resource = RotativelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
