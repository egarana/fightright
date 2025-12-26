<script setup lang="ts">
import memberships from '@/routes/memberships';
import BaseIndexPage from '@/components/BaseIndexPage.vue';
import { formatCurrency } from '@/helpers/currency';
import { Badge } from '@/components/ui/badge';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const can = computed(() => (page.props.auth as any)?.can ?? {});

const config = computed(() => ({
    resourceName: 'Membership',
    resourceNamePlural: 'Memberships',
    endpoint: memberships.index.url(),
    resourceKey: 'memberships',
    columns: [
        { key: 'name', label: 'Name', sortable: true, className: 'font-medium' },
        { key: 'duration_days', label: 'Duration (Days)', sortable: true },
        { key: 'max_attendance_qty', label: 'Max Attendance', sortable: true },
        { key: 'price', label: 'Price', sortable: true, formatter: (value: number) => formatCurrency(value, 'IDR') },
        { key: 'is_active', label: 'Status', sortable: true },
        { key: 'created_at', label: 'Created At', sortable: true },
        { key: 'updated_at', label: 'Updated At', sortable: true },
    ],
    searchFields: ['name'],
    showTable: true,
    breadcrumbs: [
        { title: 'Memberships', href: memberships.index.url() },
    ],
    // Add/Edit/Delete - only if user has manage_memberships permission
    addButtonRoute: can.value.manage_memberships ? memberships.create.url() : undefined,
    editRoute: can.value.manage_memberships 
        ? (item: any) => memberships.edit.url(item.id) 
        : undefined,
    deleteRoute: can.value.manage_memberships 
        ? (item: any) => ({ url: memberships.destroy.url(item.id) }) 
        : undefined,
}));
</script>

<template>
    <BaseIndexPage title="Memberships" :config="config">
        <template #cell-is_active="{ value }">
            <Badge :variant="value ? 'outline' : 'secondary'" class="text-xs rounded-md">
                {{ value ? 'Active' : 'Inactive' }}
            </Badge>
        </template>
    </BaseIndexPage>
</template>
