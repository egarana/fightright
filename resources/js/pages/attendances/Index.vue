<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import attendances from '@/routes/attendances';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Attendances',
        href: attendances.index().url,
    },
];
</script>

<template>
    <Head title="Attendances" />

    <AppLayout :breadcrumbs="breadcrumbs">
        
    </AppLayout>
</template>
