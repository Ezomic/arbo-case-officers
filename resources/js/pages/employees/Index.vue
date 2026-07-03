<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
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
            <Link
                v-for="employee in employees"
                :key="employee.id"
                :href="showEmployer(employee.employer.id)"
                class="rounded-lg border p-4 hover:bg-muted"
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
            <p v-if="employees.length === 0" class="text-sm text-muted-foreground">
                No employees yet — add one from an employer's page.
            </p>
        </div>
    </div>
</template>
