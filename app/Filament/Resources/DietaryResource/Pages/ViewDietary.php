<?php

namespace App\Filament\Resources\DietaryResource\Pages;

use App\Filament\Resources\DietaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDietary extends ViewRecord
{
    protected static string $resource = DietaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
