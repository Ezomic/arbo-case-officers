<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BarChart3, FolderOpen, LayoutGrid, Users, UserCog } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import CaseSidebarActions from '@/components/CaseSidebarActions.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard as dashboardIndex } from '@/routes';
import { index as casesIndex } from '@/routes/cases';
import { index as employeesIndex } from '@/routes/employees';
import { index as employersIndex } from '@/routes/employers';
import { index as usersIndex } from '@/routes/users';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboardIndex(),
        icon: BarChart3,
    },
    {
        title: 'Employers',
        href: employersIndex(),
        icon: LayoutGrid,
    },
    {
        title: 'Employees',
        href: employeesIndex(),
        icon: Users,
    },
    {
        title: 'Cases',
        href: casesIndex(),
        icon: FolderOpen,
    },
    {
        title: 'Users',
        href: usersIndex(),
        icon: UserCog,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="employersIndex()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <CaseSidebarActions />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
