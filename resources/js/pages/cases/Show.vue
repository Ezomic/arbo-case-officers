<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { show as showEmployer } from '@/routes/employers';
import { close, index, update } from '@/routes/cases';
import { formatDateTime } from '@/lib/date';

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

type CaseNote = {
    id: string;
    note_type_id: string;
    note_type_name: string;
    body: string;
    author_name: string;
    is_mine: boolean;
    can_update: boolean;
    can_delete: boolean;
    created_at: string;
};

type NoteType = { id: string; name: string };

const props = defineProps<{
    case: CaseFile;
    notes: CaseNote[];
    writableNoteTypes: NoteType[];
}>();

const editingNote = ref<CaseNote | null>(null);

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
                        <dd class="font-medium">{{ formatDateTime(props.case.opened_at) }}</dd>
                    </div>
                    <div v-if="props.case.closed_at">
                        <dt class="text-muted-foreground">Closed</dt>
                        <dd class="font-medium">{{ formatDateTime(props.case.closed_at) }}</dd>
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

        <!-- Absence actions (only for open cases) -->
        <div v-if="props.case.status === 'open'" class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border p-4">
                <h2 class="mb-1 font-medium">Report mutation</h2>
                <p class="mb-4 text-xs text-muted-foreground">Update the expected return date when the situation changes.</p>
                <Form
                    v-bind="update.form({ case: props.case.id })"
                    v-slot="{ errors, processing }"
                    class="space-y-3"
                >
                    <div class="grid gap-2">
                        <Label for="expected_return_date">Expected return date</Label>
                        <Input
                            id="expected_return_date"
                            type="date"
                            name="expected_return_date"
                            :default-value="props.case.expected_return_date ?? undefined"
                        />
                        <InputError :message="errors.expected_return_date" />
                    </div>
                    <Button type="submit" :disabled="processing">Save mutation</Button>
                </Form>
            </div>

            <div class="rounded-lg border p-4">
                <h2 class="mb-1 font-medium">Report recovery</h2>
                <p class="mb-4 text-xs text-muted-foreground">Register the date the employee returned to work and close this case.</p>
                <Form
                    v-bind="close.form({ case: props.case.id })"
                    v-slot="{ errors, processing }"
                    class="space-y-3"
                >
                    <div class="grid gap-2">
                        <Label for="recovery_date">Recovery date</Label>
                        <Input id="recovery_date" type="date" name="recovery_date" required />
                        <InputError :message="errors.recovery_date" />
                    </div>
                    <Button type="submit" variant="destructive" :disabled="processing">Close case</Button>
                </Form>
            </div>
        </div>

        <!-- Notes -->
        <div class="rounded-lg border p-4">
            <h2 class="mb-4 font-medium">Notes</h2>

            <ul v-if="notes.length > 0" class="mb-4 space-y-3">
                <li v-for="note in notes" :key="note.id" class="rounded-md border p-3 text-sm">
                    <div class="mb-1 flex items-center justify-between gap-2">
                        <span class="font-medium">{{ note.note_type_name }}</span>
                        <div class="flex shrink-0 items-center gap-1 text-xs text-muted-foreground">
                            <span>{{ note.author_name }} · {{ formatDateTime(note.created_at) }}</span>
                            <Button v-if="note.can_update" variant="ghost" size="icon" class="size-6" @click="editingNote = note">
                                <Pencil class="size-3" />
                            </Button>
                            <Form v-if="note.can_delete" :action="`/cases/${props.case.id}/notes/${note.id}`" method="delete" class="inline">
                                <Button type="submit" variant="ghost" size="icon" class="size-6">
                                    <Trash2 class="size-3" />
                                </Button>
                            </Form>
                        </div>
                    </div>
                    <p class="whitespace-pre-wrap text-muted-foreground">{{ note.body }}</p>
                </li>
            </ul>
            <p v-else class="mb-4 text-sm text-muted-foreground">No notes yet.</p>

            <Form
                v-if="writableNoteTypes.length > 0"
                :action="`/cases/${props.case.id}/notes`"
                method="post"
                v-slot="{ errors, processing }"
                :reset-on-success="['body']"
                class="space-y-3"
            >
                <div class="grid gap-2">
                    <Label for="note_type_id">Note type</Label>
                    <select
                        id="note_type_id"
                        name="note_type_id"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option v-for="nt in writableNoteTypes" :key="nt.id" :value="nt.id">{{ nt.name }}</option>
                    </select>
                    <InputError :message="errors.note_type_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="note_body">Note</Label>
                    <Textarea id="note_body" name="body" required placeholder="Write a note…" />
                    <InputError :message="errors.body" />
                </div>
                <Button type="submit" :disabled="processing">Add note</Button>
            </Form>
        </div>
    </div>  <!-- end flex flex-col gap-6 -->

    <!-- Edit note dialog -->
    <template v-if="editingNote">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="editingNote = null">
            <div class="w-full max-w-md rounded-lg border bg-background p-6 shadow-lg">
                <h3 class="mb-4 font-medium">Edit note — {{ editingNote.note_type_name }}</h3>
                <Form
                    :action="`/cases/${props.case.id}/notes/${editingNote.id}`"
                    method="put"
                    v-slot="{ errors, processing }"
                    class="space-y-3"
                >
                    <div class="grid gap-2">
                        <Label for="edit_note_body">Note</Label>
                        <Textarea id="edit_note_body" name="body" required :default-value="editingNote.body" />
                        <InputError :message="errors.body" />
                    </div>
                    <div class="flex gap-2">
                        <Button type="submit" :disabled="processing">Save</Button>
                        <Button type="button" variant="outline" @click="editingNote = null">Cancel</Button>
                    </div>
                </Form>
            </div>
        </div>
    </template>
</template>
