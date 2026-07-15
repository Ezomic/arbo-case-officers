<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    CalendarCheck,
    Check,
    Pencil,
    TriangleAlert,
    Trash2,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import CaseStatusBadge from '@/components/CaseStatusBadge.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { formatDate } from '@/lib/date';
import {
    store as storeNote,
    update as updateNote,
    destroy as destroyNote,
} from '@/routes/case-notes';
import {
    store as storeTask,
    complete as completeTask,
    destroy as destroyTask,
} from '@/routes/case-tasks';
import { close as closeCase, index } from '@/routes/cases';
import { update as updateAssignment } from '@/routes/cases/assignment';
import { show as showEmployer } from '@/routes/employers';

type CaseFile = {
    id: string;
    case_type: string | null;
    status: string;
    case_officer_user_id: string | null;
    opened_at: string;
    closed_at: string | null;
    advice: string | null;
    restrictions: string | null;
    expected_return_date: string | null;
    employer: { id: string; name: string };
    employee: { id: string; first_name: string; last_name: string };
    assigned_officer: { id: string; name: string } | null;
};

type Note = {
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

type Task = {
    id: string;
    task_type_name: string | null;
    title: string;
    description: string | null;
    due_date: string | null;
    completed_at: string | null;
    assigned_to: string | null;
};

type TaskType = { id: string; name: string };

type CaseOfficer = { id: string; name: string };

type Milestone = {
    milestone: string;
    label: string;
    due_date: string;
    status: 'overdue' | 'due_soon' | 'upcoming';
};

type TimelineEvent = {
    id: number;
    event: string;
    payload: Record<string, string> | null;
    actor_name: string | null;
    occurred_at: string;
};

const props = defineProps<{
    case: CaseFile;
    case_type_label: string | null;
    notes: Note[];
    writableNoteTypes: NoteType[];
    timeline: TimelineEvent[];
    milestones: Milestone[];
    tasks: Task[];
    taskTypes: TaskType[];
    caseOfficers: CaseOfficer[];
    can: { manage_cases: boolean; close_cases: boolean };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Cases', href: index() }],
    },
});

const editingNoteId = ref<string | null>(null);
const showTaskForm = ref(false);

const noteForm = useForm({
    note_type_id: props.writableNoteTypes[0]?.id ?? '',
    body: '',
});

const editForm = useForm({ body: '' });

const taskForm = useForm({
    task_type_id: '',
    title: '',
    description: '',
    due_date: '',
});

const assignForm = useForm({
    case_officer_user_id: props.case.case_officer_user_id ?? '',
});

function submitAssignment() {
    assignForm.put(updateAssignment(props.case.id).url, {
        preserveScroll: true,
    });
}

function submitNote() {
    noteForm.post(storeNote(props.case.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            noteForm.reset('body');
        },
    });
}

function startEdit(note: Note) {
    editingNoteId.value = note.id;
    editForm.body = note.body;
}

function submitEdit(note: Note) {
    editForm.put(updateNote({ case: props.case.id, note: note.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            editingNoteId.value = null;
        },
    });
}

type PendingDelete =
    { kind: 'note'; note: Note } | { kind: 'task'; task: Task } | null;

const pendingDelete = ref<PendingDelete>(null);

const pendingDeleteLabel = computed(() => {
    if (pendingDelete.value?.kind === 'note') {
        return `the ${pendingDelete.value.note.note_type_name} note by ${pendingDelete.value.note.author_name}`;
    }

    if (pendingDelete.value?.kind === 'task') {
        return `the task “${pendingDelete.value.task.title}”`;
    }

    return '';
});

function confirmDelete() {
    const pending = pendingDelete.value;

    if (!pending) {
        return;
    }

    const url =
        pending.kind === 'note'
            ? destroyNote({ case: props.case.id, note: pending.note.id }).url
            : destroyTask({ case: props.case.id, task: pending.task.id }).url;

    useForm({}).delete(url, {
        preserveScroll: true,
        onFinish: () => {
            pendingDelete.value = null;
        },
    });
}

function submitTask() {
    taskForm.post(storeTask(props.case.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            taskForm.reset();
            showTaskForm.value = false;
        },
    });
}

function markComplete(task: Task) {
    useForm({}).post(completeTask({ case: props.case.id, task: task.id }).url, {
        preserveScroll: true,
    });
}

const showCloseDialog = ref(false);
const today = new Date().toISOString().slice(0, 10);
const closeForm = useForm({ recovery_date: today });

function submitClose() {
    closeForm.post(closeCase(props.case.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            showCloseDialog.value = false;
        },
    });
}

const isReturnDateOverdue = computed(
    () =>
        props.case.status === 'open' &&
        props.case.expected_return_date !== null &&
        props.case.expected_return_date < today,
);

const eventLabels: Record<string, string> = {
    case_opened: 'Case opened',
    case_closed: 'Case closed',
    return_date_set: 'Return date set',
    outcome_shared: 'Medical outcome shared',
    note_added: 'Note added',
    case_assigned: 'Case assigned',
    case_handed_over: 'Case handed over',
};

function eventLabel(event: string): string {
    return eventLabels[event] ?? event;
}

const payloadLabels: Record<string, string> = {
    opened_at: 'Opened',
    closed_at: 'Closed',
    return_date: 'Return date',
    expected_return_date: 'Expected return date',
    advice: 'Advice',
    restrictions: 'Restrictions',
    note_type: 'Note type',
    assigned_to: 'Assigned to',
    from: 'From',
    to: 'To',
    case_type: 'Case type',
};

function payloadLabel(key: string): string {
    return payloadLabels[key] ?? key.replaceAll('_', ' ');
}

function payloadValue(value: string): string {
    return /^\d{4}-\d{2}-\d{2}$/.test(value) ? formatDate(value) : value;
}

const milestoneStatusLabels: Record<Milestone['status'], string> = {
    overdue: 'Overdue',
    due_soon: 'Due soon',
    upcoming: 'Upcoming',
};

const milestoneStatusClasses: Record<Milestone['status'], string> = {
    overdue: 'border-transparent bg-destructive/10 text-destructive',
    due_soon:
        'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-400',
    upcoming: 'border-transparent bg-muted text-muted-foreground',
};
</script>

<template>
    <Head
        :title="`${props.case.employee.first_name} ${props.case.employee.last_name}`"
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <Heading
                    :title="`${props.case.employee.first_name} ${props.case.employee.last_name}`"
                    :description="props.case_type_label ?? undefined"
                />
                <CaseStatusBadge class="mt-1" :status="props.case.status" />
                <Badge
                    v-if="isReturnDateOverdue"
                    variant="outline"
                    class="mt-1 border-transparent bg-destructive/10 text-destructive"
                >
                    <TriangleAlert />
                    Return date overdue
                </Badge>
            </div>
            <Button
                v-if="props.case.status === 'open' && props.can.close_cases"
                variant="outline"
                @click="showCloseDialog = true"
            >
                <CalendarCheck class="size-4" />
                Report recovered
            </Button>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left column: case info + doctor outcomes -->
            <div class="flex flex-col gap-4 lg:col-span-1">
                <div class="rounded-lg border p-4">
                    <h2 class="mb-4 font-medium">Case</h2>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Employer</dt>
                            <dd>
                                <Link
                                    :href="showEmployer(props.case.employer.id)"
                                    class="font-medium underline underline-offset-4"
                                >
                                    {{ props.case.employer.name }}
                                </Link>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Status</dt>
                            <dd class="mt-0.5">
                                <CaseStatusBadge :status="props.case.status" />
                            </dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Case officer</dt>
                            <dd
                                v-if="!props.can.manage_cases"
                                class="font-medium"
                            >
                                {{ props.case.assigned_officer?.name ?? '—' }}
                            </dd>
                            <dd v-else class="flex items-center gap-2">
                                <Select
                                    v-model="assignForm.case_officer_user_id"
                                    class="flex-1"
                                >
                                    <SelectTrigger class="h-8 text-sm">
                                        <SelectValue placeholder="Unassigned" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="officer in props.caseOfficers"
                                            :key="officer.id"
                                            :value="officer.id"
                                        >
                                            {{ officer.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <Button
                                    size="sm"
                                    class="h-8"
                                    :disabled="assignForm.processing"
                                    @click="submitAssignment"
                                    >Save</Button
                                >
                            </dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Opened</dt>
                            <dd class="font-medium">
                                {{ formatDate(props.case.opened_at) }}
                            </dd>
                        </div>
                        <div v-if="props.case.closed_at">
                            <dt class="text-muted-foreground">Closed</dt>
                            <dd class="font-medium">
                                {{ formatDate(props.case.closed_at) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border p-4">
                    <h2 class="mb-4 font-medium">From the doctor</h2>
                    <p
                        v-if="
                            !props.case.advice &&
                            !props.case.restrictions &&
                            !props.case.expected_return_date
                        "
                        class="text-sm text-muted-foreground"
                    >
                        No medical outcomes have been shared yet.
                    </p>
                    <dl v-else class="space-y-3 text-sm">
                        <div v-if="props.case.expected_return_date">
                            <dt class="text-muted-foreground">
                                Expected return date
                            </dt>
                            <dd
                                class="flex items-center gap-1.5 font-medium"
                                :class="
                                    isReturnDateOverdue
                                        ? 'text-destructive'
                                        : ''
                                "
                            >
                                <TriangleAlert
                                    v-if="isReturnDateOverdue"
                                    class="size-3.5 shrink-0"
                                />
                                {{
                                    formatDate(props.case.expected_return_date)
                                }}
                                <span
                                    v-if="isReturnDateOverdue"
                                    class="font-normal"
                                    >(overdue)</span
                                >
                            </dd>
                        </div>
                        <div v-if="props.case.restrictions">
                            <dt class="text-muted-foreground">Restrictions</dt>
                            <dd class="font-medium">
                                {{ props.case.restrictions }}
                            </dd>
                        </div>
                        <div v-if="props.case.advice">
                            <dt class="text-muted-foreground">Advice</dt>
                            <dd class="font-medium">{{ props.case.advice }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Reintegration timeline -->
                <div
                    v-if="props.milestones.length > 0"
                    class="rounded-lg border p-4"
                >
                    <h2 class="mb-4 font-medium">Reintegration timeline</h2>
                    <ol class="space-y-3 text-sm">
                        <li
                            v-for="milestone in props.milestones"
                            :key="milestone.milestone"
                            class="flex items-center justify-between gap-2"
                        >
                            <div>
                                <p class="font-medium">{{ milestone.label }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatDate(milestone.due_date) }}
                                </p>
                            </div>
                            <Badge
                                variant="outline"
                                :class="
                                    milestoneStatusClasses[milestone.status]
                                "
                            >
                                {{ milestoneStatusLabels[milestone.status] }}
                            </Badge>
                        </li>
                    </ol>
                </div>

                <!-- Tasks -->
                <div class="rounded-lg border p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-medium">Tasks</h2>
                        <Button
                            v-if="
                                props.case.status === 'open' &&
                                props.can.manage_cases
                            "
                            size="sm"
                            variant="ghost"
                            @click="showTaskForm = !showTaskForm"
                        >
                            {{ showTaskForm ? 'Cancel' : 'Add task' }}
                        </Button>
                    </div>

                    <div
                        v-if="showTaskForm"
                        class="mb-4 space-y-3 rounded-md border p-3"
                    >
                        <Select
                            v-if="props.taskTypes.length"
                            v-model="taskForm.task_type_id"
                        >
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Task type (optional)"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="tt in props.taskTypes"
                                    :key="tt.id"
                                    :value="tt.id"
                                >
                                    {{ tt.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Input v-model="taskForm.title" placeholder="Title" />
                        <Textarea
                            v-model="taskForm.description"
                            placeholder="Description (optional)"
                            rows="2"
                        />
                        <Input v-model="taskForm.due_date" type="date" />
                        <Button
                            size="sm"
                            :disabled="taskForm.processing"
                            @click="submitTask"
                            >Add</Button
                        >
                    </div>

                    <div
                        v-if="props.tasks.length === 0 && !showTaskForm"
                        class="text-sm text-muted-foreground"
                    >
                        No tasks yet.
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="task in props.tasks"
                            :key="task.id"
                            class="rounded-md border p-3 text-sm"
                            :class="task.completed_at ? 'opacity-60' : ''"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="font-medium"
                                        :class="
                                            task.completed_at
                                                ? 'line-through'
                                                : ''
                                        "
                                    >
                                        {{ task.title }}
                                    </p>
                                    <p
                                        v-if="task.task_type_name"
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ task.task_type_name }}
                                    </p>
                                    <p
                                        v-if="task.due_date"
                                        class="text-xs text-muted-foreground"
                                    >
                                        Due: {{ formatDate(task.due_date) }}
                                    </p>
                                    <p
                                        v-if="task.assigned_to"
                                        class="text-xs text-muted-foreground"
                                    >
                                        Assigned to: {{ task.assigned_to }}
                                    </p>
                                </div>
                                <div
                                    v-if="props.can.manage_cases"
                                    class="flex shrink-0 gap-1"
                                >
                                    <Button
                                        v-if="!task.completed_at"
                                        size="sm"
                                        variant="ghost"
                                        class="h-7 px-2 text-xs"
                                        @click="markComplete(task)"
                                    >
                                        <Check class="size-3.5" />
                                        Done
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="size-7 text-destructive"
                                        aria-label="Delete task"
                                        @click="
                                            pendingDelete = {
                                                kind: 'task',
                                                task,
                                            }
                                        "
                                    >
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </div>
                            <p
                                v-if="task.description"
                                class="mt-1 text-xs text-muted-foreground"
                            >
                                {{ task.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle column: notes -->
            <div class="flex flex-col gap-4 lg:col-span-1">
                <div class="rounded-lg border p-4">
                    <h2 class="mb-4 font-medium">Notes</h2>

                    <div
                        v-if="props.notes.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No notes yet.
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="note in props.notes"
                            :key="note.id"
                            class="rounded-md border p-3 text-sm"
                        >
                            <div
                                class="mb-1 flex items-center justify-between gap-2"
                            >
                                <span class="font-medium">{{
                                    note.note_type_name
                                }}</span>
                                <span class="text-xs text-muted-foreground">{{
                                    formatDate(note.created_at)
                                }}</span>
                            </div>
                            <div class="mb-2 text-xs text-muted-foreground">
                                {{ note.author_name }}
                            </div>

                            <template v-if="editingNoteId === note.id">
                                <Textarea
                                    v-model="editForm.body"
                                    class="mb-2"
                                    rows="3"
                                />
                                <div class="flex gap-2">
                                    <Button size="sm" @click="submitEdit(note)"
                                        >Save</Button
                                    >
                                    <Button
                                        size="sm"
                                        variant="ghost"
                                        @click="editingNoteId = null"
                                        >Cancel</Button
                                    >
                                </div>
                            </template>
                            <template v-else>
                                <p class="whitespace-pre-wrap">
                                    {{ note.body }}
                                </p>
                                <div
                                    v-if="note.can_update || note.can_delete"
                                    class="mt-2 flex gap-2"
                                >
                                    <Button
                                        v-if="note.can_update"
                                        size="sm"
                                        variant="ghost"
                                        class="h-7 px-2 text-xs"
                                        @click="startEdit(note)"
                                    >
                                        <Pencil class="size-3.5" />
                                        Edit
                                    </Button>
                                    <Button
                                        v-if="note.can_delete"
                                        size="icon"
                                        variant="ghost"
                                        class="size-7 text-destructive"
                                        aria-label="Delete note"
                                        @click="
                                            pendingDelete = {
                                                kind: 'note',
                                                note,
                                            }
                                        "
                                    >
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div
                        v-if="props.writableNoteTypes.length > 0"
                        class="mt-4 border-t pt-4"
                    >
                        <h3 class="mb-3 text-sm font-medium">Add note</h3>
                        <form @submit.prevent="submitNote" class="space-y-3">
                            <Select v-model="noteForm.note_type_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Note type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="nt in props.writableNoteTypes"
                                        :key="nt.id"
                                        :value="nt.id"
                                    >
                                        {{ nt.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <Textarea
                                v-model="noteForm.body"
                                placeholder="Write your note…"
                                rows="3"
                            />
                            <Button
                                type="submit"
                                size="sm"
                                :disabled="noteForm.processing"
                                >Add note</Button
                            >
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right column: timeline -->
            <div class="lg:col-span-1">
                <div class="rounded-lg border p-4">
                    <h2 class="mb-4 font-medium">Timeline</h2>

                    <div
                        v-if="props.timeline.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No events recorded yet.
                    </div>

                    <ol class="relative space-y-4 border-l border-border pl-4">
                        <li
                            v-for="event in props.timeline"
                            :key="event.id"
                            class="relative"
                        >
                            <span
                                class="absolute -left-[1.15rem] mt-1 flex h-3 w-3 items-center justify-center rounded-full bg-primary ring-2 ring-background"
                            />
                            <p class="text-sm leading-tight font-medium">
                                {{ eventLabel(event.event) }}
                            </p>
                            <p
                                v-if="event.actor_name"
                                class="text-xs text-muted-foreground"
                            >
                                {{ event.actor_name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ formatDate(event.occurred_at) }}
                            </p>
                            <div v-if="event.payload" class="mt-1 space-y-0.5">
                                <p
                                    v-for="(val, key) in event.payload"
                                    :key="key"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ payloadLabel(String(key)) }}:
                                    {{ payloadValue(String(val)) }}
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <Dialog v-model:open="showCloseDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Report recovered</DialogTitle>
                <DialogDescription>
                    Close the case for
                    {{ props.case.employee.first_name }}
                    {{ props.case.employee.last_name }} by registering the
                    recovery date.
                </DialogDescription>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submitClose">
                <div class="grid gap-2">
                    <Label for="close_recovery_date">Recovery date</Label>
                    <Input
                        id="close_recovery_date"
                        v-model="closeForm.recovery_date"
                        type="date"
                        required
                    />
                    <p
                        v-if="closeForm.errors.recovery_date"
                        class="text-sm text-destructive"
                    >
                        {{ closeForm.errors.recovery_date }}
                    </p>
                </div>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="ghost"
                        @click="showCloseDialog = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="closeForm.processing"
                    >
                        Close case
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog
        :open="pendingDelete !== null"
        @update:open="(v) => !v && (pendingDelete = null)"
    >
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    Delete
                    {{ pendingDelete?.kind === 'note' ? 'note' : 'task' }}
                </DialogTitle>
                <DialogDescription>
                    You are about to delete {{ pendingDeleteLabel }}. This
                    cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="ghost" @click="pendingDelete = null">
                    Cancel
                </Button>
                <Button variant="destructive" @click="confirmDelete">
                    Delete
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
