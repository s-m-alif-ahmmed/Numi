<?php

namespace App\Filament\Resources\DietaryResource\Pages;

use App\Filament\Resources\DietaryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDietary extends CreateRecord
{
    protected static string $resource = DietaryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
