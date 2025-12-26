<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import dashboard from '@/routes/dashboard';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Members Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Members</CardTitle>
                        <ContactRound class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.members?.total ?? 0 }}</div>
                        <p class="text-xs text-muted-foreground">
                            +{{ stats.members?.new_this_month ?? 0 }} this month
                        </p>
                    </CardContent>
                </Card>

                <!-- Active Memberships Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Memberships</CardTitle>
                        <IdCard class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.memberships?.active ?? 0 }}</div>
                        <p class="text-xs text-muted-foreground flex items-center gap-1">
                            <AlertTriangle v-if="(stats.memberships?.expiring_soon ?? 0) > 0" class="h-3 w-3 text-orange-500" />
                            {{ stats.memberships?.expiring_soon ?? 0 }} expiring soon
                        </p>
                    </CardContent>
                </Card>

                <!-- Today's Attendance Card -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Today's Check-ins</CardTitle>
                        <CalendarCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.attendances?.today ?? 0 }}</div>
                        <p class="text-xs text-muted-foreground flex items-center gap-1">
                            <Clock class="h-3 w-3 text-green-500" />
                            {{ stats.attendances?.currently_in ?? 0 }} currently in gym
                        </p>
                    </CardContent>
                </Card>

                <!-- Revenue Card - Only for super-admin/owner -->
                <Card v-if="can.view_revenue && stats.revenue">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Revenue This Month</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(stats.revenue.this_month, 'IDR') }}</div>
                        <p class="text-xs text-muted-foreground">
                            Total: {{ formatCurrency(stats.revenue.total, 'IDR') }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Users Card - Only for super-admin -->
                <Card v-if="can.manage_users && stats.users">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">System Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users.total }}</div>
                        <p class="text-xs text-muted-foreground">
                            Administrators & staff
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions Section -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card class="col-span-full lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Welcome to FightRight</CardTitle>
                        <CardDescription>
                            Manage your gym members, memberships, and track attendance all in one place.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <a href="/attendances/check-in" 
                               class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">
                                <UserCheck class="h-4 w-4" />
                                Check-in Member
                            </a>
                            <a href="/members" 
                               class="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm font-medium hover:bg-accent">
                                <ContactRound class="h-4 w-4" />
                                View Members
                            </a>
                            <a href="/attendances/today" 
                               class="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm font-medium hover:bg-accent">
                                <CalendarCheck class="h-4 w-4" />
                                Today's Attendance
                            </a>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
