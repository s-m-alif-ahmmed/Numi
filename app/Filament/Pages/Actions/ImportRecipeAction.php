<?php

namespace App\Filament\Pages\Actions;

use App\Models\TempRecipe;
use App\Services\Spoonacular\SpoonacularApiService;
use Filament\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class ImportRecipeAction extends Action
{
    protected mixed $afterCallback = null;

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Search')
            ->icon('heroicon-o-magnifying-glass')
            ->modalHeading('Search Spoonacular Recipes')
            ->modalDescription('Search and import recipes from Spoonacular API')
            ->modalSubmitActionLabel('Search')
            ->modalWidth('6xl')
            ->form([
                Section::make('Search Parameters')
                    ->description('Configure your recipe search')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('page')
                                    ->label('Page')
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->minValue(1),

                                TextInput::make('pageSize')
                                    ->label('Results Per Page')
                                    ->required()
                                    ->default(10)
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100),
                            ]),

                        TextInput::make('query')
                            ->label('Search Query')
                            ->placeholder('Enter keywords (e.g., pasta, vegetarian, dessert)'),

                        Select::make('cuisine')
                            ->label('Cuisine Type')
                            ->options([
                                'African' => 'African',
                                'Asian' => 'Asian',
                                'American' => 'American',
                                'British' => 'British',
                                'Cajun' => 'Cajun',
                                'Caribbean' => 'Caribbean',
                                'Chinese' => 'Chinese',
                                'Eastern European' => 'Eastern European',
                                'European' => 'European',
                                'French' => 'French',
                                'German' => 'German',
                                'Greek' => 'Greek',
                                'Indian' => 'Indian',
                                'Irish' => 'Irish',
                                'Italian' => 'Italian',
                                'Japanese' => 'Japanese',
                                'Jewish' => 'Jewish',
                                'Korean' => 'Korean',
                                'Latin American' => 'Latin American',
                                'Mediterranean' => 'Mediterranean',
                                'Mexican' => 'Mexican',
                                'Middle Eastern' => 'Middle Eastern',
                                'Nordic' => 'Nordic',
                                'Southern' => 'Southern',
                                'Spanish' => 'Spanish',
                                'Thai' => 'Thai',
                                'Vietnamese' => 'Vietnamese',
                            ]),

                        Select::make('diet')
                            ->label('Diet')
                            ->options([
                                'Gluten Free' => 'Gluten Free',
                                'Ketogenic' => 'Ketogenic',
                                'Vegetarian' => 'Vegetarian',
                                'Lacto-Vegetarian' => 'Lacto-Vegetarian',
                                'Ovo-Vegetarian' => 'Ovo-Vegetarian',
                                'Vegan' => 'Vegan',
                                'Pescetarian' => 'Pescetarian',
                                'Paleo' => 'Paleo',
                                'Primal' => 'Primal',
                                'Low FODMAP' => 'Low FODMAP',
                                'Whole30' => 'Whole30',
                            ]),

                        Select::make('type')
                            ->label('Meal Type')
                            ->options([
                                'main course' => 'Main Course',
                                'side dish' => 'Side Dish',
                                'dessert' => 'Dessert',
                                'appetizer' => 'Appetizer',
                                'salad' => 'Salad',
                                'bread' => 'Bread',
                                'breakfast' => 'Breakfast',
                                'soup' => 'Soup',
                                'beverage' => 'Beverage',
                                'sauce' => 'Sauce',
                                'marinade' => 'Marinade',
                                'fingerfood' => 'Fingerfood',
                                'snack' => 'Snack',
                                'drink' => 'Drink',
                            ])
                            ->default('main course'),
                    ]),
            ])
            ->action(function (array $data, SpoonacularApiService $apiService): void {
                try {
                    $filters = $this->prepareSpoonacularRequestData($data);

                    $results = $apiService->searchActivities(
                        filters: $filters,
                        apiKey: config('services.spoonacular.secret_key'),
                        page: isset($data['page']) ? (int) $data['page'] : 1,
                        number: isset($data['pageSize']) ? (int) $data['pageSize'] : 10,
                        type: $data['type'] ?? 'main course'
                    );

                    // Check if there was an API error
                    if (isset($results['error']) && $results['error']) {
                        Notification::make()
                            ->danger()
                            ->title('API Error')
                            ->body($results['message'] ?? 'An error occurred while searching recipes.')
                            ->send();
                        return;
                    }

                    if (empty($results) || empty($results['results'])) {
                        Notification::make()
                            ->warning()
                            ->title('No recipes found')
                            ->body('Try different search criteria or check your API key.')
                            ->send();

                        return;
                    }

                    // Save search results to TempRecipe
                    $saveResult = $apiService->saveSearchResultsToTempRecipe($results);

                    if ($saveResult['status']) {
                        Notification::make()
                            ->success()
                            ->title('Recipes Found')
                            ->body($saveResult['message'])
                            ->send();
                    } else {
                        Notification::make()
                            ->warning()
                            ->title('Warning')
                            ->body($saveResult['message'])
                            ->send();
                    }

                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Error')
                        ->body('Failed to search Spoonacular recipes: '.$e->getMessage())
                        ->send();
                }
            });
    }

    /**
     * Prepare request data for the Spoonacular API search endpoint.
     * Transforms form data into the format expected by the API.
     *
     * @param  array  $data  The form data
     * @return array Formatted data for the API request
     */
    protected function prepareSpoonacularRequestData(array $data): array
    {
        $requestData = [];

        if (!empty($data['query'])) {
            $requestData['query'] = $data['query'];
        }

        if (!empty($data['cuisine'])) {
            $requestData['cuisine'] = $data['cuisine'];
        }

        if (!empty($data['diet'])) {
            $requestData['diet'] = $data['diet'];
        }

        if (!empty($data['type'])) {
            $requestData['type'] = $data['type'];
        }

        return $requestData;
    }

}
