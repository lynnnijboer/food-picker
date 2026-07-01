<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { Meal, MealSelection, WeekDay } from '@/types';

const props = defineProps<{
    meals: Meal[];
}>();

const DAYS: WeekDay[] = [
    { key: 'ma', full: 'Maandag', short: 'Ma' },
    { key: 'di', full: 'Dinsdag', short: 'Di' },
    { key: 'wo', full: 'Woensdag', short: 'Wo' },
    { key: 'do', full: 'Donderdag', short: 'Do' },
    { key: 'vr', full: 'Vrijdag', short: 'Vr' },
];

const CATEGORIES = ['Alles', 'Snel', 'Vega', 'Gezond', 'Klassiek', 'Comfort'];

interface ConfettiPiece {
    left: number;
    top: number;
    size: number;
    delay: number;
    rotation: number;
    color: string;
}

const view = ref<'browse' | 'overview' | 'success'>('browse');
const detailId = ref<number | null>(null);
const query = ref('');
const category = ref('Alles');
const people = ref(2);
const selections = ref<Record<number, MealSelection>>({});
const detailServings = ref(2);
const detailDay = ref<string | null>(null);
const toast = ref<string | null>(null);
const confetti = ref<ConfettiPiece[]>([]);

let toastTimeout: ReturnType<typeof setTimeout> | null = null;

const mealById = (id: number): Meal | undefined =>
    props.meals.find((meal) => meal.id === id);

const formatPrice = (value: number): string =>
    '\u20ac ' + value.toFixed(2).replace('.', ',');

const formatNumber = (value: number): string => {
    if (Number.isInteger(value)) {
        return String(value);
    }

    return (Math.round(value * 10) / 10).toString().replace('.', ',');
};

const scaleAmount = (amount: number, unit: string, servings: number): string => {
    const factor = servings / 2;
    let value = amount * factor;

    if (unit === 'g' || unit === 'ml') {
        value = Math.max(10, Math.round(value / 10) * 10);
    } else {
        value = Math.max(0.5, Math.round(value * 2) / 2);
    }

    return formatNumber(value) + (unit ? ' ' + unit : '');
};

const mealPrice = (meal: Meal, servings: number): number =>
    meal.price * (servings / 2);

const dayFull = (key: string | null): string =>
    DAYS.find((day) => day.key === key)?.full ?? '';

const peopleLabel = (count: number): string =>
    `${count} ${count === 1 ? 'persoon' : 'personen'}`;

const tileGradient = (meal: Meal) => ({
    background: `linear-gradient(150deg, ${meal.colorStart}, ${meal.colorEnd})`,
});

const tagStyle = (meal: Meal) => ({
    color: meal.accentColor,
    background: meal.colorStart,
});

const showToast = (message: string): void => {
    toast.value = message;
    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }
    toastTimeout = setTimeout(() => {
        toast.value = null;
    }, 2100);
};

const incPeople = (): void => {
    people.value = Math.min(8, people.value + 1);
};

const decPeople = (): void => {
    people.value = Math.max(1, people.value - 1);
};

const clearQuery = (): void => {
    query.value = '';
};

const setCategory = (value: string): void => {
    category.value = value;
};

const openDetail = (id: number): void => {
    const selection = selections.value[id];
    detailId.value = id;
    detailServings.value = selection ? selection.servings : people.value;
    detailDay.value = selection ? selection.day : null;
};

const closeDetail = (): void => {
    detailId.value = null;
};

const detailInc = (): void => {
    detailServings.value = Math.min(8, detailServings.value + 1);
};

const detailDec = (): void => {
    detailServings.value = Math.max(1, detailServings.value - 1);
};

const toggleDetailDay = (key: string): void => {
    detailDay.value = detailDay.value === key ? null : key;
};

const clearDayOnOtherMeals = (
    next: Record<number, MealSelection>,
    keepId: number,
    day: string,
): void => {
    for (const key of Object.keys(next)) {
        const id = Number(key);
        if (id !== keepId && next[id].day === day) {
            next[id] = { ...next[id], day: null };
        }
    }
};

const saveDetail = (): void => {
    const id = detailId.value;
    if (id === null) {
        return;
    }

    const meal = mealById(id);
    if (!meal) {
        return;
    }

    const wasSelected = !!selections.value[id];
    const next = { ...selections.value };

    if (detailDay.value) {
        clearDayOnOtherMeals(next, id, detailDay.value);
    }

    next[id] = { servings: detailServings.value, day: detailDay.value };
    selections.value = next;
    detailId.value = null;

    showToast(wasSelected ? 'Maaltijd bijgewerkt' : `${meal.name} toegevoegd`);
};

const removeSelection = (id: number): void => {
    const next = { ...selections.value };
    delete next[id];
    selections.value = next;
    detailId.value = null;
    showToast('Maaltijd verwijderd');
};

const changeServings = (id: number, delta: number): void => {
    const current = selections.value[id];
    if (!current) {
        return;
    }

    selections.value = {
        ...selections.value,
        [id]: {
            ...current,
            servings: Math.min(8, Math.max(1, current.servings + delta)),
        },
    };
};

const setDay = (id: number, key: string): void => {
    const current = selections.value[id];
    if (!current) {
        return;
    }

    const next = { ...selections.value };
    const newDay = current.day === key ? null : key;

    if (newDay) {
        clearDayOnOtherMeals(next, id, newDay);
    }

    next[id] = { ...current, day: newDay };
    selections.value = next;
};

const surprise = (): void => {
    const available = props.meals.filter((meal) => !selections.value[meal.id]);
    const pool = available.length ? available : props.meals;
    if (!pool.length) {
        return;
    }

    const pick = pool[Math.floor(Math.random() * pool.length)];
    openDetail(pick.id);
};

const goOverview = (): void => {
    if (selectionCount.value > 0) {
        view.value = 'overview';
    }
};

const goBrowse = (): void => {
    view.value = 'browse';
};

const buildConfetti = (): void => {
    const colors = ['#FF8200', '#FFD400', '#ffffff', '#7FD3F5'];
    confetti.value = Array.from({ length: 26 }, (_, index) => ({
        left: Math.random() * 100,
        top: -6 - Math.random() * 10,
        size: 7 + Math.random() * 8,
        delay: Math.random() * 0.6,
        rotation: Math.random() * 80 - 40,
        color: colors[index % colors.length],
    }));
};

const order = (): void => {
    if (selectionCount.value === 0) {
        return;
    }

    buildConfetti();
    view.value = 'success';
};

const reset = (): void => {
    view.value = 'browse';
};

const selectionCount = computed(() => Object.keys(selections.value).length);

const hasQuery = computed(() => query.value.length > 0);

const filteredMeals = computed(() => {
    const term = query.value.trim().toLowerCase();

    return props.meals.filter((meal) => {
        const matchesCategory =
            category.value === 'Alles' || meal.category === category.value;
        const matchesQuery =
            !term ||
            meal.name.toLowerCase().includes(term) ||
            meal.category.toLowerCase().includes(term);

        return matchesCategory && matchesQuery;
    });
});

const selectedEntries = computed(() =>
    Object.keys(selections.value)
        .map((key) => {
            const id = Number(key);
            const meal = mealById(id);
            const selection = selections.value[id];

            return meal
                ? { id, meal, servings: selection.servings, day: selection.day }
                : null;
        })
        .filter((entry): entry is NonNullable<typeof entry> => entry !== null),
);

const calendarDays = computed(() =>
    DAYS.map((day) => ({
        ...day,
        entry: selectedEntries.value.find((entry) => entry.day === day.key),
    })),
);

const progressPercent = computed(() =>
    Math.min(1, selectionCount.value / DAYS.length),
);

const progressLabel = computed(
    () => `${selectionCount.value} van ${DAYS.length} maaltijden gekozen`,
);

const planHint = computed(() => {
    if (selectionCount.value >= DAYS.length) {
        return 'Je week is compleet';
    }
    if (selectionCount.value === 0) {
        return 'Kies je eerste maaltijd';
    }

    return `Nog ${DAYS.length - selectionCount.value} te gaan`;
});

const totalPortions = computed(() =>
    selectedEntries.value.reduce((sum, entry) => sum + entry.servings, 0),
);

const totalProducts = computed(() =>
    selectedEntries.value.reduce(
        (sum, entry) => sum + entry.meal.ingredients.length,
        0,
    ),
);

const totalPrice = computed(() =>
    selectedEntries.value.reduce(
        (sum, entry) => sum + mealPrice(entry.meal, entry.servings),
        0,
    ),
);

const detailMeal = computed(() =>
    detailId.value !== null ? mealById(detailId.value) : undefined,
);

const detailInWeek = computed(
    () => detailId.value !== null && !!selections.value[detailId.value],
);

const successEntries = computed(() =>
    [...selectedEntries.value].sort((a, b) => {
        const orderA = a.day ? DAYS.findIndex((day) => day.key === a.day) : 99;
        const orderB = b.day ? DAYS.findIndex((day) => day.key === b.day) : 99;

        return orderA - orderB;
    }),
);
</script>

<template>
    <Head title="Weekmenu">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link
            href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="wm-app">
        <!-- ===================== BROWSE ===================== -->
        <section v-if="view === 'browse'" class="wm-view">
            <header class="wm-header">
                <div class="wm-header-row">
                    <div class="wm-title-block">
                        <div class="wm-title">Weekmenu</div>
                        <div class="wm-subtitle">
                            Deze week &middot; {{ peopleLabel(people) }}
                        </div>
                    </div>

                    <div class="wm-stepper">
                        <svg
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#5B7385"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <button class="wm-step-btn" @click="decPeople">&minus;</button>
                        <div class="wm-stepper-value">{{ peopleLabel(people) }}</div>
                        <button class="wm-step-btn" @click="incPeople">+</button>
                    </div>

                    <button class="wm-surprise" @click="surprise">
                        <svg
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#FF8200"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 3l1.9 4.3L18 9l-4.1 1.7L12 15l-1.9-4.3L6 9l4.1-1.7z" />
                            <path d="M19 14l.8 1.8L21.5 17l-1.7.7L19 19l-.8-1.3-1.7-.7 1.7-1.2z" />
                        </svg>
                        Verras me
                    </button>

                    <div class="wm-search">
                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#8598A6"
                            stroke-width="2.2"
                            stroke-linecap="round"
                        >
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.2-3.2" />
                        </svg>
                        <input
                            v-model="query"
                            type="text"
                            placeholder="Zoek een maaltijd"
                        />
                        <button v-if="hasQuery" class="wm-search-clear" @click="clearQuery">
                            &times;
                        </button>
                    </div>
                </div>

                <div class="wm-chips">
                    <button
                        v-for="cat in CATEGORIES"
                        :key="cat"
                        class="wm-chip"
                        :class="{ 'wm-chip-active': cat === category }"
                        @click="setCategory(cat)"
                    >
                        {{ cat }}
                    </button>
                </div>
            </header>

            <div class="wm-scroll wm-grid-wrap">
                <div v-if="filteredMeals.length" class="wm-grid">
                    <div
                        v-for="meal in filteredMeals"
                        :key="meal.id"
                        class="wm-card"
                        :class="{ 'wm-card-selected': selections[meal.id] }"
                        @click="openDetail(meal.id)"
                    >
                        <div class="wm-card-tile" :style="tileGradient(meal)">
                            <span class="wm-card-emoji">{{ meal.emoji }}</span>
                            <div class="wm-card-time">
                                <svg
                                    width="12"
                                    height="12"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#5B7385"
                                    stroke-width="2.4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 7v5l3 2" />
                                </svg>
                                {{ meal.timeMinutes }} min
                            </div>
                            <div v-if="selections[meal.id]" class="wm-card-check">
                                <svg
                                    width="17"
                                    height="17"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#fff"
                                    stroke-width="3.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </div>
                        </div>
                        <div class="wm-card-body">
                            <div class="wm-card-name">{{ meal.name }}</div>
                            <div class="wm-card-meta">
                                <span class="wm-tag" :style="tagStyle(meal)">
                                    {{ meal.category }}
                                </span>
                                <span class="wm-card-price">
                                    {{ formatPrice(meal.price) }}
                                </span>
                            </div>
                            <div v-if="selections[meal.id]" class="wm-card-selinfo">
                                {{ selections[meal.id].servings }} pers.<template
                                    v-if="selections[meal.id].day"
                                >
                                    &middot; {{ dayFull(selections[meal.id].day) }}</template>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="wm-empty-results">
                    <div class="wm-empty-emoji">🔍</div>
                    <div class="wm-empty-title">Geen maaltijden gevonden</div>
                    <div class="wm-empty-sub">
                        Probeer een andere zoekterm of categorie.
                    </div>
                </div>
            </div>

            <footer class="wm-footer">
                <div class="wm-progress">
                    <div class="wm-progress-head">
                        <span class="wm-progress-label">{{ progressLabel }}</span>
                        <span class="wm-progress-hint">{{ planHint }}</span>
                    </div>
                    <div class="wm-progress-track">
                        <div
                            class="wm-progress-bar"
                            :style="{ width: progressPercent * 100 + '%' }"
                        ></div>
                    </div>
                </div>
                <button
                    class="wm-review-btn"
                    :disabled="selectionCount === 0"
                    @click="goOverview"
                >
                    Bekijk overzicht
                    <svg
                        width="19"
                        height="19"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </button>
            </footer>
        </section>

        <!-- ===================== OVERVIEW ===================== -->
        <section v-else-if="view === 'overview'" class="wm-view wm-fade">
            <header class="wm-header wm-header-overview">
                <button class="wm-back" @click="goBrowse">
                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#103A5A"
                        stroke-width="2.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M19 12H5" />
                        <path d="m11 18-6-6 6-6" />
                    </svg>
                    Terug
                </button>
                <div class="wm-title-block">
                    <div class="wm-title wm-title-sm">Jouw week</div>
                    <div class="wm-subtitle">
                        Plan je maaltijden en bestel de boodschappen
                    </div>
                </div>
            </header>

            <div class="wm-overview-body">
                <div class="wm-scroll wm-overview-main">
                    <div class="wm-section-label">Weekkalender</div>
                    <div class="wm-calendar">
                        <div v-for="day in calendarDays" :key="day.key" class="wm-cal-day">
                            <div class="wm-cal-name">{{ day.full }}</div>
                            <div
                                v-if="day.entry"
                                class="wm-cal-fill"
                                :style="tileGradient(day.entry.meal)"
                            >
                                <span class="wm-cal-emoji">{{ day.entry.meal.emoji }}</span>
                                <div class="wm-cal-meal">{{ day.entry.meal.name }}</div>
                            </div>
                            <div v-else class="wm-cal-empty">Vrij</div>
                        </div>
                    </div>

                    <div class="wm-section-label">
                        Gekozen maaltijden &middot; {{ selectionCount }}
                    </div>

                    <div v-if="selectedEntries.length" class="wm-plan-list">
                        <div
                            v-for="entry in selectedEntries"
                            :key="entry.id"
                            class="wm-plan-row"
                        >
                            <div class="wm-plan-tile" :style="tileGradient(entry.meal)">
                                <span class="wm-plan-emoji">{{ entry.meal.emoji }}</span>
                            </div>
                            <div class="wm-plan-info">
                                <div class="wm-plan-name">{{ entry.meal.name }}</div>
                                <div class="wm-plan-days">
                                    <button
                                        v-for="day in DAYS"
                                        :key="day.key"
                                        class="wm-day-pill wm-day-pill-sm"
                                        :class="{ 'wm-day-pill-active': entry.day === day.key }"
                                        @click="setDay(entry.id, day.key)"
                                    >
                                        {{ day.short }}
                                    </button>
                                </div>
                            </div>
                            <div class="wm-plan-stepper">
                                <button
                                    class="wm-step-btn wm-step-btn-sm"
                                    @click="changeServings(entry.id, -1)"
                                >
                                    &minus;
                                </button>
                                <div class="wm-plan-servings">
                                    {{ entry.servings }} personen
                                </div>
                                <button
                                    class="wm-step-btn wm-step-btn-sm"
                                    @click="changeServings(entry.id, 1)"
                                >
                                    +
                                </button>
                            </div>
                            <button class="wm-remove" @click="removeSelection(entry.id)">
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M3 6h18" />
                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div v-else class="wm-plan-empty">
                        <div class="wm-plan-empty-title">Nog geen maaltijden gekozen</div>
                        <div class="wm-plan-empty-sub">
                            Ga terug en kies je maaltijden voor deze week.
                        </div>
                    </div>
                </div>

                <aside class="wm-summary">
                    <div class="wm-summary-title">Boodschappen</div>
                    <div class="wm-summary-sub">Geschat op basis van je keuze</div>

                    <div class="wm-summary-rows">
                        <div class="wm-summary-row">
                            <span class="wm-summary-key">Maaltijden</span>
                            <span class="wm-summary-val">{{ selectedEntries.length }}</span>
                        </div>
                        <div class="wm-summary-row">
                            <span class="wm-summary-key">Porties totaal</span>
                            <span class="wm-summary-val">{{ totalPortions }}</span>
                        </div>
                        <div class="wm-summary-row">
                            <span class="wm-summary-key">Producten</span>
                            <span class="wm-summary-val">{{ totalProducts }}</span>
                        </div>
                        <div class="wm-summary-row wm-summary-total">
                            <span class="wm-summary-total-key">Geschat totaal</span>
                            <span class="wm-summary-total-val">
                                {{ formatPrice(totalPrice) }}
                            </span>
                        </div>
                    </div>

                    <div class="wm-summary-spacer"></div>

                    <button
                        class="wm-order-btn"
                        :disabled="selectedEntries.length === 0"
                        @click="order"
                    >
                        <svg
                            width="21"
                            height="21"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="9" cy="20" r="1.4" />
                            <circle cx="18" cy="20" r="1.4" />
                            <path d="M2 3h2.2l2.3 12.4a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.5L23 7H5.2" />
                        </svg>
                        Bestel bij Albert Heijn
                    </button>
                    <div class="wm-summary-note">
                        We vullen je mandje op ah.nl. Je rekent daar af.
                    </div>
                </aside>
            </div>
        </section>

        <!-- ===================== SUCCESS ===================== -->
        <section v-else class="wm-success">
            <div
                v-for="(piece, index) in confetti"
                :key="index"
                class="wm-confetti"
                :style="{
                    top: piece.top + '%',
                    left: piece.left + '%',
                    width: piece.size + 'px',
                    height: piece.size * 1.4 + 'px',
                    background: piece.color,
                    '--r': piece.rotation + 'deg',
                    animationDelay: piece.delay.toFixed(2) + 's',
                }"
            ></div>

            <div class="wm-success-card">
                <div class="wm-success-check">
                    <svg
                        width="46"
                        height="46"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#22A85B"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                </div>
                <div class="wm-success-head">
                    <div class="wm-success-title">Toegevoegd aan je AH mandje</div>
                    <div class="wm-success-sub">
                        {{ successEntries.length }} maaltijden &middot; {{ totalProducts }}
                        producten staan klaar in je mandje.
                    </div>
                </div>

                <div class="wm-scroll wm-success-list">
                    <div
                        v-for="entry in successEntries"
                        :key="entry.id"
                        class="wm-success-line"
                    >
                        <span class="wm-success-emoji">{{ entry.meal.emoji }}</span>
                        <div class="wm-success-line-info">
                            <div class="wm-success-line-name">{{ entry.meal.name }}</div>
                            <div class="wm-success-line-meta">
                                {{ entry.servings }} personen &middot;
                                {{ entry.meal.ingredients.length }} producten
                            </div>
                        </div>
                        <span class="wm-success-day">
                            {{ entry.day ? dayFull(entry.day) : 'Vrij' }}
                        </span>
                    </div>
                </div>

                <div class="wm-success-total">
                    <span class="wm-success-total-key">Geschat totaal</span>
                    <span class="wm-success-total-val">{{ formatPrice(totalPrice) }}</span>
                </div>

                <a
                    class="wm-success-cta"
                    href="https://www.ah.nl/mandje"
                    target="_blank"
                    rel="noopener"
                >
                    Open ah.nl om af te rekenen
                    <svg
                        width="19"
                        height="19"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#fff"
                        stroke-width="2.4"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M7 17 17 7" />
                        <path d="M7 7h10v10" />
                    </svg>
                </a>
                <button class="wm-success-back" @click="reset">
                    Terug naar het weekmenu
                </button>
            </div>
        </section>

        <!-- ===================== DETAIL OVERLAY ===================== -->
        <div v-if="detailMeal" class="wm-overlay" @click="closeDetail">
            <div class="wm-detail" @click.stop>
                <div class="wm-detail-hero" :style="tileGradient(detailMeal)">
                    <span class="wm-detail-emoji">{{ detailMeal.emoji }}</span>
                    <div class="wm-detail-hero-badges">
                        <div class="wm-detail-badge">
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#5B7385"
                                stroke-width="2.4"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 2" />
                            </svg>
                            <span>{{ detailMeal.timeMinutes }} min</span>
                        </div>
                        <div class="wm-detail-badge">
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#5B7385"
                                stroke-width="2.4"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M3 12h4l3 8 4-16 3 8h4" />
                            </svg>
                            <span>{{ detailMeal.difficulty }}</span>
                        </div>
                    </div>
                </div>

                <div class="wm-detail-content">
                    <div class="wm-scroll wm-detail-scroll">
                        <div class="wm-detail-top">
                            <div class="wm-detail-top-text">
                                <span class="wm-tag" :style="tagStyle(detailMeal)">
                                    {{ detailMeal.category }}
                                </span>
                                <div class="wm-detail-name">{{ detailMeal.name }}</div>
                            </div>
                            <button class="wm-detail-close" @click="closeDetail">
                                <svg
                                    width="19"
                                    height="19"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.6"
                                    stroke-linecap="round"
                                >
                                    <path d="M18 6 6 18" />
                                    <path d="m6 6 12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="wm-detail-desc">{{ detailMeal.description }}</div>

                        <div class="wm-detail-servings">
                            <div>
                                <div class="wm-detail-servings-title">Aantal personen</div>
                                <div class="wm-detail-servings-sub">
                                    Ingrediënten passen zich aan
                                </div>
                            </div>
                            <div class="wm-detail-servings-stepper">
                                <button class="wm-step-btn wm-step-btn-lg" @click="detailDec">
                                    &minus;
                                </button>
                                <div class="wm-detail-servings-value">
                                    {{ detailServings }}
                                </div>
                                <button class="wm-step-btn wm-step-btn-lg" @click="detailInc">
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="wm-detail-days-block">
                            <div class="wm-detail-days-title">
                                Plan op een dag
                                <span class="wm-detail-days-optional">&middot; optioneel</span>
                            </div>
                            <div class="wm-detail-days">
                                <button
                                    v-for="day in DAYS"
                                    :key="day.key"
                                    class="wm-day-pill wm-day-pill-lg"
                                    :class="{ 'wm-day-pill-active': detailDay === day.key }"
                                    @click="toggleDetailDay(day.key)"
                                >
                                    {{ day.short }}
                                </button>
                            </div>
                        </div>

                        <div class="wm-detail-ingredients">
                            <div class="wm-detail-ingredients-title">Ingrediënten</div>
                            <div>
                                <div
                                    v-for="ingredient in detailMeal.ingredients"
                                    :key="ingredient.name"
                                    class="wm-ingredient"
                                >
                                    <div class="wm-ingredient-left">
                                        <span class="wm-ingredient-dot"></span>
                                        <span class="wm-ingredient-name">
                                            {{ ingredient.name }}
                                        </span>
                                    </div>
                                    <span class="wm-ingredient-amount">
                                        {{ scaleAmount(ingredient.amount, ingredient.unit, detailServings) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wm-detail-actions">
                        <div class="wm-detail-price">
                            <div class="wm-detail-price-label">Geschatte prijs</div>
                            <div class="wm-detail-price-value">
                                {{ formatPrice(mealPrice(detailMeal, detailServings)) }}
                            </div>
                        </div>
                        <button
                            v-if="detailInWeek"
                            class="wm-detail-remove"
                            @click="removeSelection(detailMeal.id)"
                        >
                            Verwijderen
                        </button>
                        <button class="wm-detail-save" @click="saveDetail">
                            <svg
                                width="19"
                                height="19"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            {{ detailInWeek ? 'Bijwerken' : 'Voeg toe aan de week' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- toast -->
        <div v-if="toast" class="wm-toast">
            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#5FD08A"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M20 6 9 17l-5-5" />
            </svg>
            {{ toast }}
        </div>
    </div>
</template>

<style scoped>
.wm-app {
    position: relative;
    height: 100vh;
    overflow: hidden;
    background: #edf1f4;
    font-family: 'Nunito', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    color: #103a5a;
}

.wm-view {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.wm-fade {
    animation: wmFade 0.25s ease;
}

/* scrollbars */
.wm-scroll {
    overflow-y: auto;
}

.wm-scroll::-webkit-scrollbar {
    width: 10px;
}

.wm-scroll::-webkit-scrollbar-thumb {
    background: #cfdbe4;
    border-radius: 8px;
    border: 3px solid transparent;
    background-clip: content-box;
}

.wm-scroll::-webkit-scrollbar-track {
    background: transparent;
}

/* header */
.wm-header {
    padding: 20px 30px 14px;
    background: #fff;
    border-bottom: 1px solid #e6ecf1;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.02);
}

.wm-header-row {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.wm-title-block {
    flex: 1;
    min-width: 0;
}

.wm-title {
    font-size: 25px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.4px;
    line-height: 1.05;
}

.wm-title-sm {
    font-size: 24px;
}

.wm-subtitle {
    font-size: 13.5px;
    font-weight: 600;
    color: #7c93a3;
    margin-top: 2px;
}

.wm-stepper {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f1f5f8;
    border: 1px solid #e3eaef;
    border-radius: 14px;
    padding: 7px 12px;
}

.wm-stepper-value {
    min-width: 78px;
    text-align: center;
    font-size: 15px;
    font-weight: 800;
    color: #103a5a;
}

.wm-step-btn {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    border: none;
    background: #fff;
    color: #103a5a;
    font-size: 20px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.wm-step-btn-sm {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    font-size: 18px;
}

.wm-step-btn-lg {
    width: 36px;
    height: 36px;
    border-radius: 11px;
    font-size: 22px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.wm-surprise {
    display: flex;
    align-items: center;
    gap: 9px;
    background: #fff;
    border: 1.5px solid #ffd3ac;
    color: #e86e00;
    font-size: 15px;
    font-weight: 800;
    padding: 11px 16px;
    border-radius: 14px;
    cursor: pointer;
    font-family: inherit;
}

.wm-search {
    display: flex;
    align-items: center;
    gap: 9px;
    background: #f1f5f8;
    border: 1px solid #e3eaef;
    border-radius: 14px;
    padding: 0 12px;
    height: 46px;
    width: 214px;
}

.wm-search input {
    border: none;
    background: transparent;
    outline: none;
    font-family: inherit;
    font-size: 15px;
    font-weight: 600;
    color: #103a5a;
    width: 100%;
}

.wm-search-clear {
    border: none;
    background: #d9e2e9;
    color: #5b7385;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 12px;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    flex: none;
}

.wm-chips {
    display: flex;
    gap: 9px;
    margin-top: 14px;
    flex-wrap: wrap;
}

.wm-chip {
    font-family: inherit;
    font-size: 14.5px;
    font-weight: 800;
    padding: 9px 17px;
    border-radius: 12px;
    cursor: pointer;
    border: 1.5px solid #e3eaef;
    background: #fff;
    color: #5b7385;
    transition: all 0.12s;
}

.wm-chip-active {
    border-color: #00a0e2;
    background: #00a0e2;
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 160, 226, 0.28);
}

/* grid */
.wm-grid-wrap {
    flex: 1;
    padding: 22px 30px 26px;
}

.wm-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.wm-card {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid #e6ecf1;
    box-shadow: 0 2px 6px rgba(16, 40, 60, 0.05);
    transition: all 0.14s;
}

.wm-card:hover {
    box-shadow: 0 8px 20px rgba(16, 40, 60, 0.1);
    transform: translateY(-2px);
}

.wm-card-selected {
    border: 2px solid #00a0e2;
    box-shadow: 0 8px 22px rgba(0, 160, 226, 0.18);
}

.wm-card-tile {
    position: relative;
    height: 128px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wm-card-emoji {
    font-size: 58px;
    line-height: 1;
    filter: drop-shadow(0 3px 4px rgba(0, 0, 0, 0.12));
}

.wm-card-time {
    position: absolute;
    top: 10px;
    left: 11px;
    background: rgba(255, 255, 255, 0.9);
    color: #103a5a;
    font-size: 12px;
    font-weight: 800;
    padding: 4px 9px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.wm-card-check {
    position: absolute;
    top: 10px;
    right: 11px;
    background: #00a0e2;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 8px rgba(0, 160, 226, 0.5);
}

.wm-card-body {
    padding: 12px 14px 14px;
}

.wm-card-name {
    font-size: 17.5px;
    font-weight: 800;
    color: #103a5a;
    line-height: 1.15;
    letter-spacing: -0.2px;
}

.wm-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 7px;
}

.wm-tag {
    font-size: 12.5px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 20px;
}

.wm-card-price {
    font-size: 14.5px;
    font-weight: 800;
    color: #5b7385;
}

.wm-card-selinfo {
    margin-top: 10px;
    font-size: 13px;
    font-weight: 800;
    color: #00a0e2;
    background: #e6f6fd;
    border-radius: 9px;
    padding: 6px 10px;
    text-align: center;
}

.wm-empty-results {
    text-align: center;
    padding: 80px 20px;
    color: #8598a6;
}

.wm-empty-emoji {
    font-size: 46px;
    margin-bottom: 10px;
}

.wm-empty-title {
    font-size: 19px;
    font-weight: 800;
    color: #5b7385;
}

.wm-empty-sub {
    font-size: 14.5px;
    font-weight: 600;
    margin-top: 5px;
}

/* footer */
.wm-footer {
    padding: 14px 30px;
    background: #fff;
    border-top: 1px solid #e6ecf1;
    display: flex;
    align-items: center;
    gap: 22px;
}

.wm-progress {
    flex: 1;
}

.wm-progress-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 7px;
}

.wm-progress-label {
    font-size: 15px;
    font-weight: 800;
    color: #103a5a;
}

.wm-progress-hint {
    font-size: 13.5px;
    font-weight: 700;
    color: #7c93a3;
}

.wm-progress-track {
    height: 9px;
    border-radius: 6px;
    background: #e6ecf1;
    overflow: hidden;
}

.wm-progress-bar {
    height: 100%;
    border-radius: 6px;
    background: linear-gradient(90deg, #12b3f0, #00a0e2);
    transition: width 0.3s ease;
}

.wm-review-btn {
    display: flex;
    align-items: center;
    gap: 9px;
    background: #ff8200;
    border: none;
    color: #fff;
    font-size: 17px;
    font-weight: 800;
    padding: 16px 26px;
    border-radius: 15px;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(255, 130, 0, 0.32);
    font-family: inherit;
}

.wm-review-btn:disabled {
    background: #e6ecf1;
    color: #afc0cc;
    cursor: not-allowed;
    box-shadow: none;
}

/* overview */
.wm-header-overview {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 30px;
    flex-wrap: nowrap;
}

.wm-back {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f1f5f8;
    border: 1px solid #e3eaef;
    color: #103a5a;
    font-size: 15px;
    font-weight: 800;
    padding: 10px 15px;
    border-radius: 13px;
    cursor: pointer;
    font-family: inherit;
}

.wm-overview-body {
    flex: 1;
    display: flex;
    min-height: 0;
}

.wm-overview-main {
    flex: 1;
    padding: 22px 24px 26px;
}

.wm-section-label {
    font-size: 13px;
    font-weight: 800;
    color: #7c93a3;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 10px;
}

.wm-calendar {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 11px;
    margin-bottom: 24px;
}

.wm-cal-day {
    background: #fff;
    border: 1px solid #e6ecf1;
    border-radius: 16px;
    overflow: hidden;
    min-height: 132px;
    display: flex;
    flex-direction: column;
}

.wm-cal-name {
    padding: 8px 10px;
    font-size: 13px;
    font-weight: 800;
    color: #103a5a;
    border-bottom: 1px solid #eef2f5;
    text-align: center;
}

.wm-cal-fill {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px 8px;
}

.wm-cal-emoji {
    font-size: 34px;
    line-height: 1;
}

.wm-cal-meal {
    font-size: 12.5px;
    font-weight: 800;
    color: #103a5a;
    text-align: center;
    line-height: 1.15;
    margin-top: 6px;
}

.wm-cal-empty {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c4d1da;
    font-size: 13px;
    font-weight: 700;
}

.wm-plan-list {
    display: flex;
    flex-direction: column;
    gap: 11px;
}

.wm-plan-row {
    background: #fff;
    border: 1px solid #e6ecf1;
    border-radius: 16px;
    padding: 13px 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.wm-plan-tile {
    width: 54px;
    height: 54px;
    border-radius: 13px;
    flex: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wm-plan-emoji {
    font-size: 30px;
    line-height: 1;
}

.wm-plan-info {
    flex: 1;
    min-width: 0;
}

.wm-plan-name {
    font-size: 17px;
    font-weight: 800;
    color: #103a5a;
    line-height: 1.1;
}

.wm-plan-days {
    display: flex;
    gap: 6px;
    margin-top: 8px;
    flex-wrap: wrap;
}

.wm-day-pill {
    font-family: inherit;
    font-weight: 800;
    border-radius: 10px;
    cursor: pointer;
    border: 1.5px solid #e3eaef;
    background: #f7fafb;
    color: #7c93a3;
}

.wm-day-pill-sm {
    font-size: 13px;
    width: 42px;
    padding: 8px 0;
}

.wm-day-pill-lg {
    font-size: 14.5px;
    width: 52px;
    padding: 11px 0;
    border-radius: 12px;
    background: #fff;
    color: #5b7385;
}

.wm-day-pill-active {
    border-color: #00a0e2;
    background: #00a0e2;
    color: #fff;
}

.wm-plan-stepper {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f1f5f8;
    border-radius: 12px;
    padding: 6px 9px;
}

.wm-plan-servings {
    min-width: 66px;
    text-align: center;
    font-size: 13.5px;
    font-weight: 800;
    color: #103a5a;
}

.wm-remove {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: #fdecec;
    color: #e05a4e;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: none;
}

.wm-plan-empty {
    background: #fff;
    border: 1px dashed #d6e0e7;
    border-radius: 16px;
    padding: 44px;
    text-align: center;
    color: #8598a6;
}

.wm-plan-empty-title {
    font-size: 16px;
    font-weight: 800;
    color: #5b7385;
}

.wm-plan-empty-sub {
    font-size: 14px;
    font-weight: 600;
    margin-top: 4px;
}

/* summary */
.wm-summary {
    width: 330px;
    flex: none;
    background: #fff;
    border-left: 1px solid #e6ecf1;
    padding: 24px;
    display: flex;
    flex-direction: column;
}

.wm-summary-title {
    font-size: 19px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.3px;
}

.wm-summary-sub {
    font-size: 13.5px;
    font-weight: 600;
    color: #7c93a3;
    margin-top: 2px;
}

.wm-summary-rows {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin-top: 20px;
}

.wm-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eef2f5;
}

.wm-summary-key {
    font-size: 15px;
    font-weight: 700;
    color: #5b7385;
}

.wm-summary-val {
    font-size: 16px;
    font-weight: 800;
    color: #103a5a;
}

.wm-summary-total {
    border-bottom: none;
    padding: 14px 0 4px;
}

.wm-summary-total-key {
    font-size: 16px;
    font-weight: 800;
    color: #103a5a;
}

.wm-summary-total-val {
    font-size: 23px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.5px;
}

.wm-summary-spacer {
    flex: 1;
}

.wm-order-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 11px;
    width: 100%;
    background: #ff8200;
    border: none;
    color: #fff;
    font-size: 18px;
    font-weight: 800;
    padding: 18px;
    border-radius: 16px;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(255, 130, 0, 0.34);
    font-family: inherit;
}

.wm-order-btn:disabled {
    background: #e6ecf1;
    color: #afc0cc;
    cursor: not-allowed;
    box-shadow: none;
}

.wm-summary-note {
    text-align: center;
    font-size: 12.5px;
    font-weight: 600;
    color: #9badba;
    margin-top: 11px;
}

/* success */
.wm-success {
    position: absolute;
    inset: 0;
    background: linear-gradient(165deg, #00a0e2, #0079c4);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 20px;
}

.wm-confetti {
    position: absolute;
    border-radius: 3px;
    opacity: 0;
    animation-name: wmDrop;
    animation-duration: 0.8s;
    animation-timing-function: ease-out;
    animation-fill-mode: forwards;
}

.wm-success-card {
    width: 560px;
    max-width: 100%;
    background: #fff;
    border-radius: 26px;
    padding: 38px 40px 30px;
    box-shadow: 0 40px 90px rgba(0, 50, 90, 0.4);
    animation: wmPop 0.45s cubic-bezier(0.2, 0.9, 0.3, 1.1);
    position: relative;
    z-index: 2;
}

.wm-success-check {
    width: 82px;
    height: 82px;
    border-radius: 50%;
    background: #e9faf0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: wmCheck 0.5s 0.1s both cubic-bezier(0.2, 0.9, 0.3, 1.4);
}

.wm-success-head {
    text-align: center;
    margin-top: 18px;
}

.wm-success-title {
    font-size: 27px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.5px;
}

.wm-success-sub {
    font-size: 15.5px;
    font-weight: 600;
    color: #7c93a3;
    margin-top: 6px;
    line-height: 1.4;
}

.wm-success-list {
    background: #f5f8fa;
    border-radius: 16px;
    padding: 8px 16px;
    margin-top: 22px;
    max-height: 210px;
}

.wm-success-line {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 11px 0;
    border-bottom: 1px solid #e9eef2;
}

.wm-success-line:last-child {
    border-bottom: none;
}

.wm-success-emoji {
    font-size: 26px;
    line-height: 1;
}

.wm-success-line-info {
    flex: 1;
    min-width: 0;
}

.wm-success-line-name {
    font-size: 15.5px;
    font-weight: 800;
    color: #103a5a;
    line-height: 1.1;
}

.wm-success-line-meta {
    font-size: 13px;
    font-weight: 600;
    color: #8598a6;
    margin-top: 2px;
}

.wm-success-day {
    font-size: 14.5px;
    font-weight: 800;
    color: #5b7385;
}

.wm-success-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 18px;
    padding: 0 4px;
}

.wm-success-total-key {
    font-size: 16px;
    font-weight: 800;
    color: #103a5a;
}

.wm-success-total-val {
    font-size: 24px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.5px;
}

.wm-success-cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    background: #ff8200;
    color: #fff;
    font-size: 17px;
    font-weight: 800;
    padding: 16px;
    border-radius: 15px;
    margin-top: 20px;
    box-shadow: 0 10px 22px rgba(255, 130, 0, 0.34);
}

.wm-success-back {
    width: 100%;
    background: transparent;
    border: none;
    color: #7c93a3;
    font-size: 15px;
    font-weight: 800;
    padding: 14px 0 2px;
    cursor: pointer;
    font-family: inherit;
}

/* detail overlay */
.wm-overlay {
    position: absolute;
    inset: 0;
    background: rgba(16, 40, 60, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 5;
    animation: wmFade 0.2s ease;
    backdrop-filter: blur(3px);
    padding: 20px;
}

.wm-detail {
    width: 800px;
    max-width: 100%;
    max-height: 760px;
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    display: flex;
    box-shadow: 0 40px 90px rgba(0, 30, 60, 0.4);
    animation: wmPop 0.3s cubic-bezier(0.2, 0.9, 0.3, 1.05);
}

.wm-detail-hero {
    position: relative;
    width: 296px;
    flex: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wm-detail-emoji {
    font-size: 120px;
    line-height: 1;
    filter: drop-shadow(0 6px 10px rgba(0, 0, 0, 0.16));
}

.wm-detail-hero-badges {
    position: absolute;
    bottom: 18px;
    left: 18px;
    right: 18px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.wm-detail-badge {
    background: rgba(255, 255, 255, 0.92);
    border-radius: 11px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13.5px;
    font-weight: 800;
    color: #103a5a;
}

.wm-detail-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.wm-detail-scroll {
    flex: 1;
    padding: 26px 28px 8px;
}

.wm-detail-top {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.wm-detail-top-text {
    flex: 1;
}

.wm-detail-name {
    font-size: 26px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.5px;
    line-height: 1.08;
    margin-top: 9px;
}

.wm-detail-close {
    width: 38px;
    height: 38px;
    border-radius: 11px;
    border: none;
    background: #f1f5f8;
    color: #5b7385;
    cursor: pointer;
    flex: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wm-detail-desc {
    font-size: 15px;
    font-weight: 600;
    color: #607889;
    line-height: 1.5;
    margin-top: 12px;
}

.wm-detail-servings {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 20px;
    background: #f5f8fa;
    border-radius: 14px;
    padding: 12px 15px;
}

.wm-detail-servings-title {
    font-size: 15px;
    font-weight: 800;
    color: #103a5a;
}

.wm-detail-servings-sub {
    font-size: 12.5px;
    font-weight: 600;
    color: #8598a6;
}

.wm-detail-servings-stepper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.wm-detail-servings-value {
    min-width: 34px;
    text-align: center;
    font-size: 21px;
    font-weight: 900;
    color: #103a5a;
}

.wm-detail-days-block {
    margin-top: 20px;
}

.wm-detail-days-title {
    font-size: 15px;
    font-weight: 800;
    color: #103a5a;
}

.wm-detail-days-optional {
    font-weight: 600;
    color: #9badba;
}

.wm-detail-days {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.wm-detail-ingredients {
    margin-top: 22px;
}

.wm-detail-ingredients-title {
    font-size: 15px;
    font-weight: 800;
    color: #103a5a;
    margin-bottom: 8px;
}

.wm-ingredient {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 2px;
    border-bottom: 1px solid #eef2f5;
}

.wm-ingredient-left {
    display: flex;
    align-items: center;
    gap: 11px;
}

.wm-ingredient-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #00a0e2;
    flex: none;
}

.wm-ingredient-name {
    font-size: 15px;
    font-weight: 700;
    color: #3e5566;
}

.wm-ingredient-amount {
    font-size: 14.5px;
    font-weight: 800;
    color: #8598a6;
}

.wm-detail-actions {
    padding: 14px 28px 18px;
    border-top: 1px solid #eef2f5;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
}

.wm-detail-price {
    flex: 1;
}

.wm-detail-price-label {
    font-size: 12.5px;
    font-weight: 700;
    color: #9badba;
}

.wm-detail-price-value {
    font-size: 21px;
    font-weight: 900;
    color: #103a5a;
    letter-spacing: -0.4px;
}

.wm-detail-remove {
    background: #fdecec;
    border: none;
    color: #e05a4e;
    font-size: 15px;
    font-weight: 800;
    padding: 15px 18px;
    border-radius: 14px;
    cursor: pointer;
    font-family: inherit;
}

.wm-detail-save {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #00a0e2;
    border: none;
    color: #fff;
    font-size: 16px;
    font-weight: 800;
    padding: 15px 22px;
    border-radius: 14px;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(0, 160, 226, 0.3);
    font-family: inherit;
}

/* toast */
.wm-toast {
    position: absolute;
    bottom: 96px;
    left: 50%;
    transform: translateX(-50%);
    background: #103a5a;
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    padding: 13px 22px;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(16, 40, 60, 0.4);
    z-index: 8;
    animation: wmToast 2.2s ease forwards;
    display: flex;
    align-items: center;
    gap: 10px;
}

@keyframes wmFade {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes wmPop {
    0% {
        opacity: 0;
        transform: translateY(14px) scale(0.97);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes wmToast {
    0% {
        opacity: 0;
        transform: translateX(-50%) translateY(16px);
    }
    12% {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
    88% {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
    100% {
        opacity: 0;
        transform: translateX(-50%) translateY(8px);
    }
}

@keyframes wmCheck {
    0% {
        transform: scale(0.4);
        opacity: 0;
    }
    60% {
        transform: scale(1.12);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes wmDrop {
    0% {
        opacity: 0;
        transform: translateY(-40px) rotate(0);
    }
    100% {
        opacity: 1;
        transform: translateY(0) rotate(var(--r));
    }
}

@media (max-width: 1080px) {
    .wm-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 820px) {
    .wm-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .wm-overview-body {
        flex-direction: column;
    }

    .wm-summary {
        width: 100%;
        border-left: none;
        border-top: 1px solid #e6ecf1;
    }

    .wm-app {
        height: auto;
        min-height: 100vh;
        overflow: visible;
    }

    .wm-view {
        height: auto;
        min-height: 100vh;
    }
}

@media (max-width: 560px) {
    .wm-grid {
        grid-template-columns: 1fr;
    }

    .wm-header,
    .wm-grid-wrap,
    .wm-footer {
        padding-left: 16px;
        padding-right: 16px;
    }

    .wm-search {
        width: 100%;
    }

    .wm-footer {
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
    }

    .wm-review-btn {
        justify-content: center;
    }
}
</style>
