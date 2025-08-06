<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Support\Facades\File;

class ApiSetting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.api-setting';

    protected static ?string $navigationLabel = 'API Settings';

    protected static ?string $navigationGroup = 'Settings';

    public $api_key;

    public function mount()
    {
        $setting = \App\Models\ApiSetting::first();
        $this->api_key = $setting->api_key ?? env('SPOONACULAR_KEY');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('api_key')
                ->label('Spoonacular API Key')
                ->required()
                ->maxLength(255),
        ];
    }

    public function saveApi()
    {
        $setting = \App\Models\ApiSetting::first();

        if (!$setting) {
            $setting = \App\Models\ApiSetting::create([
                'api_key' => $this->api_key,
            ]);
        } else {
            $setting->update([
                'api_key' => $this->api_key,
            ]);
        }

        $this->updateEnvFile();

        Notification::make()
            ->body("API Key updated successfully!")
            ->success()
            ->send();
    }

    private function updateEnvFile()
    {
        $envContent = File::get(base_path('.env'));
        $envContent = preg_replace(
            '/^SPOONACULAR_KEY=.*$/m',
            'SPOONACULAR_KEY=' . $this->api_key,
            $envContent
        );

        File::put(base_path('.env'), $envContent);
    }

}
