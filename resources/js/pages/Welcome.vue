<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import dashboard from '@/routes/dashboard';
import login from '@/routes/login';

import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] justify-center lg:flex-row lg:p-8 dark:bg-[#0a0a0a]"
    >

        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row border"
            >
                <div
                    class="flex flex-col rounded-br-lg rounded-bl-lg bg-white p-6 text-[13px] leading-[20px] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC]"
                >
                    <h1 class="mb-1 font-semibold text-base lg:mb-2 lg:text-lg">Fight Right</h1>
                    <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A] lg:pe-20">
                        Welcome to the Fight Right administration portal.
                    </p>

                    <ul class="flex gap-3 text-sm leading-normal lg:mt-auto">
                        <li>
                            <Link
                                v-if="$page.props.auth.user"
                                :href="dashboard.index.url()"
                                class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                            >
                                Dashboard
                            </Link>

                            <Link
                                v-else
                                :href="login.store.url()"
                                class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                            >
                                Log in
                            </Link>
                        </li>
                    </ul>
                </div>
                <div
                    class="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg lg:mb-0 lg:aspect-4/3 lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg"
                >
                    <AppLogoIcon class="w-[200%] h-[200%] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-5 z-10" />
                    <AppLogoIcon class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20 w-3/4 h-3/4" />
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
