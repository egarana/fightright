<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Switch } from '@/components/ui/switch';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import { Head, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref, computed } from 'vue';
import publicRoutes from '@/routes/public';

const props = defineProps<{
    member: any;
    memberships: any[];
    isAdmin: boolean;
}>();

const formatDate = (date: string) => dayjs(date).format('DD MMM YYYY');
const formatDateTime = (date: string) => dayjs(date).format('DD MMM YYYY [at] HH:mm');

// Get attendance record for a specific slot
const getAttendanceForSlot = (membership: any, slotIndex: number) => {
    if (!membership.attendances || membership.attendances.length < slotIndex) {
        return null;
    }
    return membership.attendances[slotIndex - 1];
};

// Check if slot is used
const isSlotUsed = (membership: any, slotIndex: number) => {
    return slotIndex <= membership.used_count;
};

// Track loading state per membership
const loadingStates = ref<Record<number, boolean>>({});

// All accordions open by default
const defaultOpenAccordions = computed(() =>
    props.memberships.map((m) => `membership-${m.id}`)
);

const handleCheckIn = (membership: any) => {
    if (!props.isAdmin) return;
    if (loadingStates.value[membership.id]) return;

    loadingStates.value[membership.id] = true;
    
    // Use wayfinder route helper to get correct URL with proper domain
    const checkInUrl = publicRoutes.member.show.url(props.member.member_code) + '/check-in';

    router.post(
        checkInUrl,
        { member_membership_id: membership.id },
        {
            preserveScroll: true,
            onFinish: () => {
                loadingStates.value[membership.id] = false;
            },
        }
    );
};

// Get status badge variant
const getStatusVariant = (membership: any) => {
    if (membership.is_expired || membership.status === 'expired') return 'destructive';
    if (membership.status === 'cancelled') return 'secondary';
    return 'default';
};

const getStatusLabel = (membership: any) => {
    if (membership.is_expired || membership.status === 'expired') return 'Expired';
    if (membership.status === 'cancelled') return 'Cancelled';
    return 'Active';
};
</script>

<template>
    <Head :title="`${member.name} - Member Profile`" />

    <div class="min-h-svh bg-neutral-50 dark:bg-neutral-950">
        <div class="w-full max-w-sm bg-background border-x min-h-svh mx-auto flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b">
                <h1 class="text-xl font-bold text-center">{{ member.name }}</h1>
                <Badge
                    variant="secondary"
                    class="rounded-md mx-auto block w-fit font-mono text-muted-foreground text-base mt-1"
                >
                    {{ member.member_code }}
                </Badge>
                <div class="text-xs flex items-center text-muted-foreground gap-1 justify-center mt-1">
                    <span>Joined:</span>
                    <span>{{ formatDate(member.created_at) }}</span>
                </div>
            </div>

            <!-- Memberships Accordion -->
            <div class="flex-1 overflow-auto">
                <Accordion type="multiple" :default-value="defaultOpenAccordions" class="w-full">
                    <AccordionItem
                        v-for="membership in memberships"
                        :key="membership.id"
                        :value="`membership-${membership.id}`"
                    >
                        <AccordionTrigger class="px-4">
                            <div class="flex flex-col items-start text-left">
                                <span class="font-medium">{{ membership.snapshot_membership_name }}</span>
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <Badge :variant="getStatusVariant(membership)" class="text-xs">
                                        {{ getStatusLabel(membership) }}
                                    </Badge>
                                    <span>{{ formatDate(membership.expired_at) }}</span>
                                </div>
                            </div>
                        </AccordionTrigger>
                        <AccordionContent>
                            <!-- Only show session slots if qty is limited -->
                            <div v-if="membership.snapshot_max_attendance_qty !== null" class="divide-y">
                                <div
                                    v-for="slot in membership.snapshot_max_attendance_qty"
                                    :key="slot"
                                    class="flex items-center justify-between px-4 py-3"
                                    :class="{ 'bg-muted': isSlotUsed(membership, slot) }"
                                >
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium">Session #{{ slot }}</span>
                                            <span class="text-xs text-muted-foreground">-</span>
                                            <span 
                                                class="text-xs"
                                                :class="isSlotUsed(membership, slot) ? 'text-green-600 dark:text-green-400' : 'text-muted-foreground'"
                                            >
                                                {{ isSlotUsed(membership, slot) ? 'Used' : 'Available' }}
                                            </span>
                                        </div>
                                        <span v-if="isSlotUsed(membership, slot) && getAttendanceForSlot(membership, slot)" class="text-xs text-muted-foreground">
                                            {{ formatDateTime(getAttendanceForSlot(membership, slot).check_in_at) }}
                                            <template v-if="getAttendanceForSlot(membership, slot).snapshot_recorded_by_name">
                                                · by {{ getAttendanceForSlot(membership, slot).snapshot_recorded_by_name }}
                                            </template>
                                        </span>
                                    </div>
                                    <div v-if="isAdmin" class="flex items-center gap-2">
                                        <!-- Spinner only shows on the slot being checked in -->
                                        <Spinner v-if="loadingStates[membership.id] && slot === membership.used_count + 1" class="text-muted-foreground" />
                                        <Switch
                                            :model-value="isSlotUsed(membership, slot)"
                                            :disabled="slot !== membership.used_count + 1 || loadingStates[membership.id]"
                                            @update:model-value="handleCheckIn(membership)"
                                        />
                                    </div>
                                </div>
                            </div>
                            <!-- Unlimited membership - just show summary -->
                            <div v-else class="px-4 py-3 text-sm text-muted-foreground">
                                Unlimited sessions
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>

                <div v-if="memberships.length === 0" class="p-4 text-center text-muted-foreground">
                    No memberships found.
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-muted-foreground py-4 border-t mt-auto">
                &copy; {{ new Date().getFullYear() }} Fight Right
            </div>
        </div>
    </div>
</template>
