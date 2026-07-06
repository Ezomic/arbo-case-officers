<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download, Search, Users } from '@lucide/vue';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { gdprExport as gdprExportUrl, index } from '@/routes/employees';
import { show as showEmployer } from '@/routes/employers';

type Employee = {
    id: string;
    first_name: string;
    last_name: string;
    email: string | null;
    employee_number: string | null;
    status: string;
    source: string;
    employer: { id: string; name: string };
    organizational_unit: { id: string; name: string } | null;
};

const props = defineProps<{
    employees: Employee[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employees', href: index() }],
    },
});

const query = ref('');

const filteredEmployees = computed(() => {
    const q = query.value.trim().toLowerCase();

    if (!q) {
        return props.employees;
    }

    return props.employees.filter((employee) => {
        const haystack = [
            employee.first_name,
            employee.last_name,
            employee.email ?? '',
            employee.employee_number ?? '',
            employee.employer.name,
            employee.organizational_unit?.name ?? '',
        ]
            .join(' ')
            .toLowerCase();

        return haystack.includes(q);
    });
});

function initials(employee: Employee): string {
    return (
        (employee.first_name[0] ?? '') + (employee.last_name[0] ?? '')
    ).toUpperCase();
}
</script>

<template>
    <Head title="Employees" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Employees"
            :description="`${employees.length} ${employees.length === 1 ? 'employee' : 'employees'} across all of your employers`"
        />

        <div v-if="employees.length > 0" class="relative w-full sm:max-w-xs">
            <Search
                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="query"
                type="search"
                placeholder="Search name, employer, unit or number…"
                class="pl-9"
            />
        </div>

        <div v-if="filteredEmployees.length > 0" class="flex flex-col gap-2">
            <div
                v-for="employee in filteredEmployees"
                :key="employee.id"
                class="flex items-center gap-3 rounded-lg border p-3 transition-colors hover:bg-muted/50"
            >
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-semibold text-muted-foreground"
                >
                    {{ initials(employee) }}
                </div>
                <Link
                    :href="showEmployer(employee.employer.id)"
                    class="min-w-0 flex-1"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-medium">
                            {{ employee.first_name }} {{ employee.last_name }}
                        </span>
                        <Badge
                            v-if="employee.status !== 'active'"
                            variant="outline"
                            class="capitalize"
                        >
                            {{ employee.status }}
                        </Badge>
                        <span
                            v-if="employee.employee_number"
                            class="text-xs text-muted-foreground tabular-nums"
                        >
                            #{{ employee.employee_number }}
                        </span>
                    </div>
                    <div class="mt-0.5 text-sm text-muted-foreground">
                        {{ employee.employer.name }}
                        <span v-if="employee.organizational_unit">
                            · {{ employee.organizational_unit.name }}</span
                        >
                        <span v-if="employee.email">
                            · {{ employee.email }}</span
                        >
                    </div>
                </Link>
                <Button
                    variant="ghost"
                    size="icon"
                    as-child
                    class="shrink-0 text-muted-foreground"
                >
                    <a
                        :href="gdprExportUrl(employee).url"
                        :download="`gdpr-export-${employee.last_name.toLowerCase()}.json`"
                        title="Download GDPR data export (AVG Art. 15)"
                        aria-label="Download GDPR data export"
                    >
                        <Download class="size-4" />
                    </a>
                </Button>
            </div>
        </div>

        <div
            v-else-if="employees.length > 0"
            class="flex flex-col items-center gap-2 rounded-lg border border-dashed py-12 text-center"
        >
            <Search class="size-8 text-muted-foreground" />
            <p class="font-medium">No matching employees</p>
            <p class="text-sm text-muted-foreground">
                Try a different search term.
            </p>
        </div>

        <div
            v-else
            class="flex flex-col items-center gap-3 rounded-lg border border-dashed py-16 text-center"
        >
            <div
                class="flex size-12 items-center justify-center rounded-full bg-muted"
            >
                <Users class="size-6 text-muted-foreground" />
            </div>
            <div>
                <p class="font-medium">No employees yet</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Add employees from an employer's page.
                </p>
            </div>
        </div>
    </div>
</template>
