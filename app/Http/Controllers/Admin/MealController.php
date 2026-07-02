<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMealRequest;
use App\Http\Requests\Admin\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MealController extends Controller
{
    /**
     * Show all recipes in the catalogue.
     */
    public function index(): Response
    {
        $meals = Meal::query()->orderBy('name')->get();

        return Inertia::render('admin/meals/Index', [
            'meals' => MealResource::collection($meals),
        ]);
    }

    /**
     * Show the form for adding a new recipe.
     */
    public function create(): Response
    {
        return Inertia::render('admin/meals/Create');
    }

    /**
     * Store a newly added recipe.
     */
    public function store(StoreMealRequest $request): RedirectResponse
    {
        Meal::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Recipe added.')]);

        return to_route('admin.meals.index');
    }

    /**
     * Show the form for editing a recipe.
     */
    public function edit(Meal $meal): Response
    {
        return Inertia::render('admin/meals/Edit', [
            'meal' => new MealResource($meal),
        ]);
    }

    /**
     * Update an existing recipe.
     */
    public function update(UpdateMealRequest $request, Meal $meal): RedirectResponse
    {
        $meal->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Recipe updated.')]);

        return to_route('admin.meals.index');
    }

    /**
     * Remove a recipe from the catalogue.
     */
    public function destroy(Meal $meal): RedirectResponse
    {
        $meal->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Recipe deleted.')]);

        return to_route('admin.meals.index');
    }
}
