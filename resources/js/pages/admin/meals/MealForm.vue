<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Plus, Trash2 } from "@lucide/vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import type { Meal, MealIngredient } from "@/types";

const { meal, submitUrl, submitMethod } = defineProps<{
  meal?: Meal;
  submitUrl: string;
  submitMethod: "post" | "put";
}>();

const CATEGORY_SUGGESTIONS = ["Klassiek", "Snel", "Vega", "Gezond", "Comfort"];
const DIFFICULTY_SUGGESTIONS = ["Makkelijk", "Gemiddeld", "Moeilijk"];

const emptyIngredient = (): MealIngredient => ({
  name: "",
  amount: 1,
  unit: "g",
});

const form = useForm({
  name: meal?.name ?? "",
  emoji: meal?.emoji ?? "🍽️",
  category: meal?.category ?? "",
  time_minutes: meal?.timeMinutes ?? 30,
  difficulty: meal?.difficulty ?? "",
  price: meal?.price ?? 0,
  base_servings: meal?.baseServings ?? 2,
  color_start: meal?.colorStart ?? "#FFE1CF",
  color_end: meal?.colorEnd ?? "#FFC3A2",
  accent_color: meal?.accentColor ?? "#E8621E",
  description: meal?.description ?? "",
  ingredients: meal?.ingredients.length
    ? [...meal.ingredients]
    : [emptyIngredient()],
});

const addIngredient = (): void => {
  form.ingredients.push(emptyIngredient());
};

const removeIngredient = (index: number): void => {
  if (form.ingredients.length > 1) {
    form.ingredients.splice(index, 1);
  }
};

const submit = (): void => {
  if (submitMethod === "post") {
    form.post(submitUrl);
  } else {
    form.put(submitUrl);
  }
};
</script>

<template>
  <form class="max-w-2xl space-y-8" @submit.prevent="submit">
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="grid gap-2">
        <Label for="name">Naam</Label>
        <Input
          id="name"
          v-model="form.name"
          required
          placeholder="Spaghetti bolognese"
        />
        <InputError :message="form.errors.name" />
      </div>

      <div class="grid gap-2">
        <Label for="emoji">Emoji</Label>
        <Input
          id="emoji"
          v-model="form.emoji"
          required
          maxlength="8"
          placeholder="🍝"
        />
        <InputError :message="form.errors.emoji" />
      </div>

      <div class="grid gap-2">
        <Label for="category">Categorie</Label>
        <Input
          id="category"
          v-model="form.category"
          required
          list="category-suggestions"
          placeholder="Klassiek"
        />
        <datalist id="category-suggestions">
          <option
            v-for="option in CATEGORY_SUGGESTIONS"
            :key="option"
            :value="option"
          />
        </datalist>
        <InputError :message="form.errors.category" />
      </div>

      <div class="grid gap-2">
        <Label for="difficulty">Moeilijkheidsgraad</Label>
        <Input
          id="difficulty"
          v-model="form.difficulty"
          required
          list="difficulty-suggestions"
          placeholder="Makkelijk"
        />
        <datalist id="difficulty-suggestions">
          <option
            v-for="option in DIFFICULTY_SUGGESTIONS"
            :key="option"
            :value="option"
          />
        </datalist>
        <InputError :message="form.errors.difficulty" />
      </div>

      <div class="grid gap-2">
        <Label for="time_minutes">Bereidingstijd (minuten)</Label>
        <Input
          id="time_minutes"
          v-model.number="form.time_minutes"
          type="number"
          min="1"
          required
        />
        <InputError :message="form.errors.time_minutes" />
      </div>

      <div class="grid gap-2">
        <Label for="price">Geschatte prijs (&euro;)</Label>
        <Input
          id="price"
          v-model.number="form.price"
          type="number"
          min="0"
          step="0.01"
          required
        />
        <InputError :message="form.errors.price" />
      </div>

      <div class="grid gap-2">
        <Label for="base_servings">Standaard aantal porties</Label>
        <Input
          id="base_servings"
          v-model.number="form.base_servings"
          type="number"
          min="1"
          max="12"
          required
        />
        <p class="text-xs text-muted-foreground">
          Hoeveel personen dit recept zoals geschreven voedt. Bij een groter
          huishouden wordt dit recept automatisch over meerdere dagen gepland.
        </p>
        <InputError :message="form.errors.base_servings" />
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <div class="grid gap-2">
        <Label for="color_start">Kleur (start)</Label>
        <Input
          id="color_start"
          v-model="form.color_start"
          type="color"
          class="h-10 p-1"
        />
        <InputError :message="form.errors.color_start" />
      </div>
      <div class="grid gap-2">
        <Label for="color_end">Kleur (eind)</Label>
        <Input
          id="color_end"
          v-model="form.color_end"
          type="color"
          class="h-10 p-1"
        />
        <InputError :message="form.errors.color_end" />
      </div>
      <div class="grid gap-2">
        <Label for="accent_color">Accentkleur</Label>
        <Input
          id="accent_color"
          v-model="form.accent_color"
          type="color"
          class="h-10 p-1"
        />
        <InputError :message="form.errors.accent_color" />
      </div>
    </div>

    <div class="grid gap-2">
      <Label for="description">Beschrijving</Label>
      <Textarea id="description" v-model="form.description" required rows="3" />
      <InputError :message="form.errors.description" />
    </div>

    <div class="grid gap-3">
      <div class="flex items-center justify-between">
        <Label>Ingrediënten (voor {{ form.base_servings }} personen)</Label>
        <Button
          type="button"
          variant="outline"
          size="sm"
          @click="addIngredient"
        >
          <Plus />
          Ingrediënt
        </Button>
      </div>
      <InputError :message="form.errors.ingredients" />

      <div
        v-for="(ingredient, ingredientIndex) in form.ingredients"
        :key="ingredientIndex"
        class="grid grid-cols-[1fr_5rem_5rem_auto] items-start gap-2"
      >
        <div>
          <Input v-model="ingredient.name" placeholder="Naam" required />
          <InputError
            :message="form.errors[`ingredients.${ingredientIndex}.name`]"
          />
        </div>
        <div>
          <Input
            v-model.number="ingredient.amount"
            type="number"
            min="0"
            step="0.1"
            required
          />
          <InputError
            :message="form.errors[`ingredients.${ingredientIndex}.amount`]"
          />
        </div>
        <div>
          <Input v-model="ingredient.unit" placeholder="g" />
          <InputError
            :message="form.errors[`ingredients.${ingredientIndex}.unit`]"
          />
        </div>
        <Button
          type="button"
          variant="outline"
          size="icon-sm"
          :disabled="form.ingredients.length <= 1"
          @click="removeIngredient(ingredientIndex)"
        >
          <Trash2 />
        </Button>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <Button :disabled="form.processing" type="submit">Opslaan</Button>
    </div>
  </form>
</template>
