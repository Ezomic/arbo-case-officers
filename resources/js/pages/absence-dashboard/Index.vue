<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    CalendarCheck,
    CalendarClock,
    FilePlus,
    FolderOpen,
    Timer,
    TriangleAlert,
} from '@lucide/vue';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { formatDate } from '@/lib/date';
import { show as showCase } from '@/routes/cases';
import { show as showEmployer } from '@/routes/employers';

type Stats = {
    open_cases: number;
    new_this_month: number;
    closed_this_month: number;
    avg_duration_days: number | null;
};

type TypeCount = {
    case_type: string;
    count: number;
};

type EmployerCount = {
    id: string;
    name: string;
    open_count: number;
};

type OverdueCase = {
    id: string;
    employee_name: string;
    employer_name: string;
    expected_return_date: string;
    days_overdue: number;
};

const props = defineProps<{
    stats: Stats;
    openByType: TypeCount[];
    topEmployers: EmployerCount[];
    overdueReturnDates: OverdueCase[];
}>();

const kpis = computed(() => [
    {
        label: 'Open cases',
        value: String(props.stats.open_cases),
        icon: FolderOpen,
    },
    {
        label: 'New this month',
        value: String(props.stats.new_this_month),
        icon: FilePlus,
    },
    {
        label: 'Closed this month',
        value: String(props.stats.closed_this_month),
        icon: CalendarCheck,
    },
    {
        label: 'Avg. duration (this year)',
        value:
            props.stats.avg_duration_days !== null
                ? `${props.stats.avg_duration_days} days`
                : '—',
        icon: Timer,
    },
]);

const maxTypeCount = computed(() =>
    Math.max(1, ...props.openByType.map((row) => row.count)),
);

const maxEmployerCount = computed(() =>
    Math.max(1, ...props.topEmployers.map((row) => row.open_count)),
);
</script>

<template>
    <Head title="Absence Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Absence Dashboard"
            description="Overview of active absence cases"
        />

        <!-- KPI cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="kpi in kpis"
                :key="kpi.label"
                class="flex items-start justify-between gap-2 rounded-lg border p-4"
            >
                <div>
                    <p class="text-sm text-muted-foreground">{{ kpi.label }}</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight">
                        {{ kpi.value }}
                    </p>
                </div>
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-md bg-muted"
                >
                    <component
                        :is="kpi.icon"
                        class="size-4.5 text-muted-foreground"
                    />
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Open cases by type -->
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">Open cases by type</h2>
                <div
                    v-if="props.openByType.length === 0"
                    class="flex flex-col items-center gap-1 py-8 text-center"
                >
                    <FolderOpen class="size-6 text-muted-foreground" />
                    <p class="text-sm text-muted-foreground">No open cases.</p>
                </div>
                <div class="space-y-3">
                    <div v-for="row in props.openByType" :key="row.case_type">
                        <div
                            class="mb-1 flex items-center justify-between text-sm"
                        >
                            <span>{{ row.case_type }}</span>
                            <span class="font-medium tabular-nums">{{
                                row.count
                            }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-muted">
                            <div
                                class="h-1.5 rounded-full bg-primary"
                                :style="{
                                    width: `${(row.count / maxTypeCount) * 100}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top employers by open cases -->
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">Top employers by open cases</h2>
                <div
                    v-if="props.topEmployers.length === 0"
                    class="flex flex-col items-center gap-1 py-8 text-center"
                >
                    <FolderOpen class="size-6 text-muted-foreground" />
                    <p class="text-sm text-muted-foreground">No open cases.</p>
                </div>
                <div class="space-y-3">
                    <div v-for="row in props.topEmployers" :key="row.id">
                        <div
                            class="mb-1 flex items-center justify-between gap-2 text-sm"
                        >
                            <Link
                                :href="showEmployer(row.id)"
                                class="truncate hover:underline hover:underline-offset-4"
                            >
                                {{ row.name }}
                            </Link>
                            <span class="shrink-0 font-medium tabular-nums">{{
                                row.open_count
                            }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-muted">
                            <div
                                class="h-1.5 rounded-full bg-primary"
                                :style="{
                                    width: `${(row.open_count / maxEmployerCount) * 100}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue return dates -->
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 flex items-center gap-2 font-medium">
                    Overdue return dates
                    <span
                        v-if="props.overdueReturnDates.length > 0"
                        class="inline-flex size-5 items-center justify-center rounded-full bg-destructive/10 text-xs font-semibold text-destructive tabular-nums"
                    >
                        {{ props.overdueReturnDates.length }}
                    </span>
                </h2>
                <div
                    v-if="props.overdueReturnDates.length === 0"
                    class="flex flex-col items-center gap-1 py-8 text-center"
                >
                    <CalendarClock class="size-6 text-muted-foreground" />
                    <p class="text-sm text-muted-foreground">
                        No overdue return dates — everything on track.
                    </p>
                </div>
                <div class="space-y-2">
                    <Link
                        v-for="item in props.overdueReturnDates"
                        :key="item.id"
                        :href="showCase(item.id)"
                        class="block rounded-md border border-destructive/20 bg-destructive/5 p-2.5 transition-colors hover:bg-destructive/10"
                    >
                        <div
                            class="flex items-center justify-between gap-2 text-sm"
                        >
                            <span class="truncate font-medium">{{
                                item.employee_name
                            }}</span>
                            <span
                                class="flex shrink-0 items-center gap-1 text-xs font-medium text-destructive"
                            >
                                <TriangleAlert class="size-3" />
                                {{ item.days_overdue }}d overdue
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            {{ item.employer_name }} · was
                            {{ formatDate(item.expected_return_date) }}
                        </p>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
