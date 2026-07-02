<?php

namespace App\Http\Resources;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Meal
 */
class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'emoji' => $this->emoji,
            'category' => $this->category,
            'timeMinutes' => $this->time_minutes,
            'difficulty' => $this->difficulty,
            'price' => (float) $this->price,
            'baseServings' => $this->base_servings,
            'colorStart' => $this->color_start,
            'colorEnd' => $this->color_end,
            'accentColor' => $this->accent_color,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
        ];
    }
}
