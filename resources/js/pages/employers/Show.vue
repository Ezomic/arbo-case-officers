<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import OrganizationalUnitTree from '@/components/OrganizationalUnitTree.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/employers';
import { store as storeContract } from '@/routes/contracts';
import { store as storeEmployee } from '@/routes/employees';
import { store as storeOrganizationalUnit } from '@/routes/organizational-units';

type Employer = {
    id: string;
    name: string;
    kvk_number: string | null;
    status: string;
};

type Contract = {
    id: string;
    contract_type_label: string | null;
    start_date: string;
    end_date: string | null;
    status: string;
};

type ContractType = {
    id: string;
    name: string;
};

type OrganizationalUnit = {
    id: string;
    parent_id: string | null;
    name: string;
    is_legal_entity: boolean;
    kvk_number: string | null;
};

type Employee = {
    id: string;
    first_name: string;
    last_name: string;
    email: string | null;
    employee_number: string | null;
    source: string;
    organizational_unit: OrganizationalUnit | null;
};

defineProps<{
    employer: Employer;
    contracts: Contract[];
    contractTypes: ContractType[];
    organizationalUnits: OrganizationalUnit[];
    employees: Employee[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employers', href: index() }],
    },
});

const showContractDialog = ref(false);
const showUnitDialog = ref(false);
const showEmployeeDialog = ref(false);
</script>

<template>
    <Head :title="employer.name" />

    <div class="flex flex-col gap-8 p-4">
        <Heading :title="employer.name" :description="employer.kvk_number ?? undefined" />

        <section class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border p-4">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Contracts</h2>
                    <Button variant="ghost" size="icon" aria-label="Add contract" @click="showContractDialog = true">
                        <Plus class="size-4" />
                    </Button>
                </div>

                <ul class="space-y-2">
                    <li v-for="contract in contracts" :key="contract.id" class="text-sm">
                        {{ contract.contract_type_label }} — {{ contract.start_date }}
                        <span v-if="contract.end_date"> to {{ contract.end_date }}</span>
                        ({{ contract.status }})
                    </li>
                    <li v-if="contracts.length === 0" class="text-sm text-muted-foreground">
                        No contracts yet.
                    </li>
                </ul>
            </div>

            <div class="rounded-lg border p-4">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Organizational units</h2>
                    <Button variant="ghost" size="icon" aria-label="Add unit" @click="showUnitDialog = true">
                        <Plus class="size-4" />
                    </Button>
                </div>

                <OrganizationalUnitTree :units="organizationalUnits" :employer-id="employer.id" />
            </div>

            <div class="rounded-lg border p-4 md:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Employees</h2>
                    <Button variant="ghost" size="icon" aria-label="Add employee" @click="showEmployeeDialog = true">
                        <Plus class="size-4" />
                    </Button>
                </div>

                <ul class="space-y-2">
                    <li v-for="employee in employees" :key="employee.id" class="text-sm">
                        {{ employee.first_name }} {{ employee.last_name }}
                        <span class="text-muted-foreground">
                            ({{ employee.organizational_unit?.name }}, {{ employee.source }})
                        </span>
                    </li>
                    <li v-if="employees.length === 0" class="text-sm text-muted-foreground">
                        No employees yet.
                    </li>
                </ul>
            </div>
        </section>
    </div>

    <Dialog v-model:open="showContractDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add contract</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="storeContract.form({ employer: employer.id })"
                v-slot="{ errors, processing }"
                class="space-y-3"
            >
                <div class="grid gap-2">
                    <Label for="contract_type_id">Contract type</Label>
                    <select
                        id="contract_type_id"
                        name="contract_type_id"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option v-if="contractTypes.length === 0" value="" disabled>
                            No contract types yet — add one in Admin
                        </option>
                        <option v-for="contractType in contractTypes" :key="contractType.id" :value="contractType.id">
                            {{ contractType.name }}
                        </option>
                    </select>
                    <InputError :message="errors.contract_type_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="start_date">Start date</Label>
                    <Input id="start_date" type="date" name="start_date" required />
                    <InputError :message="errors.start_date" />
                </div>
                <div class="grid gap-2">
                    <Label for="end_date">End date</Label>
                    <Input id="end_date" type="date" name="end_date" />
                    <InputError :message="errors.end_date" />
                </div>
                <Button type="submit" :disabled="processing">Add contract</Button>
            </Form>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="showUnitDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add organizational unit</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="storeOrganizationalUnit.form({ employer: employer.id })"
                v-slot="{ errors, processing }"
                :reset-on-success="['name', 'kvk_number']"
                class="space-y-3"
            >
                <div class="grid gap-2">
                    <Label for="unit_name">Name</Label>
                    <Input id="unit_name" name="name" required placeholder="e.g. Logistics department" />
                    <InputError :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="parent_id">Parent unit</Label>
                    <select
                        id="parent_id"
                        name="parent_id"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option value="">None (top level)</option>
                        <option v-for="unit in organizationalUnits" :key="unit.id" :value="unit.id">
                            {{ unit.name }}
                        </option>
                    </select>
                    <InputError :message="errors.parent_id" />
                </div>
                <Label class="flex items-center gap-2">
                    <Checkbox name="is_legal_entity" value="1" />
                    Is a separate legal entity
                </Label>
                <div class="grid gap-2">
                    <Label for="unit_kvk_number">KvK number</Label>
                    <Input id="unit_kvk_number" name="kvk_number" placeholder="Optional" />
                    <InputError :message="errors.kvk_number" />
                </div>
                <Button type="submit" :disabled="processing">Add unit</Button>
            </Form>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="showEmployeeDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add employee</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="storeEmployee.form({ employer: employer.id })"
                v-slot="{ errors, processing }"
                :reset-on-success="['first_name', 'last_name', 'email', 'employee_number']"
                class="space-y-3"
            >
                <div class="grid gap-2">
                    <Label for="first_name">First name</Label>
                    <Input id="first_name" name="first_name" required />
                    <InputError :message="errors.first_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="last_name">Last name</Label>
                    <Input id="last_name" name="last_name" required />
                    <InputError :message="errors.last_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" name="email" />
                    <InputError :message="errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="organizational_unit_id">Organizational unit</Label>
                    <select
                        id="organizational_unit_id"
                        name="organizational_unit_id"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option v-for="unit in organizationalUnits" :key="unit.id" :value="unit.id">
                            {{ unit.name }}
                        </option>
                    </select>
                    <InputError :message="errors.organizational_unit_id" />
                </div>
                <Button type="submit" :disabled="processing">Add employee</Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
