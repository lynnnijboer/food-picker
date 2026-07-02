<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Heading from "@/components/Heading.vue";
import { create, store, index as mealsIndex } from "@/routes/admin/meals";
import MealForm from "./MealForm.vue";

defineOptions({
  layout: {
    breadcrumbs: [
      { title: "Recepten", href: mealsIndex() },
      { title: "Nieuw recept", href: create() },
    ],
  },
});
</script>

<template>
  <Head title="Nieuw recept" />

  <div class="flex flex-col space-y-6">
    <Heading
      title="Nieuw recept"
      description="Voeg een maaltijd toe aan het weekmenu"
    />

    <MealForm :submit-url="store().url" submit-method="post" />
  </div>
</template>
