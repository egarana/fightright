<script setup lang="ts">
import users from '@/routes/users';
import { ref } from 'vue';

import { useFormNotifications } from '@/composables/useFormNotifications';
import BaseFormPage from '@/components/BaseFormPage.vue';
import FormField from '@/components/FormField.vue';
import SubmitButton from '@/components/SubmitButton.vue';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';

interface Role {
    id: number;
    name: string;
}

interface Props {
    roles: Role[];
}

defineProps<Props>();

const breadcrumbs = [
    { title: 'Users', href: users.index.url() },
    { title: 'Create User', href: users.create.url() },
];

const { onSuccess, onError } = useFormNotifications({
    resourceName: 'user',
    action: 'create',
});

// Form fields
const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const role = ref('');
</script>

<template>
    <BaseFormPage
        title="Create User"
        :breadcrumbs="breadcrumbs"
        :action="users.store.url()"
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

            <FormField
                id="password"
                label="Password"
                type="password"
                :tabindex="3"
                autocomplete="new-password"
                placeholder="Minimum 8 characters"
                v-model="password"
                :error="errors.password"
            />

            <FormField
                id="password_confirmation"
                label="Confirm Password"
                type="password"
                :tabindex="4"
                autocomplete="new-password"
                placeholder="Confirm password"
                v-model="passwordConfirmation"
                :error="errors.password_confirmation"
            />

            <div class="grid gap-2">
                <Label for="role">Role</Label>
                <Select name="role" v-model="role">
                    <SelectTrigger :tabindex="5">
                        <SelectValue placeholder="Select a role" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="r in roles"
                            :key="r.id"
                            :value="r.name"
                        >
                            {{ r.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.role" />
            </div>

            <SubmitButton
                :processing="processing"
                :tabindex="6"
                test-id="create-user-button"
                label="Create User"
            />
        </template>
    </BaseFormPage>
</template>
