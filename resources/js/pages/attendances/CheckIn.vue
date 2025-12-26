<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import attendances from '@/routes/attendances';
import ResourceTable from '@/components/ResourceTable.vue';
import ResourceTableFilter from '@/components/ResourceTableFilter.vue';
import { useFetcher } from '@/composables/useFetcher';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { CheckCircle } from 'lucide-vue-next';
import dayjs from 'dayjs';

const props = defineProps<{
    memberMemberships: any;
    filters?: any;
}>();

const breadcrumbs = [
    {
        title: 'Attendances',
        href: attendances.index.url(),
    },
    {
        title: 'Log Visit',
        href: attendances.create.url(),
    },
];

const { resource, refresh } = useFetcher({
    endpoint: attendances.create.url(),
    resourceKey: 'memberMemberships',
    preserveScroll: true,
    preserveUrl: true,
});

const form = useForm({
    member_membership_id: '',
});

const handleCheckIn = (item: any) => {
    form.member_membership_id = item.id;
    form.post(attendances.store.url(), {
        onSuccess: () => {
            toast.success('Visit logged successfully');
        },
        onError: (errors: any) => {
            toast.error(errors.member_membership_id || 'Failed to log visit');
        }
    });
};

const columns = [
    { key: 'member.name', label: 'Member', className: 'font-medium' },
    { key: 'snapshot_membership_name', label: 'Membership' },
    { key: 'remaining_qty', label: 'Balance' },
    { key: 'expired_at', label: 'Expires' },
    { key: 'actions', label: '', className: 'w-[100px]' },
];

const formatDate = (date: string) => dayjs(date).format('DD MMM YYYY');
</script>

<template>
    <Head title="Log Visit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-2">
                <ResourceTableFilter
                    search-placeholder="Search member name or code..."
                    :search-fields="['member.name', 'member.member_code']"
                    :show-add-button="false"
                    :refresh="refresh"
                />
            </div>

            <ResourceTable
                :data="resource || props.memberMemberships"
                :columns="columns"
                :refresh="refresh"
                resource-name="active membership"
            >
                <template #cell-remaining_qty="{ value }">
                    <span v-if="value === null">Unlimited</span>
                    <span v-else>{{ value }} visits</span>
                </template>
                <template #cell-expired_at="{ value }">
                    {{ formatDate(value) }}
                </template>
                <template #cell-actions="{ item }">
                    <Button 
                        size="sm" 
                        @click="handleCheckIn(item)" 
                        :disabled="form.processing"
                    >
                        <CheckCircle class="w-4 h-4 mr-1.5" />
                        Log Visit
                    </Button>
                </template>
            </ResourceTable>
        </div>
    </AppLayout>
</template>
