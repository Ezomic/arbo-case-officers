<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Building2, ChevronRight, Plus, Search } from '@lucide/vue';
import { computed, ref } from 'vue';
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
import { index, show, store } from '@/routes/employers';

type Employer = {
    id: string;
    name: string;
    kvk_number: string | null;
    status: string;
};

const props = defineProps<{
    employers: Employer[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Employers', href: index() }],
    },
});

const showDialog = ref(false);
const query = ref('');

const filteredEmployers = computed(() => {
    const q = query.value.trim().toLowerCase();

    if (!q) {
        return props.employers;
    }

    return props.employers.filter((employer) =>
        `${employer.name} ${employer.kvk_number ?? ''}`
            .toLowerCase()
            .includes(q),
    );
});
</script>

<template>
    <Head title="Employers" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Employers"
                :description="`${employers.length} client ${employers.length === 1 ? 'company' : 'companies'} your tenant has a contract with`"
            />
            <Button @click="showDialog = true">
                <Plus class="size-4" />
                Add employer
            </Button>
        </div>

        <div v-if="employers.length > 0" class="relative w-full sm:max-w-xs">
            <Search
                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="query"
                type="search"
                placeholder="Search name or KvK number…"
                class="pl-9"
            />
        </div>

        <div v-if="filteredEmployers.length > 0" class="flex flex-col gap-2">
            <Link
                v-for="employer in filteredEmployers"
                :key="employer.id"
                :href="show(employer.id)"
                class="group flex items-center gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
            >
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-md bg-muted"
                >
                    <Building2 class="size-4 text-muted-foreground" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-medium">{{ employer.name }}</span>
                        <Badge
                            v-if="employer.status !== 'active'"
                            variant="outline"
                            class="capitalize"
                        >
                            {{ employer.status }}
                        </Badge>
                    </div>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{
                            employer.kvk_number
                                ? `KvK ${employer.kvk_number}`
                                : 'No KvK number'
                        }}
                    </p>
                </div>
                <ChevronRight
                    class="size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5"
                />
            </Link>
        </div>

        <div
            v-else-if="employers.length > 0"
            class="flex flex-col items-center gap-2 rounded-lg border border-dashed py-12 text-center"
        >
            <Search class="size-8 text-muted-foreground" />
            <p class="font-medium">No matching employers</p>
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
                <Building2 class="size-6 text-muted-foreground" />
            </div>
            <div>
                <p class="font-medium">No employers yet</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Add your first client company to get started.
                </p>
            </div>
            <Button variant="outline" @click="showDialog = true">
                <Plus class="size-4" />
                Add employer
            </Button>
        </div>
    </div>

    <Dialog v-model:open="showDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add employer</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="store.form()"
                v-slot="{ errors, processing }"
                class="space-y-4"
                @success="showDialog = false"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        name="name"
                        required
                        placeholder="Client company name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="kvk_number">KvK number</Label>
                    <Input
                        id="kvk_number"
                        name="kvk_number"
                        placeholder="Optional"
                    />
                    <InputError :message="errors.kvk_number" />
                </div>

                <Button type="submit" :disabled="processing">
                    Add employer
                </Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
