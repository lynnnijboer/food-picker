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
    colorStart: string;
    colorEnd: string;
    accentColor: string;
    description: string;
    ingredients: MealIngredient[];
}

export interface MealSelection {
    servings: number;
    day: string | null;
}

export interface WeekDay {
    key: string;
    full: string;
    short: string;
}
