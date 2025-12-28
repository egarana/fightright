<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
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
    // Use encoded_url for obfuscated URLs (hash-based)
    const checkInUrl = publicRoutes.member.show.url(props.member.encoded_url) + '/check-in';

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

const isExpired = (membership: any) => {
    return membership.is_expired || membership.status === 'expired';
};

// Get status badge variant
const getStatusVariant = (membership: any) => {
    if (isExpired(membership)) return 'destructive';
    if (membership.status === 'cancelled') return 'secondary';
    return 'default';
};

const getDisplaySlots = (membership: any) => {
    // If limited, show 1 to max qty (ascending)
    if (membership.snapshot_max_attendance_qty !== null) {
        return Array.from({ length: membership.snapshot_max_attendance_qty }, (_, i) => i + 1);
    }
    // If unlimited, show from (used + 1) down to 1 (descending)
    const total = (membership.used_count || 0) + 1;
    return Array.from({ length: total }, (_, i) => total - i);
}; 

const getStatusLabel = (membership: any) => {
    if (isExpired(membership)) return 'Expired';
    if (membership.status === 'cancelled') return 'Cancelled';
    return 'Active';
};
</script>

<template>
    <Head :title="`${member.name} - Member Profile`" />

    <div class="min-h-svh bg-neutral-50 dark:bg-neutral-950">
        <div class="w-full max-w-sm bg-background border-x min-h-svh mx-auto flex flex-col">
            <!-- Header -->
            <div class="py-10 border-b">
                <AppLogoIcon class="w-36 h-36 mx-auto mb-6 dark:invert" />
                <h1 class="text-center text-lg">{{ member.name }}</h1>
                <Badge
                    variant="secondary"
                    class="rounded-md mx-auto block w-fit font-mono text-muted-foreground text-sm mt-2"
                >
                    {{ member.member_code }}
                </Badge>
                <div class="text-xs flex items-center text-muted-foreground gap-1 justify-center mt-4">
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
                        <AccordionTrigger class="px-6 h-20 hover:bg-muted hover:no-underline hover:cursor-pointer flex items-center gap-6">
                            <div class="flex flex-col items-start text-left font-normal gap-1">
                                <div 
                                    class="line-clamp-1"
                                    :class="{ 'opacity-20': isExpired(membership) }"
                                >{{ membership.snapshot_membership_name }}</div>
                                <div 
                                    class="text-xs text-muted-foreground font-light"
                                    :class="{ 'opacity-30': isExpired(membership) }">
                                    Status: {{ getStatusLabel(membership) }}, Expires: {{ formatDate(membership.expired_at) }}
                                </div>
                            </div>
                        </AccordionTrigger>
                        <AccordionContent class="p-0">
                            <!-- Unified session slots view for both limited and unlimited -->
                            <div>
                                <div
                                    v-for="slot in getDisplaySlots(membership)"
                                    :key="slot"
                                    class="flex items-center h-20 px-6 border-t border-dashed gap-x-3"
                                    :class="{ 'bg-muted/40': isSlotUsed(membership, slot) || isExpired(membership) }"
                                >
                                    <AppLogoIcon 
                                        class="w-8 h-8"
                                        :class="{ 'opacity-20': isSlotUsed(membership, slot) || isExpired(membership) }"
                                    />
                                    <div 
                                        class="flex flex-col gap-1 pe-3"
                                        :class="{ 'opacity-40': isSlotUsed(membership, slot) || isExpired(membership) }"
                                    >
                                        <div class="flex items-center gap-1.5">
                                            <span
                                                :class="{ 'opacity-50': isSlotUsed(membership, slot) || isExpired(membership) }"
                                            >Session #{{ slot }}</span>
                                            <span 
                                                class="text-xs text-muted-foreground"
                                            >
                                                ({{ isSlotUsed(membership, slot) ? 'Used' : (isExpired(membership) ? 'Expired' : 'Available') }})
                                            </span>
                                        </div>
                                        <span v-if="isSlotUsed(membership, slot) && getAttendanceForSlot(membership, slot)" class="text-xs text-muted-foreground line-clamp-1">
                                            {{ formatDateTime(getAttendanceForSlot(membership, slot).check_in_at) }}
                                            <template v-if="getAttendanceForSlot(membership, slot).snapshot_recorded_by_name">
                                                by {{ getAttendanceForSlot(membership, slot).snapshot_recorded_by_name }}
                                            </template>
                                        </span>
                                    </div>
                                    <div 
                                        v-if="isAdmin" 
                                        class="flex items-center gap-2 ms-auto"
                                        :class="{ 'opacity-10': isSlotUsed(membership, slot) || isExpired(membership) }"
                                    >
                                        <!-- Spinner only shows on the slot being checked in -->
                                        <Spinner v-if="loadingStates[membership.id] && slot === membership.used_count + 1" class="text-muted-foreground" />
                                        <Switch
                                            :model-value="isSlotUsed(membership, slot)"
                                            :disabled="slot !== membership.used_count + 1 || loadingStates[membership.id] || isExpired(membership)"
                                            @update:model-value="handleCheckIn(membership)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>

                <div v-if="memberships.length === 0" class="h-20 flex items-center justify-center text-muted-foreground border-b text-sm">
                    No memberships found.
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-muted-foreground py-8 mt-auto">
                {{ new Date().getFullYear() }} Fight Right
            </div>
        </div>
    </div>
</template>
