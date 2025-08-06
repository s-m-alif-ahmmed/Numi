<?php

namespace App\Filament\Widgets;

use App\Models\Cuisine;
use App\Models\Dietary;
use App\Models\Goal;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\MealType;
use App\Models\Occasion;
use App\Models\Recipe;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Users',
                // Get the total number of users
                User::where('role', 'User')->count()
            )
                ->description('New Users (' . Carbon::now()->format('F') . '): ' . User::where('role', 'User')->whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            User::where('role', 'User')
                                ->whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Recipes',
                // Get the total number of Recipes
                Recipe::count()
            )
                ->description('New Recipes (' . Carbon::now()->format('F') . '): ' . Recipe::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Recipe::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Ingredients',
                // Get the total number of Ingredients
                Ingredient::count()
            )
                ->description('New Ingredients (' . Carbon::now()->format('F') . '): ' . Ingredient::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Ingredient::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Ingredient Categories',
                // Get the total number of Ingredient Categories
                IngredientCategory::count()
            )
                ->description('New Ingredient Categories (' . Carbon::now()->format('F') . '): ' . IngredientCategory::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            IngredientCategory::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Cuisines',
                // Get the total number of Cuisines
                Cuisine::count()
            )
                ->description('New Cuisines (' . Carbon::now()->format('F') . '): ' . Cuisine::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Cuisine::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Meal Types',
                // Get the total number of Meal Types
                MealType::count()
            )
                ->description('New Meal Types (' . Carbon::now()->format('F') . '): ' . MealType::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            MealType::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Dietaries',
                // Get the total number of Dietaries
                Dietary::count()
            )
                ->description('New Dietaries (' . Carbon::now()->format('F') . '): ' . Dietary::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Dietary::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Occasions',
                // Get the total number of Occasions
                Occasion::count()
            )
                ->description('New Occasions (' . Carbon::now()->format('F') . '): ' . Occasion::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Occasion::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(
                'Total Goals',
                // Get the total number of Goals
                Goal::count()
            )
                ->description('New Goals (' . Carbon::now()->format('F') . '): ' . Goal::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Goal::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
