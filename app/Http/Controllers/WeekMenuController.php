<?php

namespace App\Http\Controllers;

use App\Http\Resources\MealResource;
use App\Models\Meal;
use Inertia\Inertia;
use Inertia\Response;

class WeekMenuController extends Controller
{
    /**
     * Show the week menu meal picker homepage.
     */
    public function index(): Response
    {
        $meals = Meal::query()->orderBy('id')->get();

        return Inertia::render('WeekMenu', [
            'meals' => MealResource::collection($meals),
        ]);
    }
}
