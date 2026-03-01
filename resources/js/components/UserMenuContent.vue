<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import DropdownMenuGroup from '@/components/ui/dropdown-menu/DropdownMenuGroup.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator class="bg-border/50" />
    <DropdownMenuGroup class="p-1">
        <DropdownMenuItem :as-child="true" class="rounded-xl h-9 px-3 focus:bg-primary/5 transition-colors cursor-pointer group">
            <Link class="flex w-full items-center" :href="edit()" prefetch as="button">
                <Settings class="mr-2 h-3.5 w-3.5 opacity-50 group-hover:opacity-100 group-hover:text-primary transition-all" />
                <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground group-hover:text-foreground">Settings</span>
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator class="bg-border/50" />
    <div class="p-1">
        <DropdownMenuItem :as-child="true" class="rounded-xl h-9 px-3 focus:bg-destructive/5 transition-colors cursor-pointer group">
            <Link class="flex w-full items-center" :href="logout()" @click="handleLogout" as="button">
                <LogOut class="mr-2 h-3.5 w-3.5 opacity-50 group-hover:opacity-100 group-hover:text-destructive transition-all" />
                <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground group-hover:text-destructive">Log out</span>
            </Link>
        </DropdownMenuItem>
    </div>
</template>
