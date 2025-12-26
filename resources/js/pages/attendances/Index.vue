<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import attendanceRoutes from '@/routes/attendances';
import ResourceTable from '@/components/ResourceTable.vue';
import ResourceTableFilter from '@/components/ResourceTableFilter.vue';
import { useFetcher } from '@/composables/useFetcher';
import { Button } from '@/components/ui/button';
import { PlusCircle } from 'lucide-vue-next';
import dayjs from 'dayjs';

const props = defineProps<{
    attendances: any;
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

const columns = [
    { key: 'snapshot_member_name', label: 'Member', className: 'font-medium', headClassName: '[&>span:first-of-type]:ps-4' },
    { key: 'snapshot_membership_name', label: 'Membership', headClassName: '[&>span:first-of-type]:ps-4' },
    { key: 'check_in_at', label: 'Time', headClassName: '[&>span:first-of-type]:ps-4' },
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
                />

                <Link :href="attendanceRoutes.create.url()">
                    <Button>
                        <PlusCircle class="w-4 h-4 mr-1.5" />
                        Log visit
                    </Button>
                </Link>
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
            </ResourceTable>
        </div>
    </AppLayout>
</template>
