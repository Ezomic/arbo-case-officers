<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy, store, update } from '@/routes/users';

type IdentityUser = {
    id: string;
    name: string;
    email: string;
    user_type_id: string;
    user_type_name: string | null;
    role_name: string | null;
    created_at: string;
};

defineProps<{ users: IdentityUser[] }>();

const page = usePage<{ flash?: { temporaryPassword?: string } }>();

const showCreate = ref(false);
const editingUser = ref<IdentityUser | null>(null);

const userTypeLabel: Record<string, string> = {
    arbo: 'Case officer',
    medical_doctor: 'Medical doctor',
};
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <Heading
                title="Users"
                description="Manage case officers and medical doctors."
            />
            <Button size="sm" @click="showCreate = true">
                <Plus class="mr-1 size-4" />
                Add user
            </Button>
        </div>

        <div
            v-if="page.props.flash?.temporaryPassword"
            class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 text-sm dark:border-yellow-700 dark:bg-yellow-950"
        >
            <p class="font-medium">
                User created. Share this temporary password with them:
            </p>
            <code class="mt-1 block font-mono text-base">{{
                page.props.flash.temporaryPassword
            }}</code>
        </div>

        <div class="rounded-lg border">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-left font-medium">Type</th>
                        <th class="px-4 py-3 text-left font-medium">Since</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="cursor-pointer border-b last:border-0 hover:bg-muted/30"
                        @click="editingUser = user"
                    >
                        <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ user.email }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{
                                userTypeLabel[user.user_type_id] ??
                                user.user_type_id
                            }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ user.created_at.slice(0, 10) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Form
                                v-bind="destroy.form({ uuid: user.id })"
                                class="inline"
                                @click.stop
                            >
                                <Button
                                    type="submit"
                                    variant="ghost"
                                    size="icon"
                                    class="size-7"
                                >
                                    <Trash2 class="size-3.5" />
                                </Button>
                            </Form>
                        </td>
                    </tr>
                    <tr v-if="users.length === 0">
                        <td
                            colspan="5"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No users yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create dialog -->
    <Dialog :open="showCreate" @update:open="(v) => !v && (showCreate = false)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add user</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="store.form()"
                v-slot="{ errors, processing }"
                :reset-on-success="['name', 'email']"
                class="space-y-4"
                @success="showCreate = false"
            >
                <div class="grid gap-2">
                    <Label for="new_name">Name</Label>
                    <Input id="new_name" name="name" required />
                    <InputError :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="new_email">Email</Label>
                    <Input id="new_email" type="email" name="email" required />
                    <InputError :message="errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="new_user_type_id">Type</Label>
                    <select
                        id="new_user_type_id"
                        name="user_type_id"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option value="arbo">Case officer</option>
                        <option value="medical_doctor">Medical doctor</option>
                    </select>
                    <InputError :message="errors.user_type_id" />
                </div>
                <Button type="submit" :disabled="processing"
                    >Create user</Button
                >
            </Form>
        </DialogContent>
    </Dialog>

    <!-- Edit dialog -->
    <Dialog
        :open="editingUser !== null"
        @update:open="(v) => !v && (editingUser = null)"
    >
        <DialogContent v-if="editingUser">
            <DialogHeader>
                <DialogTitle>Edit user</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="update.form({ uuid: editingUser.id })"
                v-slot="{ errors, processing }"
                class="space-y-4"
                @success="editingUser = null"
            >
                <div class="grid gap-2">
                    <Label for="edit_name">Name</Label>
                    <Input
                        id="edit_name"
                        name="name"
                        :default-value="editingUser.name"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="edit_email">Email</Label>
                    <Input
                        id="edit_email"
                        type="email"
                        name="email"
                        :default-value="editingUser.email"
                        required
                    />
                    <InputError :message="errors.email" />
                </div>
                <div class="flex gap-2">
                    <Button type="submit" :disabled="processing">Save</Button>
                    <Button
                        type="button"
                        variant="outline"
                        @click="editingUser = null"
                        >Cancel</Button
                    >
                </div>
            </Form>
        </DialogContent>
    </Dialog>
</template>
