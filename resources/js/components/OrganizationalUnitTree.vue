<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { edit } from '@/routes/organizational-units';

type OrganizationalUnit = {
    id: string;
    parent_id: string | null;
    name: string;
    is_legal_entity: boolean;
    kvk_number: string | null;
};

const props = defineProps<{
    units: OrganizationalUnit[];
    employerId: string;
    parentId?: string | null;
}>();

const children = props.units.filter(
    (unit) => unit.parent_id === (props.parentId ?? null),
);
</script>

<template>
    <ul class="space-y-1" :class="{ 'ml-5 border-l pl-3': parentId }">
        <li v-for="unit in children" :key="unit.id">
            <Link
                :href="
                    edit({ employer: employerId, organizationalUnit: unit.id })
                "
                class="block rounded-md px-2 py-1 text-sm hover:bg-muted"
            >
                {{ unit.name }}
                <span class="text-muted-foreground">
                    ({{ unit.is_legal_entity ? 'legal entity' : 'internal unit'
                    }}<template v-if="unit.kvk_number"
                        >, KvK {{ unit.kvk_number }}</template
                    >)
                </span>
            </Link>

            <OrganizationalUnitTree
                :units="units"
                :employer-id="employerId"
                :parent-id="unit.id"
            />
        </li>
    </ul>
</template>
