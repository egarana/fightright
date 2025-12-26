<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { XCircle, Eye } from 'lucide-vue-next';
import impersonate from '@/routes/impersonate';

const page = usePage();

const impersonation = computed(() => (page.props as any).impersonation ?? { active: false });

const stopImpersonating = () => {
    router.post(impersonate.stop.url());
};
</script>

<template>
    <div v-if="impersonation.active" class="p-2">
        <div class="rounded-md bg-amber-50 p-3 border border-amber-200">
            <div class="flex items-center gap-2 mb-2 text-amber-900">
                <Eye class="h-3 w-3" />
                <span class="text-[10px] font-bold uppercase tracking-wider">Impersonating</span>
            </div>
            <div class="text-sm font-medium text-amber-950 truncate mb-3" :title="(page.props.auth as any)?.user?.name">
                {{ (page.props.auth as any)?.user?.name }}
            </div>
            <Button
                variant="outline"
                size="sm"
                class="w-full h-7 bg-white border-amber-300 hover:bg-amber-100 text-amber-900 text-xs"
                @click="stopImpersonating"
            >
                <XCircle class="h-3 w-3 mr-1" />
                Stop
            </Button>
        </div>
    </div>
</template>
