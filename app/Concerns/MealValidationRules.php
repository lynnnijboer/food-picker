<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait MealValidationRules
{
    /**
     * Get the validation rules used to validate a meal recipe.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function mealRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'emoji' => ['required', 'string', 'max:16'],
            'category' => ['required', 'string', 'max:255'],
            'time_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'difficulty' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'base_servings' => ['required', 'integer', 'min:1', 'max:12'],
            'color_start' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_end' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['required', 'string', 'max:2000'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.amount' => ['required', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:32'],
        ];
    }
}
