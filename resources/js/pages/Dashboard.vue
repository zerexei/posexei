<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { 
    BarChart2, 
    TrendingUp, 
    Users, 
    Plus, 
    Instagram, 
    Facebook, 
    Twitter, 
    Linkedin, 
    Zap, 
    Clock, 
    Target,
    ChevronRight,
    Calendar as CalendarIcon,
    ExternalLink
} from 'lucide-vue-next';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import Button from '@/components/ui/button/Button.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Connection {
    id: number;
    provider: 'facebook' | 'linkedin' | 'twitter' | 'instagram';
    name: string;
}

interface UpcomingPost {
    id: number;
    title: string;
    platforms: string[];
    date: string;
}

interface LatestPost {
    id: number;
    title: string;
    platforms: string[];
    date: string;
    status: string;
}

interface Stats {
    posts_this_month: number;
    monthly_target: number;
}

const props = defineProps<{
    connections: Connection[];
    upcoming: UpcomingPost[];
    latestPosts: LatestPost[];
    stats: Stats;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard().url }];

const dashboardStats = [
    { label: 'Total Reach', value: '82.1k', change: '+12.3%', icon: Users, color: 'text-blue-500', bg: 'bg-blue-500/10' },
    { label: 'Engagement', value: '4.2%', change: '+0.5%', icon: TrendingUp, color: 'text-emerald-500', bg: 'bg-emerald-500/10' },
    { label: 'Impressions', value: '124.5k', change: '+18.2%', icon: BarChart2, color: 'text-purple-500', bg: 'bg-purple-500/10' },
];

const getPlatformIcon = (provider: string) => {
    switch (provider) {
        case 'facebook': return Facebook;
        case 'instagram': return Instagram;
        case 'twitter': return Twitter;
        case 'linkedin': return Linkedin;
        default: return Zap;
    }
};

const getPlatformBrand = (provider: string) => {
    switch (provider) {
        case 'facebook': return { color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-950/30' };
        case 'instagram': return { color: 'text-pink-600', bg: 'bg-pink-50 dark:bg-pink-950/30' };
        case 'twitter': return { color: 'text-sky-500', bg: 'bg-sky-50 dark:bg-sky-950/30' };
        case 'linkedin': return { color: 'text-blue-700', bg: 'bg-indigo-50 dark:bg-indigo-950/30' };
        default: return { color: 'text-muted-foreground', bg: 'bg-muted' };
    }
};

const goalPercentage = computed(() => {
    if (!props.stats || !props.stats.monthly_target) return 0;
    return Math.round((props.stats.posts_this_month / props.stats.monthly_target) * 100);
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-7xl mx-auto space-y-8 animate-in fade-in duration-500">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-foreground">Welcome back, {{ user.name.split(' ')[0] }}!</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex -space-x-1.5">
                            <div v-for="conn in connections" :key="conn.id" 
                                 :class="cn('w-5 h-5 rounded-full border border-background flex items-center justify-center shadow-sm', getPlatformBrand(conn.provider).bg)">
                                <component :is="getPlatformIcon(conn.provider)" :class="cn('w-2.5 h-2.5', getPlatformBrand(conn.provider).color)" />
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider">{{ connections.length }} channels synced</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Link href="/posts/create">
                        <Button class="rounded-2xl h-11 px-6 font-bold gap-2 shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">
                            <Plus class="w-4 h-4 stroke-[3.5px]" /> New Post
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Metrics & Charts -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Metrics Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div v-for="stat in dashboardStats" :key="stat.label" class="p-5 rounded-3xl border border-border bg-card shadow-xs group hover:border-primary/20 transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <div :class="cn('p-2.5 rounded-2xl transition-colors', stat.bg)">
                                    <component :is="stat.icon" :class="cn('w-5 h-5', stat.color)" />
                                </div>
                                <Badge variant="outline" class="text-[9px] font-black uppercase border-emerald-500/20 text-emerald-600 bg-emerald-500/5 shadow-none">{{ stat.change }}</Badge>
                            </div>
                            <div class="space-y-0.5">
                                <h3 class="text-2xl font-black text-foreground">{{ stat.value }}</h3>
                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest opacity-60">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <Card class="rounded-[2rem] border-border bg-card shadow-none overflow-hidden">
                        <CardHeader class="p-8 pb-0 flex flex-row items-center justify-between">
                            <div class="space-y-1">
                                <CardTitle class="text-base font-bold">Engagement Trends</CardTitle>
                                <CardDescription class="text-[10px] font-black uppercase tracking-widest text-muted-foreground/50">Last 14 Days</CardDescription>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button variant="outline" size="sm" class="rounded-xl h-8 text-[10px] font-black uppercase tracking-widest gap-2 bg-background border-border">
                                    <CalendarIcon class="w-3 h-3 opacity-50" /> Weekly
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent class="p-8 pt-6">
                            <div class="h-[240px] w-full flex items-end gap-2 px-2">
                                <div v-for="i in 14" :key="i" 
                                     class="flex-1 bg-primary/10 hover:bg-primary/30 transition-all rounded-t-xl relative group" 
                                     :style="{ height: `${Math.random() * 60 + 20}%` }">
                                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-foreground text-background text-[9px] font-black py-1.5 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap shadow-xl z-20">
                                        {{ Math.floor(Math.random() * 500) }} eng
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between mt-6 px-2 pt-4 border-t border-border/50 text-[9px] font-black text-muted-foreground uppercase tracking-[0.2em] opacity-40">
                                <span>Feb 15</span>
                                <span>Today</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Latest Posts Info Feed -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between px-2">
                            <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-muted-foreground/60">Recent Activity Feed</h2>
                            <Link href="/posts" class="text-[10px] font-black uppercase tracking-widest text-primary hover:text-primary/80 transition-colors">View full history</Link>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <div v-for="post in latestPosts" :key="post.id" 
                                 class="flex items-center justify-between p-3 rounded-2xl bg-card border border-border/50 hover:bg-muted/30 hover:border-primary/10 transition-all group cursor-default">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="flex -space-x-2 shrink-0">
                                        <div v-for="p in post.platforms" :key="p" 
                                             :class="cn('w-8 h-8 rounded-full border-2 border-background flex items-center justify-center shadow-xs', getPlatformBrand(p).bg)">
                                            <component :is="getPlatformIcon(p)" :class="cn('w-3.5 h-3.5', getPlatformBrand(p).color)" />
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ post.title }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[9px] font-black text-muted-foreground uppercase opacity-50">{{ post.date }}</span>
                                            <span class="w-1 h-1 rounded-full bg-border"></span>
                                            <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-tighter">Successfully Published</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="hidden sm:flex flex-col items-end pr-4 border-r border-border/50">
                                        <span class="text-[10px] font-black text-foreground">{{ Math.floor(Math.random() * 500 + 100) }}</span>
                                        <span class="text-[8px] font-black text-muted-foreground uppercase tracking-widest opacity-40">Eng.</span>
                                    </div>
                                    <Link :href="`/posts/${post.id}`">
                                        <Button variant="ghost" size="icon" class="h-8 w-8 rounded-xl hover:bg-primary/10 group-hover:text-primary transition-all">
                                            <ExternalLink class="w-3.5 h-3.5" />
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Goal & Timeline -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Goal Progress Widget -->
                    <Card class="rounded-[2rem] border-border bg-linear-to-br from-card to-muted/20 shadow-none overflow-hidden text-foreground">
                        <CardHeader class="p-6 pb-2">
                            <CardTitle class="text-sm font-bold flex items-center gap-2">
                                <Target class="w-4 h-4 text-primary" /> Monthly Goal
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-6 pt-2 space-y-5">
                            <div class="space-y-3">
                                <div class="flex items-end justify-between">
                                    <h4 class="text-3xl font-black text-foreground">{{ goalPercentage }}%</h4>
                                    <span v-if="stats" class="text-[10px] font-black text-muted-foreground uppercase mb-1">{{ stats.posts_this_month }} / {{ stats.monthly_target }} Posts</span>
                                </div>
                                <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
                                    <div class="h-full bg-primary transition-all duration-1000 shadow-[0_0_8px_rgba(var(--primary),0.4)]" :style="{ width: `${goalPercentage}%` }"></div>
                                </div>
                            </div>
                            <p v-if="stats" class="text-[11px] text-muted-foreground font-medium leading-relaxed italic border-l-2 border-primary/20 pl-3">
                                You're on track! Only {{ stats.monthly_target - stats.posts_this_month }} more posts to hit your target.
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Compact Timeline -->
                    <Card class="rounded-[2rem] border-border bg-card shadow-none flex-1 overflow-hidden">
                        <CardHeader class="p-6 pb-4 border-b border-border/50 bg-muted/10">
                            <CardTitle class="text-xs font-black uppercase tracking-widest text-muted-foreground flex items-center gap-2">
                                <Clock class="w-3.5 h-3.5" /> Next in Queue
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div v-if="upcoming.length > 0" class="divide-y divide-border/30">
                                <div v-for="post in upcoming.slice(0, 3)" :key="post.id" 
                                     class="p-5 hover:bg-muted/30 transition-all flex items-center justify-between gap-4 cursor-pointer group">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ post.title }}</p>
                                        <p class="text-[9px] font-black text-muted-foreground uppercase mt-1 opacity-60 tracking-wider">{{ post.date.split(',')[0] }}</p>
                                    </div>
                                    <div class="flex -space-x-1.5 shrink-0 transition-transform group-hover:translate-x-1">
                                        <div v-for="p in post.platforms" :key="p" :class="cn('w-6 h-6 rounded-full border-2 border-background flex items-center justify-center shadow-xs', getPlatformBrand(p).bg)">
                                            <component :is="getPlatformIcon(p)" :class="cn('w-3 h-3', getPlatformBrand(p).color)" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-12 text-center">
                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest leading-relaxed">Queue empty</p>
                            </div>
                        </CardContent>
                        <div class="p-4 bg-muted/20 border-t border-border/50">
                            <Link href="/posts" class="block">
                                <Button variant="ghost" class="w-full rounded-xl h-10 text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-muted gap-2">
                                    Manage Schedule <ChevronRight class="w-3 h-3" />
                                </Button>
                            </Link>
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
