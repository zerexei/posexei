<script setup lang="ts">
import SidebarGroup from '@/components/ui/sidebar/SidebarGroup.vue';
import SidebarGroupLabel from '@/components/ui/sidebar/SidebarGroupLabel.vue';
import SidebarMenu from '@/components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from '@/components/ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from '@/components/ui/sidebar/SidebarMenuItem.vue';
import { useSidebar } from '@/components/ui/sidebar/utils';
import type { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    items: NavItem[];
    label?: string;
}>();

const page = usePage();
const { state } = useSidebar();

const isCurrentRoute = (href: string | { url: string }) => {
    const url = typeof href === 'string' ? href : href.url;
    return page.url === url;
};
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel v-if="label" class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/50 mb-2 px-2">
            {{ label }}
        </SidebarGroupLabel>
        <SidebarMenu class="gap-1">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child 
                    :is-active="isCurrentRoute(item.href)" 
                    :tooltip="item.title"
                    class="h-10 rounded-xl px-3 transition-all hover:bg-muted group-data-[state=collapsed]:px-0"
                >
                    <Link :href="item.href" class="flex items-center gap-3">
                        <component 
                            :is="item.icon" 
                            class="size-4.5 stroke-[2.5px] transition-colors"
                            :class="isCurrentRoute(item.href) ? 'text-primary' : 'text-muted-foreground/60 group-hover:text-foreground'"
                        />
                        <span 
                            class="text-[11px] font-black uppercase tracking-widest transition-colors"
                            :class="isCurrentRoute(item.href) ? 'text-foreground' : 'text-muted-foreground/70 group-hover:text-foreground'"
                        >{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

