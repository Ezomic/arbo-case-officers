<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { employee as gdprExportUrl } from '@/routes/gdpr-export';
import { show as showEmployer } from '@/routes/employers';
import { index } from '@/routes/employees';

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

defineProps<{
    employees: Employee[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employees', href: index() }],
    },
});
</script>

<template>
    <Head title="Employees" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Employees"
            description="Every employee across all of your employers"
        />

        <div class="flex flex-col gap-2">
            <div
                v-for="employee in employees"
                :key="employee.id"
                class="flex items-center gap-2 rounded-lg border p-4"
            >
                <Link
                    :href="showEmployer(employee.employer.id)"
                    class="min-w-0 flex-1 hover:underline"
                >
                    <div class="font-medium">{{ employee.first_name }} {{ employee.last_name }}</div>
                    <div class="text-sm text-muted-foreground">
                        {{ employee.employer.name }}
                        <span v-if="employee.organizational_unit"> · {{ employee.organizational_unit.name }}</span>
                        <span v-if="employee.employee_number"> · #{{ employee.employee_number }}</span>
                        <span v-if="employee.email"> · {{ employee.email }}</span>
                        · {{ employee.status }} ({{ employee.source }})
                    </div>
                </Link>
                <a
                    :href="gdprExportUrl(employee).url"
                    :download="`gdpr-export-${employee.last_name.toLowerCase()}.json`"
                    title="Download GDPR data export (AVG Art. 15)"
                >
                    <Button variant="ghost" size="icon" as-child>
                        <span><Download class="size-4" /></span>
                    </Button>
                </a>
            </div>
            <p v-if="employees.length === 0" class="text-sm text-muted-foreground">
                No employees yet — add one from an employer's page.
            </p>
        </div>
    </div>
</template>
