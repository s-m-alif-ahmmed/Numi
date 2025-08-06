<?php

namespace App\Http\Controllers\API\Search;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use App\Models\Recipe;
use App\Models\SearchQuery;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SearchQueryController extends Controller
{
    use ApiResponse;

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
            'preparation_min_time' => 'nullable|integer',
            'preparation_max_time' => 'nullable|integer',
            'category_cuisine_id' => 'nullable|exists:cuisines,id',
            'difficulty_level' => 'nullable|in:Easy,Medium,Hard',
        ]);

        $search = $request->input('search');
        $preparation_min_time = $request->input('preparation_min_time');
        $preparation_max_time = $request->input('preparation_max_time');
        $category_cuisine_id = $request->input('category_cuisine_id');
        $difficulty_level = $request->input('difficulty_level');

        // Search for cuisines by name
        $cuisines = Cuisine::where('status', 'Active')
            ->where('name', 'like', '%' . $search . '%')
            ->get();

        // Build query for recipes
        $recipesQuery = Recipe::where('status', 'Active');

        if (!empty($search)) {
            $recipesQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by preparation time
        if (!is_null($preparation_min_time) && !is_null($preparation_max_time)) {
            $recipesQuery->whereBetween('preparation_time', [$preparation_min_time, $preparation_max_time]);
        }

        // Filter by cuisine/category
        $cuisineName = null;
        if (!empty($category_cuisine_id)) {
            $cuisine = Cuisine::where('status', 'Active')->find($category_cuisine_id);
            if ($cuisine) {
                $recipesQuery->where('category', $cuisine->name);
                $cuisineName = $cuisine->name;
            }
        }

        // Filter by difficulty level
        if (!empty($difficulty_level)) {
            $recipesQuery->where('level', $difficulty_level);
        }

        $recipes = $recipesQuery->get();

        // Search users
        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('url', 'like', '%' . $search . '%');
        })->select('id', 'name', 'url', 'avatar')->get();

        // Save the search query
        SearchQuery::create([
            'user_id' => auth()->id(),
            'search_query' => $search,
            'preparation_min_time' => $preparation_min_time,
            'preparation_max_time' => $preparation_max_time,
            'category_cuisine' => $cuisineName,
            'difficulty_level' => $difficulty_level,
        ]);

        // Response
        return $this->success('Data Retrieved Successfully!', [
            'cuisines' => $cuisines,
            'recipes' => $recipes,
            'users' => $users,
        ], 200);
    }

    public function recentSearch()
    {
        $user = auth()->user();

        $recentSearch = SearchQuery::where('user_id', $user->id)
            ->latest()
            ->select('id', 'search_query')
            ->get();

        return $this->success('Data Retrieved Successfully!', $recentSearch, 200);

    }

    public function recentSearchRemove($id)
    {
        $user = auth()->user();

        $recentSearch = SearchQuery::where('user_id', $user->id)->find($id);

        if (!$recentSearch) {
            return $this->error('Recent Search not found', 404);
        }

        $recentSearch->delete();

        return $this->success('Data Retrieved Successfully!', [], 200);

    }


}
