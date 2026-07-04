<script setup lang="ts">
import { Form, usePage } from '@inertiajs/vue3';
import { CalendarCheck, CalendarClock, FilePlus } from '@lucide/vue';
import { ref } from 'vue';
import EmployeeCombobox from '@/components/EmployeeCombobox.vue';
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
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { search as employeeSearch } from '@/routes/employees';
import { close as closeRoute, store as storeRoute, update as mutateRoute } from '@/routes/cases';

type OpenCase = { id: string; employee: { first_name: string; last_name: string } | null; expected_return_date: string | null };

const page = usePage<{ sidebarOpenCases: OpenCase[] }>();

type DialogType = 'absence' | 'mutate' | 'recover' | null;
const openDialog = ref<DialogType>(null);
const today = new Date().toISOString().slice(0, 10);
const selectedCaseId = ref<string>('');
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton @click="openDialog = 'absence'">
                <FilePlus />
                <span>Report absence</span>
            </SidebarMenuButton>
        </SidebarMenuItem>
        <SidebarMenuItem>
            <SidebarMenuButton @click="openDialog = 'mutate'">
                <CalendarClock />
                <span>Mutate absence</span>
            </SidebarMenuButton>
        </SidebarMenuItem>
        <SidebarMenuItem>
            <SidebarMenuButton @click="openDialog = 'recover'">
                <CalendarCheck />
                <span>Report recovered</span>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>

    <!-- Report absence dialog -->
    <Dialog :open="openDialog === 'absence'" @update:open="(v) => !v && (openDialog = null)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Report absence</DialogTitle>
            </DialogHeader>
            <Form
                v-bind="storeRoute.form()"
                v-slot="{ errors, processing }"
                :reset-on-success="['employee_id', 'start_date']"
                class="space-y-4"
                @success="openDialog = null"
            >
                <div class="grid gap-2">
                    <Label>Employee</Label>
                    <EmployeeCombobox
                        name="employee_id"
                        :search-url="employeeSearch().url"
                        :error="errors.employee_id"
                    />
                    <InputError :message="errors.employee_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="ab_start_date">Start date</Label>
                    <Input id="ab_start_date" type="date" name="start_date" :default-value="today" required />
                    <InputError :message="errors.start_date" />
                </div>
                <Button type="submit" :disabled="processing">Report absence</Button>
            </Form>
        </DialogContent>
    </Dialog>

    <!-- Mutate absence dialog -->
    <Dialog :open="openDialog === 'mutate'" @update:open="(v) => !v && (openDialog = null)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Mutate absence</DialogTitle>
            </DialogHeader>
            <p v-if="page.props.sidebarOpenCases.length === 0" class="text-sm text-muted-foreground">
                No open cases at the moment.
            </p>
            <Form
                v-else
                v-bind="mutateRoute.form({ case: selectedCaseId })"
                v-slot="{ errors, processing }"
                class="space-y-4"
                @success="openDialog = null"
            >
                <div class="grid gap-2">
                    <Label for="mut_case_id">Case</Label>
                    <select
                        id="mut_case_id"
                        v-model="selectedCaseId"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option value="" disabled>Select a case</option>
                        <option v-for="c in page.props.sidebarOpenCases" :key="c.id" :value="c.id">
                            {{ c.employee ? `${c.employee.first_name} ${c.employee.last_name}` : c.id }}
                        </option>
                    </select>
                </div>
                <div class="grid gap-2">
                    <Label for="mut_expected_return_date">Expected return date</Label>
                    <Input id="mut_expected_return_date" type="date" name="expected_return_date" />
                    <InputError :message="errors.expected_return_date" />
                </div>
                <Button type="submit" :disabled="processing || !selectedCaseId">Save mutation</Button>
            </Form>
        </DialogContent>
    </Dialog>

    <!-- Report recovered dialog -->
    <Dialog :open="openDialog === 'recover'" @update:open="(v) => !v && (openDialog = null)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Report recovered</DialogTitle>
            </DialogHeader>
            <p v-if="page.props.sidebarOpenCases.length === 0" class="text-sm text-muted-foreground">
                No open cases at the moment.
            </p>
            <Form
                v-else
                v-bind="closeRoute.form({ case: selectedCaseId })"
                v-slot="{ errors, processing }"
                class="space-y-4"
                @success="openDialog = null"
            >
                <div class="grid gap-2">
                    <Label for="rec_case_id">Case</Label>
                    <select
                        id="rec_case_id"
                        v-model="selectedCaseId"
                        required
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs"
                    >
                        <option value="" disabled>Select a case</option>
                        <option v-for="c in page.props.sidebarOpenCases" :key="c.id" :value="c.id">
                            {{ c.employee ? `${c.employee.first_name} ${c.employee.last_name}` : c.id }}
                        </option>
                    </select>
                </div>
                <div class="grid gap-2">
                    <Label for="rec_recovery_date">Recovery date</Label>
                    <Input id="rec_recovery_date" type="date" name="recovery_date" required />
                    <InputError :message="errors.recovery_date" />
                </div>
                <Button type="submit" variant="destructive" :disabled="processing || !selectedCaseId">Close case</Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
