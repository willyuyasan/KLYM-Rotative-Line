<?php

namespace App\Filament\Resources\RotativelineResource\Pages;

use App\Filament\Resources\RotativelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRotativeline extends ViewRecord
{
    protected static string $resource = RotativelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
