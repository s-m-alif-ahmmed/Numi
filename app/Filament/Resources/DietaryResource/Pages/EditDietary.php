<?php

namespace App\Filament\Resources\DietaryResource\Pages;

use App\Filament\Resources\DietaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDietary extends EditRecord
{
    protected static string $resource = DietaryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
