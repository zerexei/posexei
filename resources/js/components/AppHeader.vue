<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import NavigationMenu from '@/components/ui/navigation-menu/NavigationMenu.vue';
import NavigationMenuItem from '@/components/ui/navigation-menu/NavigationMenuItem.vue';
import NavigationMenuList from '@/components/ui/navigation-menu/NavigationMenuList.vue';
import { navigationMenuTriggerStyle } from '@/components/ui/navigation-menu/NavigationMenu.vue';
import Sheet from '@/components/ui/sheet/Sheet.vue';
import SheetContent from '@/components/ui/sheet/SheetContent.vue';
import SheetHeader from '@/components/ui/sheet/SheetHeader.vue';
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue';
import SheetTrigger from '@/components/ui/sheet/SheetTrigger.vue';
import Tooltip from '@/components/ui/tooltip/Tooltip.vue';
import TooltipContent from '@/components/ui/tooltip/TooltipContent.vue';
import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
import TooltipTrigger from '@/components/ui/tooltip/TooltipTrigger.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useInitials } from '@/composables/useInitials';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Menu, Search } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { getInitials } = useInitials();
const page = usePage();
const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(() => (url: NonNullable<InertiaLinkProps['href']>) => page.url === (typeof url === 'string' ? url : url.url));

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(typeof url === 'string' ? url : url.url) ? 'text-foreground font-black' : 'text-muted-foreground/60',
);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/zerexei/posexei',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://github.com/zerexei/posexei',
        icon: BookOpen,
    },
];
</script>

<template>
    <div>
        <div class="border-b border-border/50 bg-background/80 backdrop-blur-md sticky top-0 z-50">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="mr-2 h-9 w-9 rounded-xl">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6 rounded-r-[2rem]">
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader class="flex justify-start text-left mb-6">
                                <AppLogo />
                            </SheetHeader>
                            <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6">
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-widest hover:bg-muted transition-colors"
                                        :class="activeItemStyles(item.href)"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="h-4 w-4 stroke-[2.5px]" />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                                <div class="flex flex-col space-y-4 pt-6 border-t border-border/50">
                                    <a
                                        v-for="item in rightNavItems"
                                        :key="item.title"
                                        :href="typeof item.href === 'string' ? item.href : item.href?.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground/60 hover:text-primary transition-colors"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
                                        <span>{{ item.title }}</span>
                                    </a>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link :href="dashboard()" class="flex items-center gap-x-2">
                    <AppLogo />
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <NavigationMenuItem v-for="(item, index) in mainNavItems" :key="index" class="relative flex h-full items-center">
                                <Link
                                    :class="[navigationMenuTriggerStyle(), activeItemStyles(item.href), 'h-9 cursor-pointer px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:bg-muted']"
                                    :href="item.href"
                                >
                                    <component v-if="item.icon" :is="item.icon" class="mr-2 h-3.5 w-3.5 stroke-[2.5px]" />
                                    {{ item.title }}
                                </Link>
                                <div
                                    v-if="isCurrentRoute(item.href)"
                                    class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-8 bg-primary rounded-full"
                                ></div>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <Button variant="ghost" size="icon" class="group h-9 w-9 cursor-pointer rounded-xl">
                            <Search class="size-4.5 opacity-60 group-hover:opacity-100 transition-opacity" />
                        </Button>

                        <div class="hidden space-x-1 lg:flex">
                            <template v-for="item in rightNavItems" :key="item.title">
                                <TooltipProvider :delay-duration="0">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <Button variant="ghost" size="icon" as-child class="group h-9 w-9 cursor-pointer rounded-xl">
                                                <a
                                                    :href="typeof item.href === 'string' ? item.href : item.href?.url"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <span class="sr-only">{{ item.title }}</span>
                                                    <component :is="item.icon" class="size-4.5 opacity-60 group-hover:opacity-100 transition-opacity" />
                                                </a>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent class="rounded-lg text-[10px] font-black uppercase tracking-widest px-3 py-1.5 border-border/50 shadow-xl">
                                            <p>{{ item.title }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </template>
                        </div>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-9 rounded-full focus-within:ring-2 focus-within:ring-primary/20 transition-all p-0"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full border border-border/50 shadow-sm">
                                    <AvatarImage v-if="auth.user.avatar" :src="'/storage/' + auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="rounded-full bg-muted font-black text-[10px] text-foreground">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-64 rounded-[1.25rem] shadow-2xl border-border/60 p-2 mt-2">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-b border-border/40 bg-muted/5">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
