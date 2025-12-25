<script setup lang="ts">
import members from '@/routes/members';
import { ref } from 'vue';

import { useFormNotifications } from '@/composables/useFormNotifications';
import BaseFormPage from '@/components/BaseFormPage.vue';
import FormField from '@/components/FormField.vue';
import DisabledFormField from '@/components/DisabledFormField.vue';
import PhoneInput from '@/components/PhoneInput.vue';
import SubmitButton from '@/components/SubmitButton.vue';

interface PhoneData {
    country: {
        country: string;
        countryName: string;
        code: string;
    };
    number: string;
}

interface Member {
    id: number;
    member_code: string;
    name: string;
    email: string;
    phone: PhoneData | null;
    address: string | null;
}

interface Props {
    member: Member;
}

const props = defineProps<Props>();

const breadcrumbs = [
    { title: 'Members', href: members.index.url() },
    { title: 'Edit Member', href: members.edit.url(props.member.id) },
];

const { onSuccess, onError } = useFormNotifications({
    resourceName: 'member',
    action: 'update',
});

// Form fields - initialize with member data
const name = ref(props.member.name);
const email = ref(props.member.email);
const phone = ref<PhoneData | null>(props.member.phone);
const address = ref(props.member.address || '');
</script>

<template>
    <BaseFormPage
        title="Edit Member"
        :breadcrumbs="breadcrumbs"
        :action="members.update.url(member.id)"
        method="put"
        :onSuccess="onSuccess"
        :onError="onError"
    >
        <template #default="{ errors, processing }">
            <DisabledFormField
                label="Member Code"
                :value="member.member_code"
            />

            <FormField
                id="name"
                label="Name"
                type="text"
                :tabindex="1"
                autocomplete="name"
                placeholder="e.g. John Doe"
                v-model="name"
                :error="errors.name"
            />

            <FormField
                id="email"
                label="Email"
                type="email"
                :tabindex="2"
                autocomplete="email"
                placeholder="e.g. john@example.com"
                v-model="email"
                :error="errors.email"
            />

            <PhoneInput
                name="phone"
                label="Phone"
                :tabindex="3"
                placeholder="81234567890"
                v-model="phone"
                :error="errors.phone"
            />

            <FormField
                id="address"
                label="Address"
                type="text"
                :tabindex="4"
                autocomplete="street-address"
                placeholder="e.g. Jl. Contoh No. 123"
                v-model="address"
                :error="errors.address"
            />

            <SubmitButton
                :processing="processing"
                :tabindex="5"
                test-id="update-member-button"
                label="Update Member"
            />
        </template>
    </BaseFormPage>
</template>
