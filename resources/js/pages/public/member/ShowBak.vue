<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { CalendarCheck, Clock, User, ShieldCheck } from 'lucide-vue-next';
import dayjs from 'dayjs';

defineProps<{
    member: any;
    activeMemberships: any[];
}>();

const formatDate = (date: string) => dayjs(date).format('DD MMM YYYY');

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};
</script>

<template>
    <Head :title="`${member.name} - Member Profile`" />

    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 flex flex-col items-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-sm space-y-6">
            <!-- Header Logo -->
            <div class="flex justify-center mb-8">
                <AppLogo class="h-10 w-auto" />
            </div>

            <!-- Member Profile Card -->
            <Card class="border-0 shadow-lg ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-primary/10 to-primary/5"></div>
                <CardContent class="relative pt-0 pb-8 px-6">
                    <div class="flex flex-col items-center -mt-12 mb-6">
                        <Avatar class="h-24 w-24 ring-4 ring-white dark:ring-neutral-950 shadow-md">
                            <AvatarImage :src="member.profile_photo_url" :alt="member.name" />
                            <AvatarFallback class="text-xl font-bold bg-primary/10 text-primary">
                                {{ getInitials(member.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <h1 class="mt-4 text-2xl font-bold text-center tracking-tight">{{ member.name }}</h1>
                        <p class="text-sm text-muted-foreground font-mono mt-1 bg-muted/50 px-3 py-1 rounded-full">
                            {{ member.member_code }}
                        </p>
                    </div>

                    <div class="grid gap-4 py-2">
                        <div class="flex items-center gap-3 text-sm">
                            <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <User class="h-4 w-4 text-primary" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-muted-foreground text-xs uppercase tracking-wider font-semibold">Joined</span>
                                <span class="font-medium">{{ formatDate(member.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Active Memberships -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold px-1 flex items-center gap-2">
                    <ShieldCheck class="w-5 h-5 text-primary" />
                    Active Memberships
                </h3>
                
                <div v-if="activeMemberships.length === 0" class="text-center py-8 px-4 bg-muted/30 rounded-lg border border-dashed text-muted-foreground">
                    No active memberships found.
                </div>

                <div v-else class="space-y-3">
                    <Card v-for="item in activeMemberships" :key="item.id" class="overflow-hidden border-l-4 border-l-primary hover:shadow-md transition-shadow">
                        <CardHeader class="p-4 pb-2">
                            <div class="flex justify-between items-start gap-2">
                                <CardTitle class="text-base font-bold leading-tight">
                                    {{ item.snapshot_membership_name }}
                                </CardTitle>
                                <Badge variant="outline" class="font-mono text-xs shrink-0">
                                    {{ item.remaining_qty === null ? '∞' : item.remaining_qty }} left
                                </Badge>
                            </div>
                            <CardDescription class="text-xs flex items-center gap-1 mt-1">
                                expires {{ formatDate(item.expired_at) }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="p-4 pt-2">
                            <div class="grid grid-cols-2 gap-2 text-xs text-muted-foreground">
                                <div class="flex items-center gap-1.5 bg-muted/50 p-2 rounded">
                                    <Clock class="w-3.5 h-3.5 opacity-70" />
                                    <span>{{ item.snapshot_duration_days }} Days</span>
                                </div>
                                <div class="flex items-center gap-1.5 bg-muted/50 p-2 rounded">
                                    <CalendarCheck class="w-3.5 h-3.5 opacity-70" />
                                    <span>{{ item.snapshot_max_attendance_qty === null ? 'Unlimited' : item.snapshot_max_attendance_qty + ' Visits' }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-muted-foreground py-4">
                &copy; {{ new Date().getFullYear() }} FightRight Gym.
            </div>
        </div>
    </div>
</template>
