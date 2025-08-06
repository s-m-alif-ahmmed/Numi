<?php

namespace App\Filament\Resources\DietaryResource\Pages;

use App\Filament\Resources\DietaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDietaries extends ListRecords
{
    protected static string $resource = DietaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
