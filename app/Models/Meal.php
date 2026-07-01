<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $emoji
 * @property string $category
 * @property int $time_minutes
 * @property string $difficulty
 * @property string $price
 * @property string $color_start
 * @property string $color_end
 * @property string $accent_color
 * @property string $description
 * @property array<int, array{name: string, amount: float, unit: string}> $ingredients
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'emoji',
    'category',
    'time_minutes',
    'difficulty',
    'price',
    'color_start',
    'color_end',
    'accent_color',
    'description',
    'ingredients',
])]
class Meal extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'time_minutes' => 'integer',
            'price' => 'decimal:2',
            'ingredients' => 'array',
        ];
    }
}
