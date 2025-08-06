<?php

namespace App\Services\Spoonacular;

use App\Helpers\Helper;
use App\Models\Cuisine;
use App\Models\Dietary;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\MealType;
use App\Models\Occasion;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\TempRecipe;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class SpoonacularApiService
{
    private mixed $secretKey;

    private mixed $baseUrl;

    private Client $client;

    public function __construct()
    {
        $this->secretKey = config('services.spoonacular.secret_key');
        $this->baseUrl = config('services.spoonacular.base_url', 'https://api.spoonacular.com');
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    /**
     * Generate the Spoonacular API signature
     *
     * @param  string  $date  ISO formatted date
     * @param  string  $method  HTTP method (GET, POST, etc.)
     * @param  string  $path  API endpoint path including query string
     * @return string Base64 encoded signature
     */
    private function generateSignature(string $date, string $method, string $path): string
    {
        $stringToSign = $date.$method.$path;
        $signature = hash_hmac('sha1', $stringToSign, $this->secretKey, true);

        return base64_encode($signature);
    }

    /**
     * Make a request to the Spoonacular API
     *
     * @param  string  $method  HTTP method (GET, POST, etc.)
     * @param  string  $endpoint  API endpoint without base URL
     * @param  array  $query  Query parameters
     * @param  array  $body  Request body
     * @return array Response data
     *
     * @throws GuzzleException
     */
    public function makeRequest(string $method, string $endpoint, array $query = [], array $body = []): array
    {
        // Build the full path including query parameters
        $path = $endpoint;
        if (! empty($query)) {
            $path .= '?'.http_build_query($query);
        }

        // Generate the current date in the format Spoonacular expects
        $date = Carbon::now()->format('Y-m-d H:i:s');

        // Generate the signature
        $signature = $this->generateSignature($date, $method, $path);

        // Set the headers
        $headers = [
            'X-Spoonacular-Date' => $date,
            'X-Spoonacular-Signature' => $signature,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Make the request
        $options = [
            'headers' => $headers,
            'http_errors' => false, // Don't throw exceptions for HTTP errors
        ];

        if (! empty($body)) {
            $options['json'] = $body;
        }

        $response = $this->client->request($method, $path, $options);
        $statusCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody(), true);

        // Check for API rate limit errors (402 Payment Required)
        if ($statusCode === 402) {
            throw new \Exception('API rate limit exceeded: ' . ($responseBody['message'] ?? 'Your daily points limit has been reached. Please try again tomorrow or upgrade your plan.'));
        }

        // Check for other errors
        if ($statusCode >= 400) {
            throw new \Exception('API error: ' . ($responseBody['message'] ?? 'An error occurred with the Spoonacular API.'));
        }

        return $responseBody;
    }

    /**
     * Search for activities
     *
     * @param  string  $language  Language code (e.g., 'EN')
     * @param  string  $currency  Currency code (e.g., 'USD')
     * @param  array  $filters  Additional search filters
     * @return array Search results
     *
     * @throws GuzzleException
     */
    /**
     * Search for recipes from Spoonacular API
     *
     * @param  array  $filters  Additional search filters
     * @param  string  $apiKey  Spoonacular API key
     * @param  int  $page  Page number
     * @param  int  $number  Number of results per page
     * @param  string  $type  Recipe type
     * @return array Search results
     *
     * @throws GuzzleException
     */
    public function searchActivities(array $filters = [], string $apiKey = null, int $page = 1, int $number = 2, string $type = 'main course'): array
    {
        $apiKey = $apiKey ?? config('services.spoonacular.secret_key', env('SPOONACULAR_API_KEY') );

        // Calculate offset based on page number
        $offset = ($page - 1) * $number;

        // Ensure number is max 100 as per requirements
        $number = min($number, 100);

        // Create a cache key based on the search parameters
        $cacheKey = 'spoonacular_search_' . md5(json_encode([
            'number' => $number,
            'offset' => $offset,
            'type' => $type,
            'filters' => $filters
        ]));

        // Try to get results from cache first (cache for 24 hours)
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        try {
            // Base query parameters
            $query = [
                'number' => $number,
                'offset' => $offset,
                'type' => $type,
                'apiKey' => $apiKey,
                'addRecipeInformation' => 'true',
                'fillIngredients' => 'true',
                'instructionsRequired' => 'true',
            ];

            // Merge filters into query parameters to ensure search parameters are used
            if (!empty($filters)) {
                // Extract specific parameters from filters
                if (isset($filters['query'])) {
                    $query['query'] = $filters['query'];
                }
                if (isset($filters['cuisine'])) {
                    $query['cuisine'] = $filters['cuisine'];
                }
                if (isset($filters['diet'])) {
                    $query['diet'] = $filters['diet'];
                }
                // Add any other parameters that might be in filters
                foreach ($filters as $key => $value) {
                    if (!isset($query[$key]) && !empty($value)) {
                        $query[$key] = $value;
                    }
                }
            }

            $response = $this->makeRequest('GET', '/recipes/complexSearch', $query, []);

            // Filter out recipes that already exist in Recipe or TempRecipe
            if (isset($response['results']) && is_array($response['results'])) {
                $existingRecipeIds = Recipe::pluck('recipe_api_id')->toArray();
                $existingTempRecipeIds = TempRecipe::pluck('recipe_api_id')->toArray();
                $allExistingIds = array_merge($existingRecipeIds, $existingTempRecipeIds);

                $filteredResults = array_filter($response['results'], function($recipe) use ($allExistingIds) {
                    return !in_array($recipe['id'], $allExistingIds);
                });

                $response['results'] = array_values($filteredResults);
                $response['totalResults'] = count($response['results']);
            }

            // Cache the results for 24 hours to reduce API calls
            cache()->put($cacheKey, $response, now()->addHours(24));

            return $response;
        } catch (\Exception $e) {
            // If we hit an API error, return a structured error response
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'results' => [],
                'totalResults' => 0
            ];
        }
    }

    /**
     * Get recipe details by ID
     *
     * @param  string  $id  Recipe ID
     * @return array Recipe details
     * @throws GuzzleException
     */
    public function getActivityByIds(string $id): array
    {
        $apiKey = config('services.spoonacular.secret_key', env('SPOONACULAR_API_KEY') );
        $endpoint = "/recipes/{$id}/information";

        // Create a cache key for this specific recipe
        $cacheKey = "spoonacular_recipe_{$id}";

        // Try to get recipe details from cache first (cache for 24 hours)
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        try {
            $query = [
                'apiKey' => $apiKey,
                'includeNutrition' => 'true',
            ];

            $response = $this->makeRequest('GET', $endpoint, $query);

            // Cache the results for 24 hours
            cache()->put($cacheKey, $response, now()->addHours(24));

            return $response;
        } catch (\Exception $e) {
            // If we hit an API error, return a structured error response
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'id' => $id
            ];
        }
    }

    /**
     * Import recipes from TempRecipe to Recipe
     *
     * @param  array  $recipeIds  Array of TempRecipe IDs to import
     * @return array Result of import operation
     */
    public function importProductData(array $recipeIds): array
    {
        try {
            $importedCount = 0;
            $importedIds = [];

            // Get all temp recipes by ID
            $tempRecipes = TempRecipe::whereIn('recipe_api_id', $recipeIds)->get();

            if ($tempRecipes->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'No recipes found to import',
                ];
            }

            foreach ($tempRecipes as $tempRecipe) {
                // Check if recipe already exists in Recipe table
                $existingRecipe = Recipe::where('recipe_api_id', $tempRecipe->recipe_api_id)->first();

                if (!$existingRecipe) {
                    // Get detailed recipe information from API if needed
                    $recipeDetails = null;
                    if (empty($tempRecipe->instruction) || empty($tempRecipe->preparation_time)) {
                        try {
                            $recipeDetails = $this->getActivityByIds($tempRecipe->recipe_api_id);

                            // Check if the API returned an error
                            if (isset($recipeDetails['error']) && $recipeDetails['error']) {
                                // Log the error but continue with available data
                                \Log::warning("API error when getting recipe details: " . ($recipeDetails['message'] ?? 'Unknown error'));
                            }
                        } catch (\Exception $e) {
                            // Log the error but continue with available data
                            \Log::warning("Exception when getting recipe details: " . $e->getMessage());
                        }
                    }

                    // Create new Recipe
                    $recipe = new Recipe();
                    $recipe->recipe_api_id = $tempRecipe->recipe_api_id;
                    $recipe->title = $tempRecipe->title;
                    $recipe->image_url = $tempRecipe->image_url;
                    $recipe->source_url = $tempRecipe->source_url;
                    $recipe->category = $tempRecipe->category ?? 'Uncategorized';
                    $recipe->preparation_time = $tempRecipe->preparation_time ?? ($recipeDetails['preparationMinutes'] ?? '0');
                    $recipe->cooking_time = $tempRecipe->cooking_time ?? ($recipeDetails['cookingMinutes'] ?? '0');
                    $recipe->total_ready_time = $tempRecipe->total_ready_time ?? ($recipeDetails['readyInMinutes'] ?? '0');
                    $recipe->servings = $tempRecipe->servings ?? ($recipeDetails['servings'] ?? '1');
                    $recipe->description = $tempRecipe->description ?? ($recipeDetails['summary'] ?? '');
                    $recipe->instruction = $tempRecipe->instruction ?? ($recipeDetails['instructions'] ?? '');
                    $recipe->calories = $tempRecipe->calories ?? ($recipeDetails['nutrition']['nutrients'][0]['amount'] ?? '0');
                    $recipe->protein = $tempRecipe->protein ?? ($recipeDetails['nutrition']['nutrients'][1]['amount'] ?? '0');
                    $recipe->fat = $tempRecipe->fat ?? ($recipeDetails['nutrition']['nutrients'][2]['amount'] ?? '0');
                    $recipe->carbs = $tempRecipe->carbs ?? ($recipeDetails['nutrition']['nutrients'][3]['amount'] ?? '0');
                    $recipe->level = 'Easy'; // Default level
                    $recipe->status = 'Active'; // Default status
                    $recipe->save();

                    // Recipe unique Cuisines save
                    $cuisines = $recipeDetails['cuisines'] ?? [];
                    if (!empty($cuisines) && is_array($cuisines)) {
                        foreach ($cuisines as $cuisine) {

                            $name = Str::title($cuisine);
                            $slug = Str::lower($cuisine);

                            if (!Cuisine::where('name', $cuisine)->orWhere('slug', Str::slug($cuisine))->exists()) {
                                Cuisine::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                    'status' => 'Active',
                                ]);
                            }
                        }
                    }

                    // Recipe unique Dish/Meal Types save
                    $dishTypes = $recipeDetails['dishTypes'] ?? [];
                    if (!empty($dishTypes) && is_array($dishTypes)) {
                        foreach ($dishTypes as $dishType) {

                            $name = Str::title($dishType);
                            $slug = Str::lower($dishType);

                            if (!MealType::where('name', $dishType)->orWhere('slug', Str::slug($dishType))->exists()) {
                                MealType::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                    'status' => 'Active',
                                ]);
                            }
                        }
                    }

                    // Recipe unique Dietary/Diet save
                    $diets = $recipeDetails['diets'] ?? [];
                    if (!empty($diets) && is_array($diets)) {
                        foreach ($diets as $diet) {

                            $name = Str::title($diet);
                            $slug = Str::lower($diet);

                            if (!Dietary::where('name', $diet)->orWhere('slug', Str::slug($diet))->exists()) {
                                Dietary::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                    'status' => 'Active',
                                ]);
                            }
                        }
                    }

                    // Recipe unique Occasion save
                    $occasions = $recipeDetails['occasions'] ?? [];
                    if (!empty($occasions) && is_array($occasions)) {
                        foreach ($occasions as $occasion) {

                            $name = Str::title($occasion);
                            $slug = Str::lower($occasion);

                            if (!Occasion::where('name', $occasion)->orWhere('slug', Str::slug($occasion))->exists()) {
                                Occasion::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                    'status' => 'Active',
                                ]);
                            }
                        }
                    }


                    // Import ingredients if available in recipe details
                    if ($recipeDetails && isset($recipeDetails['extendedIngredients']) && is_array($recipeDetails['extendedIngredients'])) {
                        foreach ($recipeDetails['extendedIngredients'] as $ingredient) {
                            // Create or find the ingredient
                            $ingredientData = new Ingredient();
                            $ingredientData->ingredient_api_id = $ingredient['id'] ?? null;
                            $ingredientData->name = $ingredient['name'] ?? null;
                            $ingredientData->aisel = $ingredient['aisle'] ?? null;
                            $ingredientData->consistency = $ingredient['consistency'] ?? null;
                            $ingredientData->original_name = $ingredient['originalName'] ?? null;
                            $ingredientData->meta = json_encode($ingredient['meta'] ?? []);
                            $ingredientData->measures = json_encode($ingredient['measures'] ?? []);
                            $ingredientData->amount = $ingredient['amount'] ?? 0;
                            $ingredientData->unit = $ingredient['unit'] ?? null;
                            $ingredientData->image = $ingredient['image'] ?? null;
                            $ingredientData->save();

                            // Create the recipe-ingredient relationship
                            $recipeIngredient = new RecipeIngredient();
                            $recipeIngredient->recipe_id = $recipe->id;
                            $recipeIngredient->ingredient_id = $ingredientData->id;
                            $recipeIngredient->save();

                            // Ingredient unique Categories save
                            $aisle = $ingredient['aisle'] ?? null;

                            if ($aisle) {
                                // Only insert if not already in DB
                                if (!IngredientCategory::where('name', $aisle)->exists()) {
                                    IngredientCategory::create([
                                        'name' => $aisle,
                                        'status' => 'Active',
                                    ]);
                                }
                            }

                        }
                    }

                    $importedCount++;
                    $importedIds[] = $tempRecipe->recipe_api_id;
                }
            }

            // Delete imported recipes from TempRecipe
            if (!empty($importedIds)) {
                TempRecipe::whereIn('recipe_api_id', $importedIds)->delete();
            }

            return [
                'status' => true,
                'items' => "Successfully imported {$importedCount} recipes",
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        } catch (GuzzleException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Save search results to TempRecipe table
     *
     * @param  array  $searchResults  Search results from Spoonacular API
     * @return array Result of save operation
     */
    public function saveSearchResultsToTempRecipe(array $searchResults): array
    {
        // Check if the search results contain an error
        if (isset($searchResults['error']) && $searchResults['error']) {
            return [
                'status' => false,
                'message' => $searchResults['message'] ?? 'An error occurred while retrieving recipes from the API.',
            ];
        }

        try {
            $savedCount = 0;

            if (isset($searchResults['results']) && is_array($searchResults['results'])) {
                foreach ($searchResults['results'] as $result) {
                    // Check if recipe already exists in Recipe or TempRecipe table
                    $existingRecipe = Recipe::where('recipe_api_id', $result['id'])->exists();
                    $existingTempRecipe = TempRecipe::where('recipe_api_id', $result['id'])->exists();

                    if (!$existingRecipe && !$existingTempRecipe) {
                        $tempRecipe = new TempRecipe();
                        $tempRecipe->recipe_api_id = $result['id'];
                        $tempRecipe->title = $result['title'] ?? null;
                        $tempRecipe->image_url = $result['image'] ?? null;
                        $tempRecipe->source_url = $result['sourceUrl'] ?? null;
                        $tempRecipe->category = $result['dishTypes'][0] ?? 'Uncategorized';
                        $tempRecipe->preparation_time = $result['preparationMinutes'] ?? null;
                        $tempRecipe->cooking_time = $result['cookingMinutes'] ?? null;
                        $tempRecipe->total_ready_time = $result['readyInMinutes'] ?? null;
                        $tempRecipe->servings = $result['servings'] ?? null;
                        $tempRecipe->description = $result['summary'] ?? null;
                        $tempRecipe->instruction = $result['instructions'] ?? null;

                        // Extract nutrition information if available
                        if (isset($result['nutrition']) && isset($result['nutrition']['nutrients'])) {
                            foreach ($result['nutrition']['nutrients'] as $nutrient) {
                                if ($nutrient['name'] === 'Calories') {
                                    $tempRecipe->calories = $nutrient['amount'];
                                } elseif ($nutrient['name'] === 'Protein') {
                                    $tempRecipe->protein = $nutrient['amount'];
                                } elseif ($nutrient['name'] === 'Fat') {
                                    $tempRecipe->fat = $nutrient['amount'];
                                } elseif ($nutrient['name'] === 'Carbohydrates') {
                                    $tempRecipe->carbs = $nutrient['amount'];
                                }
                            }
                        }

                        $tempRecipe->save();
                        $savedCount++;
                    }
                }
            }

            return [
                'status' => true,
                'count' => $savedCount,
                'message' => "Successfully saved {$savedCount} recipes to temporary storage",
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
