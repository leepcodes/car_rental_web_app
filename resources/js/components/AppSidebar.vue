<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
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
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    BookOpen, 
    Folder, 
    LayoutGrid, 
    Users, 
    Car, 
    Calendar, 
    Settings, 
    MonitorCog
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

// Get the authenticated user from Inertia
const page = usePage();
const userType = computed(() => page.props.auth?.user?.user_type);

// Dynamic navigation based on role
const mainNavItems = computed<NavItem[]>(() => {
    if (userType.value === 'superadmin') {
        return [
            {
                title: 'Dashboard',
                href: '/superadmin/dashboard',
                icon: LayoutGrid,
            },
            {
                title: 'Clients',
                href: '/superadmin/clients',
                icon: Users,
            },
            {
                title: 'Operators',
                href: '/superadmin/operators',
                icon: MonitorCog,
            },
           
         
        ];
    }

    // Default for other roles
    return [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>