<?php

namespace App\Http\Controllers\API\Recipe;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use App\Models\RecipeComment;
use App\Models\RecipeRating;
use App\Models\RecipeTag;
use App\Models\Instruction;
use App\Models\Like;
use App\Models\RecipeIngredient;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    use ApiResponse;

    public function storeRecipe(Request $request)
    {
        $Validate = Validator::make($request->all(), [
            'title'             => 'required|string|max:255',
            'image'             => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'category_id'       => 'nullable|integer',
            'pre_time'          => 'nullable|string',
            'cook_time'         => 'nullable|string',
            'total_time'        => 'nullable|string',
            'servings'          => 'nullable|string',
            'description'       => 'nullable|string',
            'level'             => 'required|in:Easy,Medium,Hard',
            'calories'          => 'nullable|string',
            'protein'           => 'nullable|string',
            'fat'               => 'nullable|string',
            'carbs'             => 'nullable|string',
            'status'            => 'nullable|string|in:Draft',

            'instructions'      => 'required|array',
            'instructions.*.instruction'   => 'required|string',
            'instructions.*.cooking_time'      => 'nullable|string',
            'instructions.*.image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',

            'ingredients'                   => 'required|array',
            'ingredients.*.ingredient_id'   => 'required|integer',

            'tags'                   => 'nullable|array',
            'tags.*'   => 'nullable|string|max:255',
        ]);

        if ($Validate->fails()) {
            return response()->json(['error' => $Validate->errors(), 'status' => '422']);
        }

        DB::beginTransaction();

        $user = Auth()->user();

        $categoryId = $request->category_id;

        $cuisine = Cuisine::find($categoryId);

        if ($request->hasFile('image')) {
            $image = $request->image;
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/recipes/'), $imageName);
            $imagePath = "uploads/recipes/" . $imageName;
        }

        $recipe = Recipe::create([
            'user_id'           => $user->id,
            'title'             => $request->title,
            'image'             => $imagePath ?? null,
            'category'          => $cuisine->slug ?? null,
            'preparation_time'  => $request->pre_time,
            'cooking_time'      => $request->cook_time,
            'total_ready_time'  => $request->total_time,
            'servings'          => $request->servings,
            'description'       => $request->description,
            'level'             => $request->level,
            'calories'          => $request->calories,
            'protein'           => $request->protein,
            'fat'               => $request->fat,
            'carbs'             => $request->carbs,
            'status'            => $request->status ?? 'Active',
        ]);


        foreach ($request->ingredients as $ingradient) {
            RecipeIngredient::create([
                'recipe_id'   => $recipe->id,
                'ingredient_id' => $ingradient['ingredient_id'],
            ]);
        }

        foreach ($request->tags as $tag) {
            RecipeTag::create([
                'recipe_id'   => $recipe->id,
                'name' => $tag,
            ]);
        }

        foreach ($request->instructions as $instruction) {

            $imgPath = null;

            if (!empty($instruction['image'])) {
                $rand = rand(1000, 9999);
                $image = $instruction['image'];
                $imageName = time() . $rand . "." . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/instructions/'), $imageName);
                $imgPath = "uploads/instructions/" . $imageName;
            }

            Instruction::create([
                "recipes_id"    => $recipe->id,
                "instruction"   => $instruction['instruction'],
                "cooking_time"  => $instruction['cooking_time'],
                "image"         => $imgPath,
            ]);
        }

        DB::commit();

        return $this->success('Recipe created successfully', $recipe, 200);
    }

    public function newLike(Request $request)
    {

        $Validated = Validator::make($request->all(), [
            'recipe_id' => "required"
        ]);

        if ($Validated->fails()) {
            return response()->json(['error' => $Validated->errors(), 'status' => '422']);
        }

        $user = Auth()->user();

        $existingLike = Like::where('user_id', $user->id)->where('recipe_id', $request->recipe_id)->first();

        if ($existingLike)
        {
            return $this->ok('You have already liked this recipe', $existingLike, 200);
        }

        $data = Like::create([
            'user_id'       => $user->id,
            'recipe_id'    => $request->recipe_id,
        ]);

        if ($data) {
            return response()->json([
                'status' => 201,
                'message' => "You like this Successfully",
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => "Failed to like the recipe",
            ]);
        }
    }

    public function likeRemove(Request $request)
    {

        $Validated = Validator::make($request->all(), [
            'recipe_id' => "required"
        ]);

        if ($Validated->fails()) {
            return response()->json(['error' => $Validated->errors(), 'status' => '422']);
        }

        $user = Auth()->user();

        $existingLike = Like::where('user_id', $user->id)->where('recipe_id', $request->recipe_id)->first();

        if ($existingLike)
        {
            $existingLike->delete();

            return $this->ok('You successfully removed the like from this recipe', $existingLike, 200);
        }

        return $this->error('You have not liked this recipe', 404);

    }

    public function newComment(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'recipe_id'    => "required|exists:recipes,id",
            'comment'       => "required",
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

        $comment = RecipeComment::create([
            'user_id'       => $user->id,
            'recipe_id'    => $request->recipe_id,
            'comment'       => $request->comment
        ]);

        if ($comment) {
            return $this->success('Comment added successfully', $comment, 200);
        } else {
            return $this->error('Failed to add comment', 500);
        }
    }

    public function newRating(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'recipe_id'    => "required|exists:recipes,id",
            'effort_rating'        => "nullable",
            'taste_rating'        => "nullable",
            'time_rating'        => "nullable",
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

        $existingRatings = RecipeRating::where('recipe_id', $recipeId)->where('user_id', $user->id)->first();

        if ($existingRatings) {
            return $this->error('You have already rated this recipe', 404);
        }

        $rating = RecipeRating::create([
            'user_id'       => $user->id,
            'recipe_id'    => $request->recipe_id,
            'effort_rating'        => $request->effort_rating,
            'taste_rating'        => $request->taste_rating,
            'time_rating'        => $request->time_rating,
        ]);

        if ($rating) {
            return $this->success('Rating added successfully', $rating, 200);
        } else {
            return $this->error('Failed to add rating', 500);
        }
    }

    //recipe list
    public function recipeList(Request $request)
    {
        $cuisine = $request->input('cuisine');
        $search = $request->input('search');
        $latest = $request->input('latest');
        $random = $request->input('random');
        $favourite = $request->input('favourite');
        $draft = $request->input('draft');
        $published = $request->input('published');
        $import = $request->input('import');
        $following = $request->input('following');
        $perPage = $request->input('per_page') ?? 10;

        $user = auth()->user();

        $recipes = Recipe::with(['ratings', 'comments', 'user'])->select('id', 'image', 'image_url', 'title', 'description', 'calories', 'servings', 'created_at', 'total_ready_time', 'user_id');

        // Filter by cuisine
        if ($cuisine) {
            $recipes->where('category', $cuisine);
        }

        // Filter favourites of the logged-in user
        if ($favourite && $user) {
            $recipes->whereHas('favourites', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        // Search by title or description
        if ($search) {
            $recipes->where(function($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by draft status
        if ($draft) {
            $recipes->where('user_id', $user->id)->where('status', 'Draft')->whereNull('source_url');
        }

        // Filter by published status
        if ($published) {
            $recipes->where('user_id', $user->id)->where('status', 'Active')->whereNull('source_url');
        }

        // Filter by import status
        if ($import) {
            $recipes->where('status', 'Active')->where('user_id', $user->id)->whereNotNull('source_url');
        }

        // Sort by latest
        if ($latest) {
            $recipes->latest();
        }

        // Get random recipes (limit can be optional or added by request)
        if ($random) {
            $recipes->inRandomOrder();
        }

        if ($following)
        {
            $recipes->whereHas('user.followers', function ($query) use ($user) {
               $query->where('user_id', $user->id);
            });
        }

        // Fetch results (you may use pagination if needed)
        $recipes = $recipes->paginate($perPage);

        return $this->success('Recipes fetched successfully', $recipes, 200);
    }

    public function cuisineRecipeList(Request $request)
    {
        // Fetch all cuisines
        $cuisines = Cuisine::all();

        // Loop through each cuisine and only include it if it has at least one matching recipe
        $data = $cuisines->map(function ($cuisine) {
            $recipes = Recipe::with(['ratings', 'comments', 'likes', 'user'])
                ->where('category', $cuisine->slug)
                ->get()
                ->map(function ($recipe) {
                    return [
                        'id'             => $recipe->id,
                        'image'          => $recipe->image ?? null,
                        'image_url'      => $recipe->image_url,
                        'title'          => $recipe->title,
                        'total_ready_time'    => $recipe->total_ready_time,
                        'average_rating'    => $recipe->average_rating,
                        'rating_count'    => $recipe->rating_count,
                        'is_favourite'    => $recipe->is_favourite,
                    ];
                });

            if ($recipes->isEmpty()) {
                return null; // skip this cuisine
            }

            return [
                'id'      => $cuisine->id,
                'name'    => $cuisine->name,
                'slug'    => $cuisine->slug,
                'recipes' => $recipes,
            ];
        })->filter()->values();

        return $this->success('Recipes fetched successfully', $data, 200);
    }

    public function cuisineRecipes(Request $request, $id)
    {
        // Fetch all cuisines
        $cuisine = Cuisine::find($id);

        $recipes = Recipe::with(['ratings', 'comments', 'likes', 'user'])
            ->where('category', $cuisine->slug)
            ->get()
            ->map(function ($recipe) {
                return [
                    'id'             => $recipe->id,
                    'image'          => $recipe->image ?? null,
                    'image_url'      => $recipe->image_url,
                    'title'          => $recipe->title,
                    'total_ready_time'    => $recipe->total_ready_time,
                    'average_rating'    => $recipe->average_rating,
                    'rating_count'    => $recipe->rating_count,
                    'is_favourite'    => $recipe->is_favourite,
                ];
            });

        if ($recipes->isEmpty()) {
            return null; // skip this cuisine
        }

        $data = [
            'id'      => $cuisine->id,
            'name'    => $cuisine->name,
            'slug'    => $cuisine->slug,
            'recipes' => $recipes,
        ];

        return $this->success('Recipes fetched successfully', $data, 200);
    }

    public function recipeDetail($id)
    {
        $recipe = Recipe::with([
            'ratings',
            'comments',
            'comments.user:id,name,avatar',
            'user:id,name,avatar',
            'tags',
            'recipeIngredients.ingredient',
            'instructions',
        ])->find($id);

        if (!$recipe) {
            return $this->error('Recipe not found', 404);
        }

        return $this->success('Recipe fetched successfully', $recipe, 200);
    }

}
