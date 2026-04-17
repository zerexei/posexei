<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import SidebarTrigger from '@/components/ui/sidebar/SidebarTrigger.vue';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-border/40 bg-background/80 backdrop-blur-md sticky top-0 z-10 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1 rounded-xl hover:bg-muted" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <div class="flex items-center gap-2 ml-2">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </div>
            </template>
        </div>
    </header>
</template>
