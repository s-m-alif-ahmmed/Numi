<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SystemSetting\SystemSettingController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ProfileUpdateController;
use App\Http\Controllers\API\PersonalizeController;
use App\Http\Controllers\API\Recipe\RecipeController;
use App\Http\Controllers\API\Recipe\FavouriteController;
use App\Http\Controllers\API\Recipe\CollectionController;
use App\Http\Controllers\API\Search\SearchQueryController;

// Common routes
Route::get('/system-setting', [SystemSettingController::class, 'systemSetting']);

//Personalize Categories List routes
Route::get('/dietary-list', [PersonalizeController::class, 'dietaryList']);
Route::get('/goal-list', [PersonalizeController::class, 'goalList']);
Route::get('/cuisine-list', [PersonalizeController::class, 'cuisineList']);
Route::get('/time-availability-list', [PersonalizeController::class, 'abilityTimeList']);
Route::get('/meal-type-list', [PersonalizeController::class, 'mealTypeList']);
Route::get('/occasion-list', [PersonalizeController::class, 'occasionList']);
Route::get('/ingredient-category-list', [PersonalizeController::class, 'ingredientCategoryList']);
Route::get('/ingredient-list', [PersonalizeController::class, 'ingredientList']);


Route::middleware(['guest'])->group(function () {

    //  Authentication routes
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('resend_otp', [RegisterController::class, 'resend_otp']);
    Route::post('verify_otp', [RegisterController::class, 'verify_otp']);
    Route::post('forgot-password', [RegisterController::class, 'forgot_password']);
    Route::post('forgot-verify-otp', [RegisterController::class, 'forgot_verify_otp']);
    Route::post('reset-password', [RegisterController::class, 'reset_password']);
});

Route::group(['middleware' => 'auth:sanctum'], function ($router) {

    // common routes
    Route::get('/user-detail', [LoginController::class, 'userDetails']);
    Route::post('/logout', [LoginController::class, 'logout']);

    // Profile Update routes
    Route::post('/profile-info', [ProfileUpdateController::class, 'profileInfo']);
    Route::post('/update-profile', [ProfileUpdateController::class, 'updateDetails']);

    // Route::post('/change-password', [ProfileUpdateController::class, 'changePassword']);
    // Route::post('/change-email', [ProfileUpdateController::class, 'changeEmail']);
    // Route::post('/profile-url', [ProfileUpdateController::class, 'profileUrl']);
    // Route::post('/profile-private', [ProfileUpdateController::class, 'profilePrivate']);

    Route::post('/profile-avatar-upload', [ProfileUpdateController::class, 'profileAvatarUpload']);
    Route::post('/profile-avatar-remove', [ProfileUpdateController::class, 'profileAvatarRemove']);
    Route::post('/profile-follow', [ProfileUpdateController::class, 'profileFollow']);
    Route::post('/profile-unfollow', [ProfileUpdateController::class, 'profileUnfollow']);

    // Route::get('/profile-overview', [ProfileUpdateController::class, 'sellerProfileOverview']);
    // Route::post('/profile-seller-stripe-store', [ProfileUpdateController::class, 'sellerStripePaymentUpdate']);

    // User Recipe routes
    Route::post('/make-new-recipe', [RecipeController::class, 'storeRecipe']);
    Route::post('/make-new-recipe', [RecipeController::class, 'storeRecipe']);
    Route::post('/recipe-like', [RecipeController::class, 'newLike']);
    Route::post('/recipe-like-remove', [RecipeController::class, 'likeRemove']);
    Route::post('/recipe-comment', [RecipeController::class, 'newComment']);
    Route::post('/recipe-rating', [RecipeController::class, 'newRating']);
    Route::get('/recipe-list', [RecipeController::class, 'recipeList']);
    Route::get('/cuisine-recipe-list', [RecipeController::class, 'cuisineRecipeList']);
    Route::get('/cuisine-recipes/{id}', [RecipeController::class, 'cuisineRecipes']);
    Route::get('/recipe/show/{id}', [RecipeController::class, 'recipeDetail']);

    // Recipe Favourite routes
    Route::post('/recipe-favourite', [FavouriteController::class, 'favouriteStore']);
    Route::post('/recipe-favourite-remove/{id}', [FavouriteController::class, 'favouriteRemove']);

    // Collection routes
    Route::get('/collection/list', [CollectionController::class, 'index']);
    Route::post('/collection/store', [CollectionController::class, 'store']);
    Route::post('/collection/update/{id}', [CollectionController::class, 'update']);
    Route::get('/collection/show/{id}', [CollectionController::class, 'show']);
    Route::post('/collection/delete/{id}', [CollectionController::class, 'destroy']);
    Route::post('/collection/recipe-store/{id}', [CollectionController::class, 'recipeStore']);
    Route::post('/collection/recipe-remove/{id}', [CollectionController::class, 'recipeRemove']);


    // Recipes routes
    Route::get('/recipe-list', [RecipeController::class, 'recipeList']);

    // Recipes routes
    Route::post('/search', [SearchQueryController::class, 'search']);
    Route::get('/recent-search', [SearchQueryController::class, 'recentSearch']);
    Route::post('/recent-search/remove/{id}', [SearchQueryController::class, 'recentSearchRemove']);


});
