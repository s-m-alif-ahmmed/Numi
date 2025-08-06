<?php

namespace App\Filament\Resources\TempRecipeResource\Pages;

use App\Filament\Pages\Actions\ImportRecipeAction;
use App\Filament\Resources\TempRecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTempRecipes extends ListRecords
{
    protected static string $resource = TempRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportRecipeAction::make(),
        ];
    }
}
