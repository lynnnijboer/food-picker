<?php

namespace App\Http\Controllers;

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
        $meals = Meal::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Meal $meal) => [
                'id' => $meal->id,
                'name' => $meal->name,
                'emoji' => $meal->emoji,
                'category' => $meal->category,
                'timeMinutes' => $meal->time_minutes,
                'difficulty' => $meal->difficulty,
                'price' => (float) $meal->price,
                'colorStart' => $meal->color_start,
                'colorEnd' => $meal->color_end,
                'accentColor' => $meal->accent_color,
                'description' => $meal->description,
                'ingredients' => $meal->ingredients,
            ]);

        return Inertia::render('WeekMenu', [
            'meals' => $meals,
        ]);
    }
}
