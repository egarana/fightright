<script setup lang="ts">
import members from '@/routes/members';
import BaseIndexPage from '@/components/BaseIndexPage.vue';

const config = {
    resourceName: 'Member',
    resourceNamePlural: 'Members',
    endpoint: members.index.url(),
    resourceKey: 'members',
    columns: [
        { key: 'member_code', label: 'Code', sortable: true, className: 'font-mono' },
        { key: 'name', label: 'Name', sortable: true, className: 'font-medium' },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'created_at', label: 'Created At', sortable: true },
        { key: 'updated_at', label: 'Updated At', sortable: true },
    ],
    searchFields: ['member_code', 'name', 'email'],
    showTable: true,
    breadcrumbs: [
        { title: 'Members', href: members.index.url() },
    ],
    addButtonRoute: members.create.url(),
    editRoute: (item: any) => members.edit.url(item.id),
    deleteRoute: (item: any) => ({ url: members.destroy.url(item.id) }),
};
</script>

<template>
    <BaseIndexPage title="Members" :config="config" />
</template>
