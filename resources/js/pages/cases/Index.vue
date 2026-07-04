<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ChevronRight, FolderOpen, Plus, Search } from '@lucide/vue';
import { computed, ref } from 'vue';
import CaseStatusBadge from '@/components/CaseStatusBadge.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
    if (!selectedEmployeeId.value) {
        return props.caseTypes;
    }

    const employee = props.employees.find(
        (e) => e.id === selectedEmployeeId.value,
    );

    if (!employee) {
        return props.caseTypes;
    }

    const allowed = props.allowedTypesByEmployer[employee.employer_id];

    if (!allowed || allowed.length === 0) {
        return props.caseTypes;
    }

    return props.caseTypes.filter((t) => allowed.includes(t.value));
});

function caseTypeLabel(value: string | null): string {
    if (!value) {
        return '';
    }

    return props.caseTypes.find((t) => t.value === value)?.label ?? value;
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('nl-NL', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Cases', href: index() }],
    },
});

const showDialog = ref(false);

const query = ref('');
const statusFilter = ref<'all' | 'open' | 'closed'>('all');

const statusOptions = [
    { value: 'all', label: 'All' },
    { value: 'open', label: 'Open' },
    { value: 'closed', label: 'Closed' },
] as const;

const openCount = computed(
    () => props.cases.filter((c) => c.status === 'open').length,
);

const filteredCases = computed(() => {
    const q = query.value.trim().toLowerCase();

    return props.cases.filter((c) => {
        if (statusFilter.value !== 'all' && c.status !== statusFilter.value) {
            return false;
        }

        if (!q) {
            return true;
        }

        const haystack =
            `${c.employee.first_name} ${c.employee.last_name} ${c.employer.name} ${caseTypeLabel(c.case_type)}`.toLowerCase();

        return haystack.includes(q);
    });
});
</script>

<template>
    <Head title="Cases" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Cases"
                :description="`${openCount} open ${openCount === 1 ? 'dossier' : 'dossiers'} currently being managed`"
            />
            <Button @click="showDialog = true">
                <Plus class="size-4" />
                Report absence
            </Button>
        </div>

        <div
            v-if="cases.length > 0"
            class="flex flex-col gap-3 sm:flex-row sm:items-center"
        >
            <div class="relative w-full sm:max-w-xs">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="query"
                    type="search"
                    placeholder="Search employee, employer or type…"
                    class="pl-9"
                />
            </div>
            <div
                class="flex w-fit items-center gap-1 rounded-lg bg-muted p-1"
                role="group"
                aria-label="Filter by status"
            >
                <button
                    v-for="option in statusOptions"
                    :key="option.value"
                    type="button"
                    class="rounded-md px-3 py-1 text-sm font-medium transition-colors"
                    :class="
                        statusFilter === option.value
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    :aria-pressed="statusFilter === option.value"
                    @click="statusFilter = option.value"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>

        <div v-if="filteredCases.length > 0" class="flex flex-col gap-2">
            <Link
                v-for="caseFile in filteredCases"
                :key="caseFile.id"
                :href="show(caseFile.id)"
                class="group flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50"
            >
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-medium"
                            >{{ caseFile.employee.first_name }}
                            {{ caseFile.employee.last_name }}</span
                        >
                        <Badge v-if="caseFile.case_type" variant="secondary">{{
                            caseTypeLabel(caseFile.case_type)
                        }}</Badge>
                    </div>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{ caseFile.employer.name }} · opened
                        {{ formatDate(caseFile.opened_at) }}
                    </p>
                </div>
                <CaseStatusBadge :status="caseFile.status" />
                <ChevronRight
                    class="size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5"
                />
            </Link>
        </div>

        <div
            v-else-if="cases.length > 0"
            class="flex flex-col items-center gap-2 rounded-lg border border-dashed py-12 text-center"
        >
            <Search class="size-8 text-muted-foreground" />
            <p class="font-medium">No matching cases</p>
            <p class="text-sm text-muted-foreground">
                Try a different search term or status filter.
            </p>
        </div>

        <div
            v-else
            class="flex flex-col items-center gap-3 rounded-lg border border-dashed py-16 text-center"
        >
            <div
                class="flex size-12 items-center justify-center rounded-full bg-muted"
            >
                <FolderOpen class="size-6 text-muted-foreground" />
            </div>
            <div>
                <p class="font-medium">No cases yet</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Report an absence to open the first dossier.
                </p>
            </div>
            <Button variant="outline" @click="showDialog = true">
                <Plus class="size-4" />
                Report absence
            </Button>
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
                        @change="
                            selectedEmployeeId = (
                                $event.target as HTMLSelectElement
                            ).value
                        "
                    >
                        <option value="" disabled selected>
                            Select an employee
                        </option>
                        <option
                            v-for="employee in employees"
                            :key="employee.id"
                            :value="employee.id"
                        >
                            {{ employee.first_name }} {{ employee.last_name }} —
                            {{ employee.employer.name }}
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
                        <option value="" disabled selected>
                            Select a type
                        </option>
                        <option
                            v-for="type in availableCaseTypes"
                            :key="type.value"
                            :value="type.value"
                        >
                            {{ type.label }}
                        </option>
                    </select>
                    <InputError :message="errors.case_type" />
                </div>
                <div class="grid gap-2">
                    <Label for="start_date">Start date</Label>
                    <Input
                        id="start_date"
                        type="date"
                        name="start_date"
                        required
                    />
                    <InputError :message="errors.start_date" />
                </div>
                <Button type="submit" :disabled="processing"
                    >Report absence</Button
                >
            </Form>
        </DialogContent>
    </Dialog>
</template>
