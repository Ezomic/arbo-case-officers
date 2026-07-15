<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/employees';
import { index as employersIndex } from '@/routes/employers';

type Employer = { id: string; name: string };
type OrganizationalUnit = { id: string; name: string };
type Address = {
    address_line_1: string;
    address_line_2: string | null;
    postal_code: string;
    city: string;
    country: string;
} | null;
type Employee = {
    id: string;
    first_name: string;
    last_name: string;
    email: string | null;
    employee_number: string | null;
    date_of_birth: string | null;
    gender: string | null;
    bsn: string | null;
    nationality: string | null;
    organizational_unit_id: string | null;
    address: Address;
};

defineProps<{
    employer: Employer;
    employee: Employee;
    organizationalUnits: OrganizationalUnit[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employers', href: employersIndex() }],
    },
});
</script>

<template>
    <Head :title="`Edit ${employee.first_name} ${employee.last_name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="`Edit ${employee.first_name} ${employee.last_name}`"
            :description="employer.name"
        />

        <Form
            v-bind="
                update.form({ employer: employer.id, employee: employee.id })
            "
            v-slot="{ errors, processing }"
            class="max-w-md space-y-4"
        >
            <div class="grid gap-2">
                <Label for="first_name">First name</Label>
                <Input
                    id="first_name"
                    name="first_name"
                    required
                    :default-value="employee.first_name"
                />
                <InputError :message="errors.first_name" />
            </div>

            <div class="grid gap-2">
                <Label for="last_name">Last name</Label>
                <Input
                    id="last_name"
                    name="last_name"
                    required
                    :default-value="employee.last_name"
                />
                <InputError :message="errors.last_name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    :default-value="employee.email ?? undefined"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="employee_number">Employee number</Label>
                <Input
                    id="employee_number"
                    name="employee_number"
                    :default-value="employee.employee_number ?? undefined"
                />
                <InputError :message="errors.employee_number" />
            </div>

            <div class="grid gap-2">
                <Label for="date_of_birth">Date of birth</Label>
                <Input
                    id="date_of_birth"
                    type="date"
                    name="date_of_birth"
                    :default-value="employee.date_of_birth ?? undefined"
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
                    <option value="" :selected="!employee.gender">
                        Unspecified
                    </option>
                    <option value="male" :selected="employee.gender === 'male'">
                        Male
                    </option>
                    <option
                        value="female"
                        :selected="employee.gender === 'female'"
                    >
                        Female
                    </option>
                    <option
                        value="other"
                        :selected="employee.gender === 'other'"
                    >
                        Other
                    </option>
                </select>
                <InputError :message="errors.gender" />
            </div>

            <div class="grid gap-2">
                <Label for="bsn">BSN</Label>
                <Input
                    id="bsn"
                    name="bsn"
                    maxlength="9"
                    :default-value="employee.bsn ?? undefined"
                />
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
                    :default-value="employee.nationality ?? undefined"
                />
                <InputError :message="errors.nationality" />
            </div>

            <div class="grid gap-2">
                <Label for="address_line_1">Address line 1</Label>
                <Input
                    id="address_line_1"
                    name="address_line_1"
                    :default-value="employee.address?.address_line_1"
                />
                <InputError :message="errors.address_line_1" />
            </div>

            <div class="grid gap-2">
                <Label for="address_line_2">Address line 2</Label>
                <Input
                    id="address_line_2"
                    name="address_line_2"
                    :default-value="
                        employee.address?.address_line_2 ?? undefined
                    "
                />
                <InputError :message="errors.address_line_2" />
            </div>

            <div class="grid gap-2">
                <Label for="postal_code">Postal code</Label>
                <Input
                    id="postal_code"
                    name="postal_code"
                    :default-value="employee.address?.postal_code"
                />
                <InputError :message="errors.postal_code" />
            </div>

            <div class="grid gap-2">
                <Label for="city">City</Label>
                <Input
                    id="city"
                    name="city"
                    :default-value="employee.address?.city"
                />
                <InputError :message="errors.city" />
            </div>

            <div class="grid gap-2">
                <Label for="country">Country (ISO 3166-1 alpha-2)</Label>
                <Input
                    id="country"
                    name="country"
                    maxlength="2"
                    placeholder="NL"
                    :default-value="employee.address?.country ?? 'NL'"
                />
                <InputError :message="errors.country" />
            </div>

            <div class="grid gap-2">
                <Label for="organizational_unit_id">Organizational unit</Label>
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
                        :selected="unit.id === employee.organizational_unit_id"
                    >
                        {{ unit.name }}
                    </option>
                </select>
                <InputError :message="errors.organizational_unit_id" />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="processing">Save</Button>
            </div>
        </Form>
    </div>
</template>
