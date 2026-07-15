<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import OrganizationalUnitTree from '@/components/OrganizationalUnitTree.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatDate } from '@/lib/date';
import {
    destroy as destroyContactPerson,
    store as storeContactPerson,
} from '@/routes/contact-persons';
import { store as storeContract } from '@/routes/contracts';
import {
    edit as editEmployee,
    store as storeEmployee,
} from '@/routes/employees';
import { index } from '@/routes/employers';
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

type ContactPerson = {
    id: string;
    name: string;
    email: string | null;
    phone: string | null;
    job_title: string | null;
};

defineProps<{
    employer: Employer;
    contracts: Contract[];
    contractTypes: ContractType[];
    organizationalUnits: OrganizationalUnit[];
    employees: Employee[];
    contactPersons: ContactPerson[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employers', href: index() }],
    },
});

const showContractDialog = ref(false);
const showUnitDialog = ref(false);
const showEmployeeDialog = ref(false);
const showContactDialog = ref(false);
</script>

<template>
    <Head :title="employer.name" />

    <div class="flex flex-col gap-8 p-4">
        <Heading
            :title="employer.name"
            :description="employer.kvk_number ?? undefined"
        />

        <section class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border p-4">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Contracts</h2>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label="Add contract"
                        @click="showContractDialog = true"
                    >
                        <Plus class="size-4" />
                    </Button>
                </div>

                <ul class="space-y-2">
                    <li
                        v-for="contract in contracts"
                        :key="contract.id"
                        class="text-sm"
                    >
                        {{ contract.contract_type_label }} —
                        {{ formatDate(contract.start_date) }}
                        <span v-if="contract.end_date">
                            to {{ formatDate(contract.end_date) }}</span
                        >
                        ({{ contract.status }})
                    </li>
                    <li
                        v-if="contracts.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No contracts yet.
                    </li>
                </ul>
            </div>

            <div class="rounded-lg border p-4">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Organizational units</h2>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label="Add unit"
                        @click="showUnitDialog = true"
                    >
                        <Plus class="size-4" />
                    </Button>
                </div>

                <OrganizationalUnitTree
                    :units="organizationalUnits"
                    :employer-id="employer.id"
                />
            </div>

            <div class="rounded-lg border p-4 md:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Contact persons</h2>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label="Add contact person"
                        @click="showContactDialog = true"
                    >
                        <Plus class="size-4" />
                    </Button>
                </div>

                <ul class="space-y-2">
                    <li
                        v-for="cp in contactPersons"
                        :key="cp.id"
                        class="flex items-center justify-between text-sm"
                    >
                        <div>
                            <span class="font-medium">{{ cp.name }}</span>
                            <span
                                v-if="cp.job_title"
                                class="ml-2 text-muted-foreground"
                                >{{ cp.job_title }}</span
                            >
                            <span
                                v-if="cp.email"
                                class="ml-2 text-muted-foreground"
                                >· {{ cp.email }}</span
                            >
                            <span
                                v-if="cp.phone"
                                class="ml-2 text-muted-foreground"
                                >· {{ cp.phone }}</span
                            >
                        </div>
                        <Form
                            v-bind="
                                destroyContactPerson.form({
                                    employer: employer.id,
                                    contactPerson: cp.id,
                                })
                            "
                            class="ml-2"
                        >
                            <Button
                                type="submit"
                                variant="ghost"
                                size="icon"
                                aria-label="Delete"
                            >
                                <Trash2 class="size-4 text-destructive" />
                            </Button>
                        </Form>
                    </li>
                    <li
                        v-if="contactPersons.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No contact persons yet.
                    </li>
                </ul>
            </div>

            <div class="rounded-lg border p-4 md:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium">Employees</h2>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label="Add employee"
                        @click="showEmployeeDialog = true"
                    >
                        <Plus class="size-4" />
                    </Button>
                </div>

                <ul class="space-y-2">
                    <li
                        v-for="employee in employees"
                        :key="employee.id"
                        class="flex items-center justify-between gap-2 text-sm"
                    >
                        <span>
                            {{ employee.first_name }} {{ employee.last_name }}
                            <span class="text-muted-foreground">
                                ({{ employee.organizational_unit?.name }},
                                {{ employee.source }})
                            </span>
                        </span>
                        <Button
                            variant="ghost"
                            size="icon"
                            as-child
                            class="shrink-0 text-muted-foreground"
                        >
                            <Link
                                :href="
                                    editEmployee({
                                        employer: employer.id,
                                        employee: employee.id,
                                    })
                                "
                                title="Edit employee"
                                aria-label="Edit employee"
                            >
                                <Pencil class="size-4" />
                            </Link>
                        </Button>
                    </li>
                    <li
                        v-if="employees.length === 0"
                        class="text-sm text-muted-foreground"
                    >
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
                        <option
                            v-if="contractTypes.length === 0"
                            value=""
                            disabled
                        >
                            No contract types yet — add one in Admin
                        </option>
                        <option
                            v-for="contractType in contractTypes"
                            :key="contractType.id"
                            :value="contractType.id"
                        >
                            {{ contractType.name }}
                        </option>
                    </select>
                    <InputError :message="errors.contract_type_id" />
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
                <div class="grid gap-2">
                    <Label for="end_date">End date</Label>
                    <Input id="end_date" type="date" name="end_date" />
                    <InputError :message="errors.end_date" />
                </div>
                <Button type="submit" :disabled="processing"
                    >Add contract</Button
                >
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
                    <Input
                        id="unit_name"
                        name="name"
                        required
                        placeholder="e.g. Logistics department"
                    />
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
                        <option
                            v-for="unit in organizationalUnits"
                            :key="unit.id"
                            :value="unit.id"
                        >
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
                    <Input
                        id="unit_kvk_number"
                        name="kvk_number"
                        placeholder="Optional"
                    />
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
                :reset-on-success="[
                    'first_name',
                    'last_name',
                    'email',
                    'employee_number',
                    'date_of_birth',
                    'gender',
                    'bsn',
                    'nationality',
                    'address_line_1',
                    'address_line_2',
                    'postal_code',
                    'city',
                    'country',
                ]"
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
                    <Label for="date_of_birth">Date of birth</Label>
                    <Input
                        id="date_of_birth"
                        type="date"
                        name="date_of_birth"
                    />
                    <InputError :message="errors.date_of_birth" />
                </div>
                <div class="grid gap-2">
                    <Label for="gender">Gender</Label>
                    <select
                        id="gender"
                        name="gender"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option value="">Unspecified</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <InputError :message="errors.gender" />
                </div>
                <div class="grid gap-2">
                    <Label for="bsn">BSN</Label>
                    <Input id="bsn" name="bsn" maxlength="9" />
                    <InputError :message="errors.bsn" />
                </div>
                <div class="grid gap-2">
                    <Label for="nationality"
                        >Nationality (ISO 3166-1 alpha-3)</Label
                    >
                    <Input
                        id="nationality"
                        name="nationality"
                        maxlength="3"
                        placeholder="NLD"
                    />
                    <InputError :message="errors.nationality" />
                </div>
                <div class="grid gap-2">
                    <Label for="address_line_1">Address line 1</Label>
                    <Input id="address_line_1" name="address_line_1" />
                    <InputError :message="errors.address_line_1" />
                </div>
                <div class="grid gap-2">
                    <Label for="address_line_2">Address line 2</Label>
                    <Input id="address_line_2" name="address_line_2" />
                    <InputError :message="errors.address_line_2" />
                </div>
                <div class="grid gap-2">
                    <Label for="postal_code">Postal code</Label>
                    <Input id="postal_code" name="postal_code" />
                    <InputError :message="errors.postal_code" />
                </div>
                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input id="city" name="city" />
                    <InputError :message="errors.city" />
                </div>
                <div class="grid gap-2">
                    <Label for="country">Country (ISO 3166-1 alpha-2)</Label>
                    <Input
                        id="country"
                        name="country"
                        maxlength="2"
                        placeholder="NL"
                    />
                    <InputError :message="errors.country" />
                </div>
                <div class="grid gap-2">
                    <Label for="organizational_unit_id"
                        >Organizational unit</Label
                    >
                    <select
                        id="organizational_unit_id"
                        name="organizational_unit_id"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option
                            v-for="unit in organizationalUnits"
                            :key="unit.id"
                            :value="unit.id"
                        >
                            {{ unit.name }}
                        </option>
                    </select>
                    <InputError :message="errors.organizational_unit_id" />
                </div>
                <Button type="submit" :disabled="processing"
                    >Add employee</Button
                >
            </Form>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="showContactDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add contact person</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="storeContactPerson.form({ employer: employer.id })"
                v-slot="{ errors, processing }"
                :reset-on-success="['name', 'email', 'phone', 'job_title']"
                class="space-y-3"
            >
                <div class="grid gap-2">
                    <Label for="cp_name">Name</Label>
                    <Input id="cp_name" name="name" required />
                    <InputError :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="cp_job_title">Job title</Label>
                    <Input id="cp_job_title" name="job_title" />
                    <InputError :message="errors.job_title" />
                </div>
                <div class="grid gap-2">
                    <Label for="cp_email">Email</Label>
                    <Input id="cp_email" type="email" name="email" />
                    <InputError :message="errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="cp_phone">Phone</Label>
                    <Input id="cp_phone" name="phone" />
                    <InputError :message="errors.phone" />
                </div>
                <Button type="submit" :disabled="processing"
                    >Add contact person</Button
                >
            </Form>
        </DialogContent>
    </Dialog>
</template>
