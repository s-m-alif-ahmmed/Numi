<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use App\Models\Dietary;
use App\Models\Goal;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\MealType;
use App\Models\Occasion;
use App\Models\Region;
use App\Models\TimeAvailability;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PersonalizeController extends Controller
{
    use ApiResponse;

    public function dietaryList()
    {
        $data = Dietary::orderBy('id', 'asc')
            ->where('status', 'Active')->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function goalList()
    {
        $data = Goal::orderBy('id', 'asc')
            ->where('status', 'Active')->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function cuisineList()
    {
        $data = Cuisine::orderByRaw("CASE WHEN name = 'Global' THEN 0 ELSE 1 END")
            ->orderBy('name', 'asc')
            ->where('status', 'Active')
            ->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function abilityTimeList()
    {
        $data = TimeAvailability::orderBy('id', 'desc')
            ->where('status', 'Active')
            ->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function mealTypeList()
    {
        $data = MealType::orderBy('id', 'asc')
            ->where('status', 'Active')
            ->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function occasionList()
    {
        $data = Occasion::orderBy('id', 'asc')
            ->where('status', 'Active')
            ->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function ingredientCategoryList(Request $request)
    {
        $search = $request->input('search');

        $data = IngredientCategory::orderBy('id', 'asc')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->where('status', 'Active')
            ->get();

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }

    public function ingredientList(Request $request)
    {
        $limit = $request->limit ?? 10;
        $search = $request->input('search');

        $data = Ingredient::orderBy('id', 'asc')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('aisel', 'like', '%' . $search . '%')
                ->orWhere('original_name', 'like', '%' . $search . '%');
            })
            ->where('status', 'Active')
            ->paginate($limit);

        return $this->success('Data Retrieve Successfully!',$data, 200);
    }
}
