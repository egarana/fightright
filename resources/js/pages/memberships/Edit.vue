<script setup lang="ts">
import memberships from '@/routes/memberships';
import { ref } from 'vue';

import { useFormNotifications } from '@/composables/useFormNotifications';
import BaseFormPage from '@/components/BaseFormPage.vue';
import FormField from '@/components/FormField.vue';
import SubmitButton from '@/components/SubmitButton.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Item, ItemContent, ItemTitle, ItemDescription, ItemActions } from '@/components/ui/item';
import InputError from '@/components/InputError.vue';

interface Membership {
    id: number;
    name: string;
    description: string | null;
    max_attendance_qty: number | null;
    duration_days: number;
    price: number;
    is_active: boolean;
}

interface Props {
    membership: Membership;
}

const props = defineProps<Props>();

const breadcrumbs = [
    { title: 'Memberships', href: memberships.index.url() },
    { title: 'Edit Membership', href: memberships.edit.url(props.membership.id) },
];

const { onSuccess, onError } = useFormNotifications({
    resourceName: 'membership',
    action: 'update',
});

// Form fields - initialize with membership data
const name = ref(props.membership.name);
const description = ref(props.membership.description || '');
const maxAttendanceQty = ref<number | null>(props.membership.max_attendance_qty);
const durationDays = ref<number | null>(props.membership.duration_days);
const price = ref<number | null>(props.membership.price);
const isActive = ref(props.membership.is_active);
</script>

<template>
    <BaseFormPage
        title="Edit Membership"
        :breadcrumbs="breadcrumbs"
        :action="memberships.update.url(membership.id)"
        method="put"
        :onSuccess="onSuccess"
        :onError="onError"
    >
        <template #default="{ errors, processing }">
            <FormField
                id="name"
                label="Name"
                type="text"
                :tabindex="1"
                autocomplete="off"
                placeholder="e.g. Gold - Premium"
                v-model="name"
                :error="errors.name"
            />

            <div class="grid gap-2">
                <Label for="description">Description (Optional)</Label>
                <Textarea
                    id="description"
                    name="description"
                    placeholder="Additional details about the membership..."
                    v-model="description"
                    rows="4"
                />
                <InputError :message="errors.description" />
            </div>

            <FormField
                id="duration_days"
                label="Duration (Days)"
                type="number"
                :tabindex="3"
                autocomplete="off"
                placeholder="e.g. 30"
                v-model="durationDays"
                :error="errors.duration_days"
            />

            <FormField
                id="max_attendance_qty"
                label="Max Attendance (Leave empty for unlimited)"
                type="number"
                :tabindex="4"
                autocomplete="off"
                placeholder="e.g. 12"
                v-model="maxAttendanceQty"
                :error="errors.max_attendance_qty"
            />

            <FormField
                id="price"
                label="Price (IDR)"
                type="number"
                :tabindex="5"
                autocomplete="off"
                placeholder="e.g. 500000"
                v-model="price"
                :error="errors.price"
            />

            <Item variant="outline">
                <ItemContent>
                    <ItemTitle>Active</ItemTitle>
                    <ItemDescription>When enabled, this membership will be visible and can be applied to members. Disable to temporarily hide without deleting.</ItemDescription>
                </ItemContent>
                <ItemActions>
                    <Switch id="is_active" v-model:checked="isActive" />
                </ItemActions>
            </Item>

            <SubmitButton
                :processing="processing"
                :tabindex="7"
                test-id="update-membership-button"
                label="Update Membership"
            />
        </template>
    </BaseFormPage>
</template>
