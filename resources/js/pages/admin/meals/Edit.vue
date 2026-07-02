<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Heading from "@/components/Heading.vue";
import { edit, index as mealsIndex, update } from "@/routes/admin/meals";
import type { Meal } from "@/types";
import MealForm from "./MealForm.vue";

const { meal } = defineProps<{
  meal: Meal;
}>();

defineOptions({
  layout: {
    breadcrumbs: [
      { title: "Recepten", href: mealsIndex() },
      { title: "Recept bewerken", href: edit(meal.id) },
    ],
  },
});
</script>

<template>
  <Head :title="`${meal.name} bewerken`" />

  <div class="flex flex-col space-y-6">
    <Heading title="Recept bewerken" :description="meal.name" />

    <MealForm
      :meal="meal"
      :submit-url="update(meal.id).url"
      submit-method="put"
    />
  </div>
</template>
