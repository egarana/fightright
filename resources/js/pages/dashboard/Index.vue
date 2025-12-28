<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import dashboard from '@/routes/dashboard';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Users, ContactRound, IdCard, CalendarCheck, DollarSign, UserCheck, Clock, AlertTriangle } from 'lucide-vue-next';
import { formatCurrency } from '@/helpers/currency';
import { computed } from 'vue';

interface Stats {
    members?: {
        total: number;
        new_this_month: number;
    };
    memberships?: {
        active: number;
        expiring_soon: number;
    };
    attendances?: {
        today: number;
        currently_in: number;
    };
    revenue?: {
        total: number;
        this_month: number;
    };
    users?: {
        total: number;
    };
}

const props = defineProps<{
    stats: Stats;
}>();

const page = usePage();
const can = computed(() => (page.props.auth as any)?.can ?? {});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard.index.url(),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Stats Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Members Card -->
                <Card class="gap-5 shadow-xs">
                    <CardHeader class="flex items-center gap-3">
                        <div class="bg-primary/10 text-primary flex size-8 shrink-0 items-center justify-center rounded-md">
                            <ContactRound class="h-4 w-4" />
                        </div>
                        <span class="text-2xl">{{ stats.members?.total ?? 0 }}</span>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-1.5">
                        <div class="font-semibold text-sm">Members</div>
                        <div class="flex items-center gap-2">
                            <div class="text-sm">
                                {{ stats.members?.new_this_month ?? 0 }} <span class="text-muted-foreground">new this month</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Active Memberships Card -->
                <Card class="gap-5 shadow-xs">
                    <CardHeader class="flex items-center gap-3">
                        <div class="bg-primary/10 text-primary flex size-8 shrink-0 items-center justify-center rounded-md">
                            <IdCard class="h-4 w-4" />
                        </div>
                        <span class="text-2xl">{{ stats.memberships?.active ?? 0 }}</span>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-1.5">
                        <div class="font-semibold text-sm">Active Memberships</div>
                        <div class="flex items-center gap-2">
                            <div>
                                <AlertTriangle v-if="(stats.memberships?.expiring_soon ?? 0) > 0" class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="text-sm">
                                {{ stats.memberships?.expiring_soon ?? 0 }} <span class="text-muted-foreground">expiring soon</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Today's Attendance Card -->
                <Card class="gap-5 shadow-xs">
                    <CardHeader class="flex items-center gap-3">
                        <div class="bg-primary/10 text-primary flex size-8 shrink-0 items-center justify-center rounded-md">
                            <CalendarCheck class="h-4 w-4" />
                        </div>
                        <span class="text-2xl">{{ stats.attendances?.today ?? 0 }}</span>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-1.5">
                        <div class="font-semibold text-sm">Daily Attendance</div>
                        <div class="flex items-center gap-2">
                            <div>
                                <Clock class="inline h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="text-sm">
                                {{ stats.attendances?.currently_in ?? 0 }} <span class="text-muted-foreground">currently training</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Revenue Card - Only for super-admin/owner -->
                <Card v-if="can.view_revenue && stats.revenue" class="gap-5 shadow-xs">
                    <CardHeader class="flex items-center gap-3">
                        <div class="bg-primary/10 text-primary flex size-8 shrink-0 items-center justify-center rounded-md">
                            <DollarSign class="h-4 w-4" />
                        </div>
                        <span class="text-2xl">{{ formatCurrency(stats.revenue.this_month, 'IDR') }}</span>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-1.5">
                        <div class="font-semibold text-sm">Revenue This Month</div>
                        <div class="flex items-center gap-2">
                            <div class="text-sm">
                                Total: <span class="text-muted-foreground">{{ formatCurrency(stats.revenue.total, 'IDR') }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Users Card - Only for super-admin -->
                <Card v-if="can.manage_users && stats.users" class="gap-5 shadow-xs">
                    <CardHeader class="flex items-center gap-3">
                        <div class="bg-primary/10 text-primary flex size-8 shrink-0 items-center justify-center rounded-md">
                            <Users class="h-4 w-4" />
                        </div>
                        <span class="text-2xl">{{ stats.users.total }}</span>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-1.5">
                        <div class="font-semibold text-sm">System Users</div>
                        <div class="flex items-center gap-2">
                            <div class="text-sm">
                                <span class="text-muted-foreground">Administrators & staff</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
