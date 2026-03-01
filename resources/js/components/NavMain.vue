<script setup lang="ts">
import SidebarGroup from '@/components/ui/sidebar/SidebarGroup.vue';
import SidebarGroupLabel from '@/components/ui/sidebar/SidebarGroupLabel.vue';
import SidebarMenu from '@/components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from '@/components/ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from '@/components/ui/sidebar/SidebarMenuItem.vue';
import { useSidebar } from '@/components/ui/sidebar/utils';
import type { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
const { state } = useSidebar();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="page.url === item.href" :tooltip="item.title">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

