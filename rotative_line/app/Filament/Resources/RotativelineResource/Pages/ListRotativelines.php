<?php

namespace App\Filament\Resources\RotativelineResource\Pages;

use App\Filament\Resources\RotativelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRotativelines extends ListRecords
{
    protected static string $resource = RotativelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
