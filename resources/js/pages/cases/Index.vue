<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, show, store } from '@/routes/cases';

type Employer = {
    id: string;
    name: string;
};

type Employee = {
    id: string;
    employer_id: string;
    first_name: string;
    last_name: string;
    employer: Employer;
};

type CaseFile = {
    id: string;
    case_type: string | null;
    status: string;
    opened_at: string;
    closed_at: string | null;
    employer: Employer;
    employee: { id: string; first_name: string; last_name: string };
};

type CaseTypeOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    cases: CaseFile[];
    employees: Employee[];
    caseTypes: CaseTypeOption[];
    allowedTypesByEmployer: Record<string, string[]>;
}>();

const selectedEmployeeId = ref('');

const availableCaseTypes = computed(() => {
    if (!selectedEmployeeId.value) return props.caseTypes;
    const employee = props.employees.find((e) => e.id === selectedEmployeeId.value);
    if (!employee) return props.caseTypes;
    const allowed = props.allowedTypesByEmployer[employee.employer_id];
    if (!allowed || allowed.length === 0) return props.caseTypes;
    return props.caseTypes.filter((t) => allowed.includes(t.value));
});

function caseTypeLabel(value: string | null): string {
    if (!value) return '';
    return props.caseTypes.find((t) => t.value === value)?.label ?? value;
}

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Cases', href: index() }],
    },
});

const showDialog = ref(false);
</script>

<template>
    <Head title="Cases" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <Heading title="Cases" description="Dossiers currently being managed" />
            <Button variant="outline" size="icon" aria-label="Report absence" @click="showDialog = true">
                <Plus class="size-4" />
            </Button>
        </div>

        <div class="flex flex-col gap-2">
            <Link
                v-for="caseFile in cases"
                :key="caseFile.id"
                :href="show(caseFile.id)"
                class="rounded-lg border p-4 hover:bg-muted"
            >
                <div class="font-medium">
                    {{ caseFile.employee.first_name }} {{ caseFile.employee.last_name }}
                    <span v-if="caseFile.case_type" class="font-normal text-muted-foreground">— {{ caseTypeLabel(caseFile.case_type) }}</span>
                </div>
                <div class="text-sm text-muted-foreground">
                    {{ caseFile.employer.name }} · opened {{ caseFile.opened_at }} · {{ caseFile.status }}
                </div>
            </Link>
            <p v-if="cases.length === 0" class="text-sm text-muted-foreground">
                No cases yet — report an absence above.
            </p>
        </div>
    </div>

    <Dialog v-model:open="showDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Report absence</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="store.form()"
                v-slot="{ errors, processing }"
                :reset-on-success="['start_date']"
                class="space-y-3"
            >
                <div class="grid gap-2">
                    <Label for="employee_id">Employee</Label>
                    <select
                        id="employee_id"
                        name="employee_id"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                        @change="selectedEmployeeId = ($event.target as HTMLSelectElement).value"
                    >
                        <option value="" disabled selected>Select an employee</option>
                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                            {{ employee.first_name }} {{ employee.last_name }} — {{ employee.employer.name }}
                        </option>
                    </select>
                    <InputError :message="errors.employee_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="case_type">Dossier type</Label>
                    <select
                        id="case_type"
                        name="case_type"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option value="" disabled selected>Select a type</option>
                        <option v-for="type in availableCaseTypes" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </option>
                    </select>
                    <InputError :message="errors.case_type" />
                </div>
                <div class="grid gap-2">
                    <Label for="start_date">Start date</Label>
                    <Input id="start_date" type="date" name="start_date" required />
                    <InputError :message="errors.start_date" />
                </div>
                <Button type="submit" :disabled="processing">Report absence</Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
