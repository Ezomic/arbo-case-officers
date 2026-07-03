<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, show, store } from '@/routes/employers';

type Employer = {
    id: string;
    name: string;
    kvk_number: string | null;
    status: string;
};

defineProps<{
    employers: Employer[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employers', href: index() }],
    },
});
</script>

<template>
    <Head title="Employers" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Employers"
            description="Client companies your tenant has a contract with"
        />

        <div class="flex flex-col gap-2">
            <Link
                v-for="employer in employers"
                :key="employer.id"
                :href="show(employer.id)"
                class="rounded-lg border p-4 hover:bg-muted"
            >
                <div class="font-medium">{{ employer.name }}</div>
                <div class="text-sm text-muted-foreground">
                    {{ employer.kvk_number ?? 'No KvK number' }} ·
                    {{ employer.status }}
                </div>
            </Link>
            <p v-if="employers.length === 0" class="text-sm text-muted-foreground">
                No employers yet — add one below.
            </p>
        </div>

        <div class="max-w-md rounded-lg border p-4">
            <h2 class="mb-4 font-medium">Add employer</h2>

            <Form v-bind="store.form()" v-slot="{ errors, processing }" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" name="name" required placeholder="Client company name" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="kvk_number">KvK number</Label>
                    <Input id="kvk_number" name="kvk_number" placeholder="Optional" />
                    <InputError :message="errors.kvk_number" />
                </div>

                <Button type="submit" :disabled="processing">Add employer</Button>
            </Form>
        </div>
    </div>
</template>
