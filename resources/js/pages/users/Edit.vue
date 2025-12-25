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

interface User {
    id: number;
    name: string;
    email: string;
    roles?: Role[];
}

interface Props {
    user: User;
    roles: Role[];
}

const props = defineProps<Props>();

const breadcrumbs = [
    { title: 'Users', href: users.index.url() },
    { title: 'Edit User', href: users.edit.url(props.user.id) },
];

const { onSuccess, onError } = useFormNotifications({
    resourceName: 'user',
    action: 'update',
});

// Form fields - initialize with user data
const name = ref(props.user.name);
const email = ref(props.user.email);
const password = ref('');
const passwordConfirmation = ref('');
const role = ref(props.user.roles?.[0]?.name || '');
</script>

<template>
    <BaseFormPage
        title="Edit User"
        :breadcrumbs="breadcrumbs"
        :action="users.update.url(user.id)"
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
                label="New Password"
                type="password"
                :tabindex="3"
                autocomplete="new-password"
                placeholder="Leave empty to keep current"
                help-text="Leave empty to keep the current password"
                v-model="password"
                :error="errors.password"
            />

            <FormField
                id="password_confirmation"
                label="Confirm New Password"
                type="password"
                :tabindex="4"
                autocomplete="new-password"
                placeholder="Confirm new password"
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
                test-id="update-user-button"
                label="Update User"
            />
        </template>
    </BaseFormPage>
</template>
