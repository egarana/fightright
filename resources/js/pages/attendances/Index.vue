<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import attendanceRoutes from '@/routes/attendances';
import ResourceTable from '@/components/ResourceTable.vue';
import ResourceTableFilter, { type FilterConfig } from '@/components/ResourceTableFilter.vue';
import { useFetcher } from '@/composables/useFetcher';
import { computed } from 'vue';
import dayjs from 'dayjs';

const props = defineProps<{
    attendances: any;
    admins: string[];
    filters?: any;
}>();

const breadcrumbs = [
    {
        title: 'Attendances',
        href: attendanceRoutes.index.url(),
    },
];

const { resource, refresh } = useFetcher({
    endpoint: attendanceRoutes.index.url(),
    resourceKey: 'attendances',
});

// Build admin filter options from backend data
const adminFilters = computed<FilterConfig[]>(() => {
    if (!props.admins || props.admins.length === 0) return [];
    
    return [{
        name: 'recordedBy',
        label: 'Recorded By',
        placeholder: 'Filter by admin',
        options: props.admins,
    }];
});

const columns = [
    { key: 'snapshot_member_name', label: 'Member', className: 'font-medium', headClassName: '[&>span:first-of-type]:ps-4' },
    { key: 'snapshot_membership_name', label: 'Membership', headClassName: '[&>span:first-of-type]:ps-4' },
    { key: 'check_in_at', label: 'Time', headClassName: '[&>span:first-of-type]:ps-4' },
    { key: 'snapshot_recorded_by_name', label: 'Recorded By', headClassName: '[&>span:first-of-type]:ps-4' },
];

const formatDateTime = (date: string) => dayjs(date).format('DD MMM YYYY, HH:mm');
</script>

<template>
    <Head title="Attendances" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-2">
                <ResourceTableFilter
                    search-placeholder="Search member name..."
                    :search-fields="['snapshot_member_name']"
                    :show-add-button="false"
                    :refresh="refresh"
                    :filters="adminFilters"
                />
            </div>

            <ResourceTable
                :data="resource || props.attendances"
                :columns="columns"
                :refresh="refresh"
                resource-name="visit"
            >
                <template #cell-check_in_at="{ value }">
                    {{ formatDateTime(value) }}
                </template>
                <template #cell-snapshot_recorded_by_name="{ value }">
                    <span v-if="value" class="text-muted-foreground">{{ value }}</span>
                    <span v-else class="text-muted-foreground/50 italic">—</span>
                </template>
            </ResourceTable>
        </div>
    </AppLayout>
</template>


