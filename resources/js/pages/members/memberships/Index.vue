<script setup lang="ts">
import members from '@/routes/members';
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useFetcher } from '@/composables/useFetcher';
import { useSorter } from '@/composables/useSorter';
import ResourceTable from '@/components/ResourceTable.vue';
import ResourceTableFilter from '@/components/ResourceTableFilter.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { PlusCircle, CalendarIcon, CalendarCheck, Infinity } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { DateFormatter, getLocalTimeZone, today, type DateValue } from '@internationalized/date';
import dayjs from 'dayjs';
import { formatCurrency } from '@/helpers/currency';
import { toast } from 'vue-sonner';

interface Member {
    id: number;
    name: string;
    member_code: string;
}

interface Membership {
    id: number;
    name: string;
    duration_days: number;
    max_attendance_qty: number | null;
    price: number;
}

interface MemberMembership {
    id: number;
    snapshot_membership_name: string;
    snapshot_max_attendance_qty: number | null;
    snapshot_duration_days: number;
    snapshot_price: number;
    started_at: string;
    expired_at: string;
    status: string;
    created_at: string;
}

interface Props {
    member: Member;
    memberMemberships: {
        data: MemberMembership[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    memberships: Membership[];
}

const props = defineProps<Props>();

const page = usePage();
const can = computed(() => (page.props.auth as any)?.can ?? {});

const breadcrumbs = [
    { title: 'Members', href: members.index.url() },
    { title: props.member.name, href: members.edit.url(props.member.id) },
    { title: 'Memberships', href: members.memberships.index.url(props.member.id) },
];

// Dialog state
const dialogOpen = ref(false);

// Setup fetcher
const { resource, fetchData, refresh, lastParams } = useFetcher({
    endpoint: members.memberships.index.url(props.member.id),
    resourceKey: 'memberMemberships',
    preserveScroll: true,
    preserveUrl: false,
});

// Setup sorter
const { sortState, handleSort } = useSorter({
    fetcher: { fetchData, lastParams },
    backend: true,
    defaultSort: '-started_at',
});

// Date formatter for display
const df = new DateFormatter('id-ID', { dateStyle: 'long' });
const todayDate = today(getLocalTimeZone());

// Form
const form = useForm({
    membership_id: '',
    started_at: dayjs().format('YYYY-MM-DD'),
});

// DateValue for calendar
const startedAtDate = ref<DateValue>(todayDate);

const selectedMembership = ref<Membership | null>(null);

const handleMembershipChange = (value: any) => {
    const strValue = String(value || '');
    form.membership_id = strValue;
    selectedMembership.value = props.memberships.find(m => m.id.toString() === strValue) || null;
};

const handleSubmit = () => {
    // Sync date from calendar to form
    if (startedAtDate.value) {
        form.started_at = dayjs(startedAtDate.value.toDate(getLocalTimeZone())).format('YYYY-MM-DD');
    }
    
    form.post(members.memberships.store.url(props.member.id), {
        onSuccess: () => {
            toast.success('Membership assigned successfully.');
            dialogOpen.value = false;
            form.reset();
            selectedMembership.value = null;
            startedAtDate.value = todayDate;
            refresh({}, true);
        },
        onError: () => {
            toast.error('Failed to assign membership.');
        },
    });
};

const columns = [
    { key: 'snapshot_membership_name', label: 'Membership', sortable: true, className: 'font-medium' },
    { key: 'started_at', label: 'Started', sortable: true },
    { key: 'expired_at', label: 'Expires', sortable: true },
    { key: 'snapshot_max_attendance_qty', label: 'Max Attendance', sortable: true },
    { key: 'remaining_qty', label: 'Remaining', sortable: true },
    { key: 'snapshot_price', label: 'Price', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
];

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'active': return 'default';
        case 'expired': return 'secondary';
        case 'cancelled': return 'destructive';
        default: return 'secondary';
    }
};

const formatDate = (date: string) => dayjs(date).format('DD MMM YYYY');
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`${member.name} - Memberships`" />

        <div class="flex h-full flex-col gap-4 p-4">
            <!-- Filter with Dialog -->
            <div class="flex items-center justify-between gap-2">
                <ResourceTableFilter
                    search-placeholder="Search memberships..."
                    :search-fields="['snapshot_membership_name']"
                    :show-add-button="false"
                    :refresh="refresh"
                />

                <!-- Add membership Dialog - only if user can manage -->
                <Dialog v-if="can.manage_member_memberships" v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <PlusCircle class="w-4 h-4" />
                            Add membership
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>Add membership</DialogTitle>
                            <DialogDescription>
                                Assign a membership to {{ member.name }}.
                            </DialogDescription>
                        </DialogHeader>

                        <form @submit.prevent="handleSubmit" class="space-y-4 py-4">
                            <!-- Membership Select -->
                            <div class="space-y-2">
                                <Label for="membership">Membership</Label>
                                <Select
                                    :model-value="form.membership_id"
                                    @update:model-value="handleMembershipChange"
                                >
                                    <SelectTrigger id="membership">
                                        <SelectValue placeholder="Select a membership" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="membership in memberships"
                                            :key="membership.id"
                                            :value="membership.id.toString()"
                                        >
                                            {{ membership.name }} - {{ formatCurrency(membership.price, 'IDR') }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.membership_id" class="text-sm text-destructive">
                                    {{ form.errors.membership_id }}
                                </p>
                            </div>

                            <!-- Membership Info Preview -->
                            <div v-if="selectedMembership" class="rounded-lg bg-muted p-3 text-sm space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Duration:</span>
                                    <span class="font-medium">{{ selectedMembership.duration_days }} days</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Max Attendance:</span>
                                    <span class="font-medium">{{ selectedMembership.max_attendance_qty ?? 'Unlimited' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Price:</span>
                                    <span class="font-medium">{{ formatCurrency(selectedMembership.price, 'IDR') }}</span>
                                </div>
                            </div>

                            <!-- Start Date with Calendar Popover -->
                            <div class="space-y-2">
                                <Label for="started_at">Start Date</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn('w-full justify-start text-left font-normal', !startedAtDate && 'text-muted-foreground')"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ startedAtDate ? df.format(startedAtDate.toDate(getLocalTimeZone())) : 'Pick a date' }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0" align="start">
                                        <Calendar
                                            v-model="startedAtDate"
                                            :default-placeholder="todayDate"
                                            layout="month-and-year"
                                            initial-focus
                                        />
                                    </PopoverContent>
                                </Popover>
                                <p v-if="form.errors.started_at" class="text-sm text-destructive">
                                    {{ form.errors.started_at }}
                                </p>
                            </div>

                            <DialogFooter>
                                <Button type="submit" :disabled="form.processing || !form.membership_id">
                                    {{ form.processing ? 'Saving...' : 'Add membership' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Table -->
            <ResourceTable
                :data="resource || props.memberMemberships"
                :columns="columns"
                :sort-state="sortState"
                :handle-sort="handleSort"
                :refresh="refresh"
                resource-name="membership"
                :delete-route="can.manage_member_memberships 
                    ? (item: any) => ({ url: members.memberships.destroy.url({ member: member.id, memberMembership: item.id }) })
                    : undefined"
            >
                <template #cell-started_at="{ value }">
                    {{ formatDate(value) }}
                </template>
                <template #cell-expired_at="{ value }">
                    {{ formatDate(value) }}
                </template>
                <template #cell-snapshot_max_attendance_qty="{ value }">
                    <div class="flex items-center gap-2">
                        <CalendarCheck class="w-4 h-4 text-muted-foreground" />
                        <span>{{ value ?? 'Unlimited' }}</span>
                    </div>
                </template>
                <template #cell-remaining_qty="{ value }">
                    <span v-if="value !== null">{{ value }}</span>
                    <Infinity v-else class="w-4 h-4 text-muted-foreground" />
                </template>
                <template #cell-snapshot_price="{ value }">
                    {{ formatCurrency(value, 'IDR') }}
                </template>
                <template #cell-status="{ value }">
                    <Badge :variant="getStatusVariant(value)" class="text-xs rounded-md capitalize">
                        {{ value }}
                    </Badge>
                </template>
            </ResourceTable>
        </div>
    </AppLayout>
</template>
