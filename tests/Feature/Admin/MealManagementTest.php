<?php

namespace Tests\Feature\Admin;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('admin.meals.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_the_recipe_list(): void
    {
        $user = User::factory()->create();
        Meal::factory()->create(['name' => 'Testrecept']);

        $response = $this->actingAs($user)->get(route('admin.meals.index'));

        $response->assertOk();
    }

    public function test_a_recipe_can_be_created_with_ingredients(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.meals.store'), [
            'name' => 'Familielasagne',
            'emoji' => '🍝',
            'category' => 'Comfort',
            'time_minutes' => 60,
            'difficulty' => 'Gemiddeld',
            'price' => 14.4,
            'base_servings' => 4,
            'color_start' => '#FBDCC8',
            'color_end' => '#F5B79A',
            'accent_color' => '#B24A26',
            'description' => 'Grote ovenschotel voor het hele gezin.',
            'ingredients' => [
                ['name' => 'lasagnebladen', 'amount' => 250, 'unit' => 'g'],
                ['name' => 'half-om-half gehakt', 'amount' => 600, 'unit' => 'g'],
            ],
        ]);

        $response->assertRedirect(route('admin.meals.index'));
        $this->assertDatabaseHas('meals', [
            'name' => 'Familielasagne',
            'base_servings' => 4,
        ]);

        $meal = Meal::where('name', 'Familielasagne')->firstOrFail();
        $this->assertCount(2, $meal->ingredients);
    }

    public function test_a_recipe_requires_at_least_one_ingredient(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.meals.store'), [
            'name' => 'Onvolledig recept',
            'emoji' => '🍽️',
            'category' => 'Comfort',
            'time_minutes' => 30,
            'difficulty' => 'Makkelijk',
            'price' => 5,
            'base_servings' => 2,
            'color_start' => '#FFFFFF',
            'color_end' => '#FFFFFF',
            'accent_color' => '#FFFFFF',
            'description' => 'Test.',
            'ingredients' => [],
        ]);

        $response->assertSessionHasErrors('ingredients');
    }

    public function test_a_recipe_can_be_updated(): void
    {
        $user = User::factory()->create();
        $meal = Meal::factory()->create(['base_servings' => 2]);

        $response = $this->actingAs($user)->put(route('admin.meals.update', $meal), [
            'name' => $meal->name,
            'emoji' => $meal->emoji,
            'category' => $meal->category,
            'time_minutes' => $meal->time_minutes,
            'difficulty' => $meal->difficulty,
            'price' => $meal->price,
            'base_servings' => 4,
            'color_start' => $meal->color_start,
            'color_end' => $meal->color_end,
            'accent_color' => $meal->accent_color,
            'description' => $meal->description,
            'ingredients' => $meal->ingredients,
        ]);

        $response->assertRedirect(route('admin.meals.index'));
        $this->assertSame(4, $meal->fresh()->base_servings);
    }

    public function test_a_recipe_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $meal = Meal::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.meals.destroy', $meal));

        $response->assertRedirect(route('admin.meals.index'));
        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
    }
}
