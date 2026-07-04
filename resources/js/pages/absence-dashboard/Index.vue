<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { show as showCase } from '@/routes/cases';

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

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>
    <Head title="Absence Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <Heading title="Absence Dashboard" description="Overview of active absence cases" />

        <!-- KPI cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border p-4">
                <p class="text-muted-foreground text-sm">Open cases</p>
                <p class="mt-1 text-3xl font-bold">{{ props.stats.open_cases }}</p>
            </div>
            <div class="rounded-lg border p-4">
                <p class="text-muted-foreground text-sm">New this month</p>
                <p class="mt-1 text-3xl font-bold">{{ props.stats.new_this_month }}</p>
            </div>
            <div class="rounded-lg border p-4">
                <p class="text-muted-foreground text-sm">Closed this month</p>
                <p class="mt-1 text-3xl font-bold">{{ props.stats.closed_this_month }}</p>
            </div>
            <div class="rounded-lg border p-4">
                <p class="text-muted-foreground text-sm">Avg. duration (this year)</p>
                <p class="mt-1 text-3xl font-bold">
                    {{ props.stats.avg_duration_days !== null ? props.stats.avg_duration_days + ' d' : '—' }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Open cases by type -->
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">Open cases by type</h2>
                <div v-if="props.openByType.length === 0" class="text-muted-foreground text-sm">No open cases.</div>
                <div class="space-y-2">
                    <div v-for="row in props.openByType" :key="row.case_type" class="flex items-center justify-between text-sm">
                        <span class="capitalize">{{ row.case_type }}</span>
                        <span class="font-medium">{{ row.count }}</span>
                    </div>
                </div>
            </div>

            <!-- Top employers by open cases -->
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">Top employers by open cases</h2>
                <div v-if="props.topEmployers.length === 0" class="text-muted-foreground text-sm">No open cases.</div>
                <div class="space-y-2">
                    <div v-for="row in props.topEmployers" :key="row.name" class="flex items-center justify-between text-sm">
                        <span class="truncate">{{ row.name }}</span>
                        <span class="ml-2 shrink-0 font-medium">{{ row.open_count }}</span>
                    </div>
                </div>
            </div>

            <!-- Overdue return dates -->
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">Overdue return dates</h2>
                <div v-if="props.overdueReturnDates.length === 0" class="text-muted-foreground text-sm">None overdue.</div>
                <div class="space-y-3">
                    <div v-for="item in props.overdueReturnDates" :key="item.id" class="text-sm">
                        <div class="flex items-center justify-between gap-2">
                            <Link :href="showCase(item.id)" class="truncate font-medium underline underline-offset-4">
                                {{ item.employee_name }}
                            </Link>
                            <span class="text-destructive shrink-0 text-xs font-medium">{{ item.days_overdue }}d overdue</span>
                        </div>
                        <p class="text-muted-foreground text-xs">{{ item.employer_name }} · Was {{ formatDate(item.expected_return_date) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
