<script setup lang="ts">
import users from '@/routes/users';
import impersonate from '@/routes/impersonate';
import BaseIndexPage from '@/components/BaseIndexPage.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Eye } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

const page = usePage();
const can = computed(() => (page.props.auth as any)?.can ?? {});
const currentUserId = computed(() => (page.props.auth as any)?.user?.id);

const config = computed(() => ({
    resourceName: 'User',
    resourceNamePlural: 'Users',
    endpoint: users.index.url(),
    resourceKey: 'users',
    columns: [
        { key: 'name', label: 'Name', sortable: true, className: 'font-medium' },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'roles', label: 'Role', sortable: true },
        { key: 'created_at', label: 'Created At', sortable: true },
        { key: 'updated_at', label: 'Updated At', sortable: true },
    ],
    searchFields: ['name', 'email'],
    showTable: true,
    breadcrumbs: [
        { title: 'Users', href: users.index.url() },
    ],
    addButtonRoute: users.create.url(),
    editRoute: (item: any) => users.edit.url(item.id),
    deleteRoute: (item: any) => ({ url: users.destroy.url(item.id) }),
    customActions: can.value.impersonate ? [
        {
            icon: Eye,
            tooltip: 'Impersonate',
            url: (item: any) => impersonate.start.url(item.id),
            variant: 'outline' as const,
            method: 'post' as const,
            // Hide for current user and other super-admins
            condition: (item: any) => {
                const isSelf = item.id === currentUserId.value;
                const isSuperAdmin = item.roles?.some((r: any) => 
                    (typeof r === 'string' ? r : r.name) === 'super-admin'
                );
                return !isSelf && !isSuperAdmin;
            },
        },
    ] : [],
}));
</script>

<template>
    <BaseIndexPage title="Users" :config="config">
        <template #cell-roles="{ item }">
            <div class="flex flex-wrap gap-1">
                <Badge
                    v-for="(role, index) in item.roles"
                    :key="index"
                    variant="secondary"
                    class="rounded-md"
                >
                    {{ typeof role === 'string' ? role : role.name }}
                </Badge>
            </div>
        </template>
    </BaseIndexPage>
</template>
