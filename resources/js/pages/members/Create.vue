<script setup lang="ts">
import members from '@/routes/members';
import { ref } from 'vue';

import { useFormNotifications } from '@/composables/useFormNotifications';
import BaseFormPage from '@/components/BaseFormPage.vue';
import FormField from '@/components/FormField.vue';
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

const breadcrumbs = [
    { title: 'Members', href: members.index.url() },
    { title: 'Create Member', href: members.create.url() },
];

const { onSuccess, onError } = useFormNotifications({
    resourceName: 'member',
    action: 'create',
});

// Form fields
const name = ref('');
const email = ref('');
const phone = ref<PhoneData | null>(null);
const address = ref('');
</script>

<template>
    <BaseFormPage
        title="Create Member"
        :breadcrumbs="breadcrumbs"
        :action="members.store.url()"
        method="post"
        :onSuccess="onSuccess"
        :onError="onError"
    >
        <template #default="{ errors, processing }">
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
                test-id="create-member-button"
                label="Create Member"
            />
        </template>
    </BaseFormPage>
</template>
