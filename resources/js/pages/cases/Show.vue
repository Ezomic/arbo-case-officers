<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { show as showEmployer } from '@/routes/employers';
import { index } from '@/routes/cases';

type CaseFile = {
    id: string;
    case_type: string | null;
    status: string;
    opened_at: string;
    closed_at: string | null;
    advice: string | null;
    restrictions: string | null;
    expected_return_date: string | null;
    employer: { id: string; name: string };
    employee: { id: string; first_name: string; last_name: string };
};

const props = defineProps<{
    case: CaseFile;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Cases', href: index() },
        ],
    },
});
</script>

<template>
    <Head :title="`${props.case.employee.first_name} ${props.case.employee.last_name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="`${props.case.employee.first_name} ${props.case.employee.last_name}`"
            :description="props.case.case_type ?? undefined"
        />

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">Case</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-muted-foreground">Employer</dt>
                        <dd>
                            <Link :href="showEmployer(props.case.employer.id)" class="font-medium underline underline-offset-4">
                                {{ props.case.employer.name }}
                            </Link>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="font-medium">{{ props.case.status }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Opened</dt>
                        <dd class="font-medium">{{ props.case.opened_at }}</dd>
                    </div>
                    <div v-if="props.case.closed_at">
                        <dt class="text-muted-foreground">Closed</dt>
                        <dd class="font-medium">{{ props.case.closed_at }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-lg border p-4">
                <h2 class="mb-4 font-medium">From the doctor</h2>
                <p v-if="!props.case.advice && !props.case.restrictions && !props.case.expected_return_date" class="text-sm text-muted-foreground">
                    No medical outcomes have been shared yet.
                </p>
                <dl v-else class="space-y-3 text-sm">
                    <div v-if="props.case.expected_return_date">
                        <dt class="text-muted-foreground">Expected return date</dt>
                        <dd class="font-medium">{{ props.case.expected_return_date }}</dd>
                    </div>
                    <div v-if="props.case.restrictions">
                        <dt class="text-muted-foreground">Restrictions</dt>
                        <dd class="font-medium">{{ props.case.restrictions }}</dd>
                    </div>
                    <div v-if="props.case.advice">
                        <dt class="text-muted-foreground">Advice</dt>
                        <dd class="font-medium">{{ props.case.advice }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</template>
