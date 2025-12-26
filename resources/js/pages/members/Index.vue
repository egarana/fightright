<script setup lang="ts">
import members from '@/routes/members';
import BaseIndexPage from '@/components/BaseIndexPage.vue';
import { IdCard, ExternalLink } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const can = computed(() => (page.props.auth as any)?.can ?? {});

const config = computed(() => ({
    resourceName: 'Member',
    resourceNamePlural: 'Members',
    endpoint: members.index.url(),
    resourceKey: 'members',
    columns: [
        { key: 'member_code', label: 'Code', sortable: true, className: 'font-mono' },
        { key: 'name', label: 'Name', sortable: true, className: 'font-medium' },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'member_memberships_count', label: 'Memberships', sortable: true },
        { key: 'created_at', label: 'Created At', sortable: true },
        { key: 'updated_at', label: 'Updated At', sortable: true },
    ],
    searchFields: ['member_code', 'name', 'email'],
    showTable: true,
    breadcrumbs: [
        { title: 'Members', href: members.index.url() },
    ],
    addButtonRoute: members.create.url(),
    // Edit - only if user has edit_members permission
    editRoute: can.value.edit_members 
        ? (item: any) => members.edit.url(item.id) 
        : undefined,
    // Delete - only if user has delete_members permission
    deleteRoute: can.value.delete_members 
        ? (item: any) => ({ url: members.destroy.url(item.id) }) 
        : undefined,
    customActions: [
        {
            icon: ExternalLink,
            label: 'View',
            tooltip: 'View profile',
            url: (item: any) => `/m/${item.encoded_url}`,
            target: '_blank',
        },
        {
            icon: IdCard,
            tooltip: 'Memberships',
            url: (item: any) => members.memberships.index.url(item.id),
            variant: 'outline' as const,
        },
    ],
}));
</script>

<template>
    <BaseIndexPage title="Members" :config="config">
        <template #cell-member_memberships_count="{ value }">
            <div class="flex items-center gap-2">
                <IdCard class="w-4 h-4 text-muted-foreground" />
                <span>{{ value }}</span>
            </div>
        </template>
    </BaseIndexPage>
</template>
