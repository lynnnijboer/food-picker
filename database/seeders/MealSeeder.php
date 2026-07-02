<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    /**
     * Seed the meals table with the week menu catalogue.
     */
    public function run(): void
    {
        foreach ($this->meals() as $meal) {
            Meal::updateOrCreate(['name' => $meal['name']], $meal);
        }
    }

    /**
     * The meal catalogue shown on the week menu homepage.
     *
     * @return array<int, array<string, mixed>>
     */
    private function meals(): array
    {
        return [
            [
                'name' => 'Spaghetti bolognese', 'emoji' => '🍝', 'category' => 'Klassiek',
                'time_minutes' => 30, 'difficulty' => 'Makkelijk', 'price' => 8.50, 'base_servings' => 2,
                'color_start' => '#FFE1CF', 'color_end' => '#FFC3A2', 'accent_color' => '#E8621E',
                'description' => 'Romige tomatensaus met rundergehakt en verse kruiden, over al dente spaghetti.',
                'ingredients' => $this->ingredients([
                    ['rundergehakt', 250, 'g'], ['spaghetti', 200, 'g'], ['tomatenblokjes', 400, 'g'],
                    ['ui', 1, 'st'], ['knoflook', 2, 'teen'], ['Parmezaanse kaas', 50, 'g'],
                ]),
            ],
            [
                'name' => 'Groene curry met kip', 'emoji' => '🍛', 'category' => 'Snel',
                'time_minutes' => 25, 'difficulty' => 'Makkelijk', 'price' => 9.20, 'base_servings' => 2,
                'color_start' => '#DEF2D6', 'color_end' => '#BEE6B0', 'accent_color' => '#4E9A3E',
                'description' => 'Milde Thaise groene curry met malse kip, kokosmelk en knapperige groenten.',
                'ingredients' => $this->ingredients([
                    ['kipfilet', 300, 'g'], ['groene currypasta', 2, 'el'], ['kokosmelk', 400, 'ml'],
                    ['sperziebonen', 200, 'g'], ['jasmijnrijst', 150, 'g'], ['limoen', 1, 'st'],
                ]),
            ],
            [
                'name' => 'Zalmfilet met broccoli', 'emoji' => '🐟', 'category' => 'Gezond',
                'time_minutes' => 20, 'difficulty' => 'Makkelijk', 'price' => 11.00, 'base_servings' => 2,
                'color_start' => '#FFD8D4', 'color_end' => '#FFB6AC', 'accent_color' => '#E86A5E',
                'description' => 'Geroosterde zalm met gestoomde broccoli, krieltjes en een frisse citroendressing.',
                'ingredients' => $this->ingredients([
                    ['zalmfilet', 2, 'st'], ['broccoli', 400, 'g'], ['krieltjes', 400, 'g'],
                    ['citroen', 1, 'st'], ['dille', 1, 'takje'], ['olijfolie', 2, 'el'],
                ]),
            ],
            [
                'name' => 'Vegetarische chili', 'emoji' => '🌶️', 'category' => 'Vega',
                'time_minutes' => 35, 'difficulty' => 'Makkelijk', 'price' => 6.80, 'base_servings' => 2,
                'color_start' => '#FFD2C2', 'color_end' => '#FF9E86', 'accent_color' => '#C43A20',
                'description' => 'Stevige chili met twee soorten bonen, paprika en een vleugje komijn.',
                'ingredients' => $this->ingredients([
                    ['kidneybonen', 400, 'g'], ['zwarte bonen', 400, 'g'], ['tomatenblokjes', 400, 'g'],
                    ['paprika', 2, 'st'], ['ui', 1, 'st'], ['rijst', 150, 'g'],
                ]),
            ],
            [
                'name' => 'Pompoensoep', 'emoji' => '🎃', 'category' => 'Comfort',
                'time_minutes' => 30, 'difficulty' => 'Makkelijk', 'price' => 5.50, 'base_servings' => 2,
                'color_start' => '#FFE9C7', 'color_end' => '#FFD79A', 'accent_color' => '#E0912B',
                'description' => 'Fluweelzachte pompoensoep met een scheutje kokos en verse gember.',
                'ingredients' => $this->ingredients([
                    ['flespompoen', 600, 'g'], ['ui', 1, 'st'], ['kokosmelk', 200, 'ml'],
                    ['groentebouillon', 500, 'ml'], ['gember', 1, 'st'], ['zuurdesembrood', 4, 'sneden'],
                ]),
            ],
            [
                'name' => 'Kip met zoete aardappel', 'emoji' => '🍠', 'category' => 'Gezond',
                'time_minutes' => 35, 'difficulty' => 'Makkelijk', 'price' => 8.90, 'base_servings' => 2,
                'color_start' => '#FCE1BE', 'color_end' => '#F8CE8E', 'accent_color' => '#C98A2E',
                'description' => 'Kruidige kipfilet uit de oven met geroosterde zoete aardappel en courgette.',
                'ingredients' => $this->ingredients([
                    ['kipfilet', 300, 'g'], ['zoete aardappel', 500, 'g'], ['courgette', 1, 'st'],
                    ['rode ui', 1, 'st'], ['honing', 1, 'el'], ['tijm', 1, 'takje'],
                ]),
            ],
            [
                'name' => 'Pizza margherita', 'emoji' => '🍕', 'category' => 'Comfort',
                'time_minutes' => 20, 'difficulty' => 'Makkelijk', 'price' => 7.20, 'base_servings' => 2,
                'color_start' => '#FFDAD0', 'color_end' => '#FFB4A3', 'accent_color' => '#D24B33',
                'description' => 'Dunne bodem met passata, romige mozzarella en verse basilicum.',
                'ingredients' => $this->ingredients([
                    ['pizzabodem', 2, 'st'], ['passata', 150, 'g'], ['mozzarella', 250, 'g'],
                    ['basilicum', 1, 'bosje'], ['olijfolie', 1, 'el'],
                ]),
            ],
            [
                'name' => 'Pad thai', 'emoji' => '🍜', 'category' => 'Snel',
                'time_minutes' => 25, 'difficulty' => 'Gemiddeld', 'price' => 9.50, 'base_servings' => 2,
                'color_start' => '#FDE7C0', 'color_end' => '#FBD08A', 'accent_color' => '#CE8C22',
                'description' => 'Wokgerecht met rijstnoedels, garnalen, ei en geroosterde pinda’s.',
                'ingredients' => $this->ingredients([
                    ['rijstnoedels', 200, 'g'], ['garnalen', 200, 'g'], ['ei', 2, 'st'],
                    ['taugé', 150, 'g'], ['pinda’s', 40, 'g'], ['limoen', 1, 'st'],
                ]),
            ],
            [
                'name' => 'Stamppot boerenkool', 'emoji' => '🥬', 'category' => 'Klassiek',
                'time_minutes' => 40, 'difficulty' => 'Makkelijk', 'price' => 7.00, 'base_servings' => 2,
                'color_start' => '#DDEFD0', 'color_end' => '#BCE0A6', 'accent_color' => '#4C8B36',
                'description' => 'Hollandse stamppot met boerenkool, rookworst en knapperige spekjes.',
                'ingredients' => $this->ingredients([
                    ['aardappels', 600, 'g'], ['boerenkool', 400, 'g'], ['rookworst', 1, 'st'],
                    ['spekjes', 100, 'g'], ['mosterd', 1, 'el'], ['melk', 100, 'ml'],
                ]),
            ],
            [
                'name' => 'Burrito bowl', 'emoji' => '🌯', 'category' => 'Snel',
                'time_minutes' => 20, 'difficulty' => 'Makkelijk', 'price' => 8.30, 'base_servings' => 2,
                'color_start' => '#E7F1C6', 'color_end' => '#CFE79A', 'accent_color' => '#6B9A2E',
                'description' => 'Frisse bowl met rijst, bonen, maïs, avocado en een limoendressing.',
                'ingredients' => $this->ingredients([
                    ['rijst', 150, 'g'], ['kidneybonen', 400, 'g'], ['maïs', 150, 'g'],
                    ['avocado', 1, 'st'], ['tomaat', 2, 'st'], ['limoen', 1, 'st'],
                ]),
            ],
            [
                'name' => 'Paddenstoelenrisotto', 'emoji' => '🍄', 'category' => 'Vega',
                'time_minutes' => 35, 'difficulty' => 'Gemiddeld', 'price' => 7.60, 'base_servings' => 2,
                'color_start' => '#EFE6D6', 'color_end' => '#DED0B6', 'accent_color' => '#9A824F',
                'description' => 'Romige risotto met kastanjechampignons, witte wijn en Parmezaan.',
                'ingredients' => $this->ingredients([
                    ['risottorijst', 200, 'g'], ['kastanjechampignons', 250, 'g'], ['ui', 1, 'st'],
                    ['witte wijn', 100, 'ml'], ['Parmezaanse kaas', 50, 'g'], ['groentebouillon', 750, 'ml'],
                ]),
            ],
            [
                'name' => 'Gehaktballen met puree', 'emoji' => '🥔', 'category' => 'Klassiek',
                'time_minutes' => 45, 'difficulty' => 'Gemiddeld', 'price' => 7.80, 'base_servings' => 2,
                'color_start' => '#E9DDCF', 'color_end' => '#D6C3AD', 'accent_color' => '#8A6A4A',
                'description' => 'Ouderwetse gehaktballen met romige aardappelpuree en jus.',
                'ingredients' => $this->ingredients([
                    ['half-om-half gehakt', 300, 'g'], ['aardappels', 600, 'g'], ['ui', 1, 'st'],
                    ['paneermeel', 40, 'g'], ['melk', 150, 'ml'], ['runderjus', 1, 'zakje'],
                ]),
            ],
            [
                'name' => 'Lasagne al forno', 'emoji' => '🍝', 'category' => 'Comfort',
                'time_minutes' => 60, 'difficulty' => 'Gemiddeld', 'price' => 14.40, 'base_servings' => 4,
                'color_start' => '#FBDCC8', 'color_end' => '#F5B79A', 'accent_color' => '#B24A26',
                'description' => 'Ovenschotel voor het hele gezin: lagen pasta, ragù en romige bechamelsaus. Een grote bak, ideaal om over meerdere dagen te eten.',
                'ingredients' => $this->ingredients([
                    ['half-om-half gehakt', 600, 'g'], ['lasagnebladen', 250, 'g'], ['tomatenblokjes', 800, 'g'],
                    ['ui', 2, 'st'], ['knoflook', 3, 'teen'], ['bechamelsaus', 500, 'ml'],
                    ['Parmezaanse kaas', 100, 'g'],
                ]),
            ],
        ];
    }

    /**
     * Normalise the compact ingredient tuples into structured records.
     *
     * @param  array<int, array{0: string, 1: int|float, 2: string}>  $rows
     * @return array<int, array{name: string, amount: int|float, unit: string}>
     */
    private function ingredients(array $rows): array
    {
        return array_map(fn (array $row) => [
            'name' => $row[0],
            'amount' => $row[1],
            'unit' => $row[2],
        ], $rows);
    }
}
