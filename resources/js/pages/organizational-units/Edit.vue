<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/employers';
import { update } from '@/routes/organizational-units';

type Employer = { id: string; name: string };
type OrganizationalUnit = {
    id: string;
    parent_id: string | null;
    name: string;
    is_legal_entity: boolean;
    kvk_number: string | null;
};

defineProps<{
    employer: Employer;
    organizationalUnit: OrganizationalUnit;
    availableParents: OrganizationalUnit[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employers', href: index() }],
    },
});
</script>

<template>
    <Head :title="`Edit ${organizationalUnit.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="`Edit ${organizationalUnit.name}`"
            :description="employer.name"
        />

        <Form
            v-bind="
                update.form({
                    employer: employer.id,
                    organizationalUnit: organizationalUnit.id,
                })
            "
            v-slot="{ errors, processing }"
            class="max-w-md space-y-4"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    :default-value="organizationalUnit.name"
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
                        v-for="unit in availableParents"
                        :key="unit.id"
                        :value="unit.id"
                        :selected="unit.id === organizationalUnit.parent_id"
                    >
                        {{ unit.name }}
                    </option>
                </select>
                <InputError :message="errors.parent_id" />
            </div>

            <Label class="flex items-center gap-2">
                <Checkbox
                    name="is_legal_entity"
                    value="1"
                    :model-value="organizationalUnit.is_legal_entity"
                />
                Is a separate legal entity
            </Label>

            <div class="grid gap-2">
                <Label for="kvk_number">KvK number</Label>
                <Input
                    id="kvk_number"
                    name="kvk_number"
                    placeholder="Optional"
                    :default-value="organizationalUnit.kvk_number ?? undefined"
                />
                <InputError :message="errors.kvk_number" />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="processing">Save</Button>
            </div>
        </Form>
    </div>
</template>
