<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { ShieldCheck, Sparkles } from 'lucide-vue-next';
import SidebarGroup from '@/components/ui/sidebar/SidebarGroup.vue';
import SidebarGroupContent from '@/components/ui/sidebar/SidebarGroupContent.vue';
import SidebarMenu from '@/components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuItem from '@/components/ui/sidebar/SidebarMenuItem.vue';
import SidebarMenuButton from '@/components/ui/sidebar/SidebarMenuButton.vue';
import { type AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const subscription = computed(() => page.props.subscription);

const progress = computed(() => {
    // Mock 30 day cycle calculation
    return Math.max(0, Math.min(100, (subscription.value.days_remaining / 30) * 100));
});
</script>

<template>
    <SidebarGroup class="group-data-[collapsible=icon]:hidden px-2 py-4">
        <div class="px-3 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground opacity-50">Subscription</span>
                <span class="text-[9px] font-black text-primary uppercase tracking-tighter">{{ subscription.days_remaining }} days left</span>
            </div>
            <div class="h-1.5 w-full bg-muted rounded-full overflow-hidden">
                <div 
                    class="h-full bg-primary transition-all duration-1000 shadow-[0_0_8px_rgba(var(--primary),0.4)]" 
                    :style="{ width: `${progress}%` }"
                ></div>
            </div>
        </div>
        
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child class="bg-primary/5 hover:bg-primary/10 border border-primary/10 transition-all h-14 rounded-2xl p-0 overflow-hidden group">
                        <Link href="/settings/billing" class="flex items-center gap-3 px-3 w-full h-full">
                            <div class="p-2 rounded-xl bg-primary/10 text-primary group-hover:scale-110 transition-transform">
                                <Sparkles class="w-4 h-4 fill-current" />
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-[11px] font-black uppercase tracking-widest text-foreground leading-none">{{ subscription.plan }}</span>
                                <span class="text-[9px] font-bold text-muted-foreground/60 mt-1.5 uppercase tracking-tighter truncate">Upgrade your features</span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
