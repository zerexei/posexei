<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BarChart2, 
    TrendingUp, 
    Users, 
    MessageSquare, 
    Heart, 
    Share2, 
    MousePointer2,
    Calendar as CalendarIcon,
    ChevronDown,
    Plus,
    Check,
    Edit2
} from 'lucide-vue-next';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import Button from '@/components/ui/button/Button.vue';
import { cn } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const stats = [
    { label: 'Total Impressions', value: '124.5k', change: '+12.3%', icon: BarChart2, color: 'text-blue-500', bg: 'bg-blue-500/10' },
    { label: 'Total Reach', value: '82.1k', change: '+8.1%', icon: Users, color: 'text-purple-500', bg: 'bg-purple-500/10' },
    { label: 'Engagement Rate', value: '4.2%', change: '+0.5%', icon: TrendingUp, color: 'text-emerald-500', bg: 'bg-emerald-500/10' },
    { label: 'Drafts', value: '12', change: '-2', icon: Edit2, color: 'text-muted-foreground', bg: 'bg-muted/50' },
];

const recentPosts = [
    { id: 1, title: 'Summer Sale Kickoff', type: 'Image', status: 'Published', date: '2h ago', engagement: '2.4k' },
    { id: 2, title: 'Product Walkthrough', type: 'Video', status: 'Published', date: '5h ago', engagement: '1.8k' },
    { id: 3, title: 'Hiring Full-stack Devs', type: 'Text', status: 'Draft', date: '1d ago', engagement: '-' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 max-w-7xl w-full mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Dashboard</h1>
                    <p class="text-muted-foreground text-sm">Overview of your social media performance.</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" class="bg-card border-border shadow-sm rounded-xl h-10 px-4 text-xs font-bold">
                        <CalendarIcon class="w-3.5 h-3.5 mr-2" />
                        Last 30 Days
                        <ChevronDown class="w-3.5 h-3.5 ml-2" />
                    </Button>
                    <Link href="/posts/create">
                        <Button class="rounded-xl px-5 h-10 gap-2 font-bold shadow-sm transition-all hover:scale-[1.02]">
                            <Plus class="w-4 h-4 stroke-[3px]" /> New Post
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Card v-for="stat in stats" :key="stat.label" class="rounded-2xl border-border bg-card">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div :class="cn('p-2.5 rounded-xl', stat.bg)">
                                <component :is="stat.icon" :class="cn('w-5 h-5', stat.color)" />
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase bg-muted text-muted-foreground border border-border/50">
                                {{ stat.change }}
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-2xl font-bold tracking-tight text-foreground">{{ stat.value }}</h3>
                            <p class="text-[10px] font-black text-muted-foreground mt-1 uppercase tracking-widest opacity-60">{{ stat.label }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Engagement Chart -->
                <Card class="lg:col-span-2 rounded-2xl border-border bg-card">
                    <CardHeader>
                        <CardTitle class="text-lg font-bold">Engagement Over Time</CardTitle>
                        <CardDescription class="text-xs">Daily engagement across all platforms</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-[250px] w-full flex items-end gap-2 pt-4">
                            <div v-for="i in 14" :key="i" class="flex-1 bg-primary/10 hover:bg-primary/30 transition-all rounded-t-md relative group border-x border-t border-transparent hover:border-primary/20" :style="{ height: `${Math.random() * 80 + 10}%` }">
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-popover text-popover-foreground text-[10px] font-bold py-1 px-2 rounded-md opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap border border-border shadow-md z-10">
                                    {{ Math.floor(Math.random() * 500) }}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between mt-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest px-1 border-t border-border pt-3">
                            <span>Feb 14</span>
                            <span>Feb 21</span>
                            <span>Feb 28</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Activity -->
                <Card class="rounded-2xl border-border bg-card">
                    <CardHeader>
                        <CardTitle class="text-lg font-bold">Recent Activity</CardTitle>
                        <CardDescription class="text-xs">Latest posts and drafts</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-for="post in recentPosts" :key="post.id" class="flex items-center justify-between p-3 rounded-xl hover:bg-muted/50 transition-colors border border-transparent hover:border-border cursor-pointer group">
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-foreground group-hover:text-primary transition-colors">{{ post.title }}</p>
                                <div class="flex items-center gap-2 text-[10px] text-muted-foreground font-medium uppercase tracking-wide">
                                    <span :class="{'text-emerald-500': post.status === 'Published', 'text-amber-500': post.status === 'Draft'}">{{ post.status }}</span>
                                    <span>•</span>
                                    <span>{{ post.date }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-foreground">{{ post.engagement }}</span>
                            </div>
                        </div>
                        <Link href="/posts" class="block pt-2">
                            <Button variant="ghost" class="w-full h-9 text-xs font-bold uppercase tracking-widest text-muted-foreground hover:text-foreground">View All Activity</Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
