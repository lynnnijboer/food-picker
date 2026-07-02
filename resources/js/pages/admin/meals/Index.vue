<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { Pencil, Plus, Trash2 } from "@lucide/vue";
import Heading from "@/components/Heading.vue";
import { Button } from "@/components/ui/button";
import { create, destroy, edit, index } from "@/routes/admin/meals";
import type { Meal } from "@/types";

const { meals } = defineProps<{
  meals: Meal[];
}>();

defineOptions({
  layout: {
    breadcrumbs: [{ title: "Recepten", href: index() }],
  },
});

const formatPrice = (price: number): string =>
  new Intl.NumberFormat("nl-NL", { style: "currency", currency: "EUR" }).format(
    price,
  );

const removeMeal = (meal: Meal): void => {
  if (!confirm(`"${meal.name}" verwijderen?`)) {
    return;
  }

  router.delete(destroy(meal.id).url);
};
</script>

<template>
  <Head title="Recepten" />

  <div class="flex flex-col space-y-6">
    <div class="flex items-center justify-between">
      <Heading
        title="Recepten"
        description="Beheer de maaltijden die in het weekmenu verschijnen"
      />
      <Button as-child>
        <Link :href="create()">
          <Plus />
          Nieuw recept
        </Link>
      </Button>
    </div>

    <div v-if="meals.length" class="overflow-hidden rounded-lg border">
      <table class="w-full text-left text-sm">
        <thead class="bg-muted/50 text-muted-foreground">
          <tr>
            <th class="px-4 py-3 font-medium">Recept</th>
            <th class="px-4 py-3 font-medium">Categorie</th>
            <th class="px-4 py-3 font-medium">Porties</th>
            <th class="px-4 py-3 font-medium">Prijs</th>
            <th class="px-4 py-3 font-medium"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="meal in meals" :key="meal.id" class="border-t">
            <td class="px-4 py-3">
              <span class="mr-2">{{ meal.emoji }}</span>
              <span class="font-medium">{{ meal.name }}</span>
            </td>
            <td class="px-4 py-3 text-muted-foreground">{{ meal.category }}</td>
            <td class="px-4 py-3 text-muted-foreground">
              {{ meal.baseServings }} pers.
            </td>
            <td class="px-4 py-3 text-muted-foreground">
              {{ formatPrice(meal.price) }}
            </td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                <Button variant="outline" size="icon-sm" as-child>
                  <Link :href="edit(meal.id)">
                    <Pencil />
                  </Link>
                </Button>
                <Button
                  variant="outline"
                  size="icon-sm"
                  @click="removeMeal(meal)"
                >
                  <Trash2 />
                </Button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="rounded-lg border border-dashed p-10 text-center">
      <p class="text-muted-foreground">Nog geen recepten toegevoegd.</p>
    </div>
  </div>
</template>
