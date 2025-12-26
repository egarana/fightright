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
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, CalendarCheck, ContactRound, Folder, IdCard, LayoutGrid, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
import dashboard from '@/routes/dashboard';
import users from '@/routes/users';
import members from '@/routes/members';
import memberships from '@/routes/memberships';
import attendances from '@/routes/attendances';

const page = usePage();

import ImpersonationBanner from '@/components/ImpersonationBanner.vue';

// Auth permissions from backend
const can = computed(() => (page.props.auth as any)?.can ?? {});

// Build nav items based on permissions
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard.index.url(),
            icon: LayoutGrid,
        },
    ];

    // Only super-admin can see Users
    if (can.value.manage_users) {
        items.push({
            title: 'Users',
            href: users.index.url(),
            icon: Users,
        });
    }

    items.push(
        {
            title: 'Members',
            href: members.index.url(),
            icon: ContactRound,
        },
        {
            title: 'Memberships',
            href: memberships.index.url(),
            icon: IdCard,
        },
        {
            title: 'Attendances',
            href: attendances.index.url(),
            icon: CalendarCheck,
        },
    );

    return items;
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
                        <Link :href="dashboard.index.url()">
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
            <!-- <NavFooter :items="footerNavItems" /> -->
            <ImpersonationBanner />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
