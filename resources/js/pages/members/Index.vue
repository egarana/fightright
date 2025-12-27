<script setup lang="ts">
import members from '@/routes/members';
import BaseIndexPage from '@/components/BaseIndexPage.vue';
import { IdCard, ExternalLink, CalendarX2 } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const can = computed(() => (page.props.auth as any)?.can ?? {});

const props = defineProps<{
    membershipTypes: Array<{ id: number; name: string }>;
}>();

const config = computed(() => ({
    resourceName: 'Member',
    resourceNamePlural: 'Members',
    endpoint: members.index.url(),
    resourceKey: 'members',
    filters: [
        ...(props.membershipTypes.length > 0 ? [{
            name: 'membership_id',
            label: 'Membership',
            options: props.membershipTypes.map((m) => ({ value: m.id.toString(), label: m.name })),
        }] : []),
    ],
    columns: [
        { key: 'member_code', label: 'Code', sortable: true, className: 'font-mono' },
        { key: 'name', label: 'Name', sortable: true, className: 'font-medium' },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'memberships_count', label: 'Memberships', sortable: true },
        { key: 'expired_memberships', label: 'Expired', sortable: true },
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
        <template #cell-memberships_count="{ value }">
            <div class="flex items-center gap-2">
                <IdCard class="w-4 h-4 text-muted-foreground" />
                <span>{{ value }}</span>
            </div>
        </template>
        <template #cell-expired_memberships="{ value }">
            <div class="flex items-center gap-2">
                <CalendarX2 class="w-4 h-4 text-muted-foreground" />
                <span>{{ value }}</span>
            </div>
        </template>
    </BaseIndexPage>
</template>
