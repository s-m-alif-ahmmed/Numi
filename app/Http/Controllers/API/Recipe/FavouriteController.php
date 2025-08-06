<?php

namespace App\Http\Controllers\API\Recipe;

use App\Http\Controllers\Controller;
use App\Models\FavouriteRecipe;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    use ApiResponse;

    public function favouriteStore(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'recipe_id'    => "required|exists:recipes,id",
        ]);

        if ($validated->fails()) {
            return $this->error('Validation failed', 422);
        }

        $user = Auth()->user();
        $recipeId = $request->recipe_id;

        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return $this->error('Recipe not found', 404);
        }

        $existingFavourite = FavouriteRecipe::where('recipe_id', $recipeId)->where('user_id', $user->id)->first();

        if ($existingFavourite) {
            return $this->error('You already favourite this recipe', 404);
        }

        $favourite = FavouriteRecipe::create([
            'user_id'       => $user->id,
            'recipe_id'    => $request->recipe_id,
        ]);

        if ($favourite) {
            return $this->success('Favourite added successfully', $favourite, 200);
        } else {
            return $this->error('Failed to add favourite', 500);
        }
    }

    public function favouriteRemove($id)
    {
        $user = Auth()->user();
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return $this->error('Recipe not found', 404);
        }

        $existingFavourite = FavouriteRecipe::where('recipe_id', $id)->where('user_id', $user->id)->first();

        if (!$existingFavourite) {
            return $this->error('Recipe not found in favourite.', 500);
        }

        $existingFavourite->delete();

        return $this->success('Favourite added successfully', $recipe, 200);

    }
}
