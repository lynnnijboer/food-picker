export interface MealIngredient {
  name: string;
  amount: number;
  unit: string;
}

export interface Meal {
  id: number;
  name: string;
  emoji: string;
  category: string;
  timeMinutes: number;
  difficulty: string;
  price: number;
  baseServings: number;
  colorStart: string;
  colorEnd: string;
  accentColor: string;
  description: string;
  ingredients: MealIngredient[];
}

export interface MealSelection {
  servings: number;
  /**
   * Consecutive day keys this meal occupies, in order. Meals whose recipe
   * yields more than `servings` portions per sitting span multiple days so
   * the leftovers get eaten right away (never more than a couple of days
   * out). Empty when the meal is picked but not yet planned on a day.
   */
  days: string[];
}

export interface WeekDay {
  key: string;
  full: string;
  short: string;
}
