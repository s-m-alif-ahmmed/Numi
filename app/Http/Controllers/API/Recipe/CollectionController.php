<?php

namespace App\Http\Controllers\API\Recipe;

use ALifAhmmed\HelperPackage\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionRecipe;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = Auth::user();

        $collections = Collection::where('user_id', $user->id)->get();

        return $this->success('Data Retrieve Successfully!',$collections, 200);
    }

    public function show($id)
    {
        $user = Auth()->user();

        $collection = Collection::where('user_id', $user->id)->find($id);

        if (!$collection) {
            return $this->error('Collection not found', 404);
        }

        $collection->load('collection_recipes.recipe');

        return $this->success('Data Retrieve Successfully!',$collection, 200);
    }

    public function store(Request $request)
    {
        $Validate = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($Validate->fails()) {
            return $this->error('Validation failed', 422, $Validate->errors());
        }

        $user = Auth()->user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->image;
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/collections/'), $imageName);
            $imagePath = "uploads/collections/" . $imageName;
        }

        $collection = Collection::create([
            'user_id'  => $user->id,
            'name'     => $request->name,
            'image'    => $imagePath,
        ]);

        return $this->success('Collection created successfully', $collection, 200);
    }

    public function update(Request $request, $id)
    {
        $Validate = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($Validate->fails()) {
            return $this->error('Validation failed', 422, $Validate->errors());
        }

        $user = Auth()->user();

        $collection = Collection::where('user_id', $user->id)->find($id);

        if (!$collection) {
            return $this->error('Collection not found', 404);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {

            if ($collection->image)
            {
                // Remove the base URL
                $relativePath = Str::replaceFirst('https://sasachahop.test/', '', $collection->image);
                Helper::fileDelete($relativePath);
            }

            $image = $request->image;
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/collections/'), $imageName);
            $imagePath = "uploads/collections/" . $imageName;
        }

        $collection->name = $request->name ?? $collection->name;
        $collection->image = $imagePath ?? $collection->image;
        $collection->save();

        return $this->success('Recipe created successfully', $collection, 200);
    }

    public function destroy($id)
    {
        $user = Auth()->user();

        $collection = Collection::where('user_id', $user->id)->find($id);

        if (!$collection) {
            return $this->error('Collection not found', 404);
        }

        if ($collection->image)
        {
            // Remove the base URL
            $relativePath = Str::replaceFirst('https://sasachahop.test/', '', $collection->image);
            Helper::fileDelete($relativePath);
        }

        $collection->delete();

        return $this->success('Collection deleted successfully', [], 200);
    }

    public function recipeStore(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'collection_ids' => 'required|array',
            'collection_ids.*' => 'integer|exists:collections,id',
        ]);

        if ($validate->fails()) {
            return $this->error('Validation failed', 422, $validate->errors());
        }

        $user = auth()->user();

        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('Recipe not found', 404);
        }

        $collectionIds = $request->input('collection_ids');
        $alreadyAdded = [];
        $added = [];

        foreach ($collectionIds as $collectionId) {
            $collection = Collection::where('id', $collectionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$collection) {
                continue;
            }

            $exists = CollectionRecipe::where('collection_id', $collectionId)
                ->where('recipe_id', $recipe->id)
                ->exists();

            if ($exists) {
                $alreadyAdded[] = $collectionId;
                continue;
            }

            CollectionRecipe::create([
                'collection_id' => $collectionId,
                'recipe_id' => $recipe->id,
            ]);

            $added[] = $collectionId;
        }

        return $this->success('Recipe store in collection successfully', [
            'added_to_collections' => $added,
            'already_in_collections' => $alreadyAdded,
        ], 200);
    }

    public function recipeRemove(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'recipe_id' => 'required|exists:recipes,id',
        ]);

        if ($validate->fails()) {
            return $this->error('Validation failed', 422, $validate->errors());
        }

        $user = auth()->user();

        $collection = Collection::where('user_id', $user->id)->find($id);

        if (!$collection)
        {
            return $this->error('Collection not found', 404);
        }

        $recipe = Recipe::find($request->recipe_id);

        if (!$recipe)
        {
            return $this->error('Recipe not found', 404);
        }

        $collection_recipe = CollectionRecipe::where('collection_id', $collection->id)->where('recipe_id', $recipe->id)->first();

        if (!$collection_recipe)
        {
            return $this->error('Recipe not found in collection.', 500);
        }

        $collection_recipe->delete();

        return $this->success('Recipe removed from collection successfully', [], 200);

    }

}
