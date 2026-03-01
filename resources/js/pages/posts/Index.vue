<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { 
    Search, 
    Plus, 
    Filter, 
    MoreHorizontal, 
    Instagram, 
    Facebook, 
    Twitter, 
    Linkedin, 
    Youtube,
    Type,
    Image as ImageIcon,
    Video,
    Edit2,
    Eye,
    BarChart2,
    Trash2,
    Calendar,
    ArrowUpRight,
    TrendingUp,
    Check,
    Heart,
    Clock
} from 'lucide-vue-next';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import { ref } from 'vue';
import { cn } from '@/lib/utils';

const breadcrumbs = [
    { title: 'Feed', href: '/posts' },
];

const posts = ref([
    { id: 1, title: 'Product Launch Day!', type: 'image', status: 'published', platforms: ['instagram', 'facebook'], created_at: '2h ago', engagement: '2.4k', reach: '12.5k' },
    { id: 2, title: 'Quick Demo Video', type: 'video', status: 'published', platforms: ['youtube', 'linkedin'], created_at: '5h ago', engagement: '1.8k', reach: '8.2k' },
    { id: 3, title: 'Hiring Full-stack Devs', type: 'text', status: 'draft', platforms: ['twitter', 'linkedin'], created_at: '1d ago', engagement: '-', reach: '-' },
    { id: 4, title: 'Weekly Recap', type: 'image', status: 'failed', platforms: ['instagram'], created_at: '2d ago', engagement: '-', reach: '-' },
]);

const getStatusColor = (status: string) => {
    switch (status) {
        case 'published': return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
        case 'draft': return 'bg-muted/50 text-muted-foreground border-border';
        case 'failed': return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20';
        default: return 'bg-muted text-foreground border-border';
    }
};

const getPlatformColor = (platform: string) => {
    switch (platform) {
        case 'facebook': return 'text-blue-600 bg-blue-50 dark:bg-blue-950/30 border-blue-100 dark:border-blue-900/50';
        case 'instagram': return 'text-pink-600 bg-pink-50 dark:bg-pink-950/30 border-pink-100 dark:border-pink-900/50';
        case 'twitter': return 'text-sky-500 bg-sky-50 dark:bg-sky-950/30 border-sky-100 dark:border-sky-900/50';
        case 'linkedin': return 'text-blue-700 bg-indigo-50 dark:bg-indigo-950/30 border-indigo-100 dark:border-indigo-900/50';
        default: return 'text-muted-foreground bg-muted border-border/50';
    }
};

const getPlatformIcon = (platform: string) => {
    switch (platform) {
        case 'instagram': return Instagram;
        case 'facebook': return Facebook;
        case 'twitter': return Twitter;
        case 'linkedin': return Linkedin;
        case 'youtube': return Youtube;
        default: return Calendar;
    }
};

const getTypeIcon = (type: string) => {
    switch (type) {
        case 'text': return Type;
        case 'image': return ImageIcon;
        case 'video': return Video;
        default: return Type;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Content Feed" />

        <div class="flex flex-col gap-6 p-6 max-w-7xl w-full mx-auto">
            <!-- Header (Analytics Style) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Content Feed</h1>
                    <p class="text-muted-foreground text-sm">Monitor and manage your active social presence.</p>
                </div>
                <Link href="/posts/create">
                    <Button class="rounded-xl px-6 h-11 gap-2 font-bold transition-all hover:scale-[1.02] border-0">
                        <Plus class="w-4 h-4 stroke-[3px]" /> New Post
                    </Button>
                </Link>
            </div>

            <!-- Stats Bar (Analytics Style) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Card v-for="stat in [
                    { label: 'Published', value: '124', change: '+12%', color: 'text-emerald-500', bg: 'bg-emerald-500/10', icon: Check },
                    { label: 'Total Reach', value: '82.1k', change: '+8.1%', color: 'text-blue-500', bg: 'bg-blue-500/10', icon: TrendingUp },
                    { label: 'Engagement', value: '4.8%', change: '+0.5%', color: 'text-purple-500', bg: 'bg-purple-500/10', icon: Heart },
                    { label: 'Drafts', value: '12', change: '-2', color: 'text-muted-foreground', bg: 'bg-muted/50', icon: Edit2 }
                ]" :key="stat.label" class="rounded-2xl border-border bg-card">
                    <CardContent>
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

            <!-- Feed Actions -->
            <div class="flex flex-col lg:flex-row items-center gap-4">
                <div class="relative flex-1 w-full">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/50" />
                    <Input placeholder="Search your feed..." class="pl-11 h-12 rounded-xl bg-card border-border shadow-none focus-visible:ring-primary/10" />
                </div>
                <Button variant="outline" class="h-12 rounded-xl border-border bg-card px-6 gap-2 text-xs font-bold uppercase tracking-widest">
                    <Filter class="w-4 h-4" /> Filters
                </Button>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <Card v-for="post in posts" :key="post.id" class="rounded-2xl border-border bg-card hover:border-primary/20 transition-all group overflow-hidden flex flex-col p-0">
                    <div class="p-6 flex-1 space-y-5">
                        <div class="flex items-start justify-between">
                            <div class="w-11 h-11 rounded-xl bg-muted flex items-center justify-center border border-border/50 group-hover:bg-primary/5 transition-colors">
                                <component :is="getTypeIcon(post.type)" class="w-6 h-6 text-muted-foreground group-hover:text-primary transition-colors" />
                            </div>
                            <div class="flex items-center gap-1.5">
                                <Badge variant="outline" :class="cn('rounded-full text-[9px] font-black uppercase px-2.5 py-0.5 border shadow-none', getStatusColor(post.status))">
                                    {{ post.status }}
                                </Badge>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="icon" class="h-8 w-8 rounded-full">
                                            <MoreHorizontal class="w-4 h-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-48 rounded-xl border-border bg-card shadow-2xl p-1.5">
                                        <DropdownMenuItem class="gap-2 py-2.5 rounded-lg cursor-pointer font-bold text-xs text-foreground"><Edit2 class="w-3.5 h-3.5" /> Edit Post</DropdownMenuItem>
                                        <DropdownMenuItem class="gap-2 py-2.5 rounded-lg cursor-pointer font-bold text-xs text-foreground"><BarChart2 class="w-3.5 h-3.5" /> Analytics</DropdownMenuItem>
                                        <DropdownMenuSeparator class="my-1 border-border" />
                                        <DropdownMenuItem class="gap-2 py-2.5 rounded-lg cursor-pointer text-rose-500 focus:text-rose-500 font-bold text-xs"><Trash2 class="w-3.5 h-3.5" /> Delete</DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h3 class="font-bold text-lg leading-tight group-hover:text-primary transition-colors line-clamp-2 text-foreground">{{ post.title }}</h3>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-muted-foreground opacity-60">
                                <span>{{ post.type }}</span>
                                <div class="w-1 h-1 rounded-full bg-border"></div>
                                <span>{{ post.created_at }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-border/50">
                            <div class="flex -space-x-2.5">
                                <div 
                                    v-for="p in post.platforms" 
                                    :key="p" 
                                    :class="cn('w-9 h-9 rounded-full border-2 border-card flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm', getPlatformColor(p))" 
                                    :title="p"
                                >
                                    <component :is="getPlatformIcon(p)" class="w-4 h-4" />
                                </div>
                            </div>
                            <div v-if="post.status === 'published'" class="text-right">
                                <p class="text-sm font-black text-foreground leading-none">{{ post.engagement }}</p>
                                <p class="text-[9px] font-black uppercase text-muted-foreground tracking-tighter mt-1 opacity-60">Engagements</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-muted/20 border-t border-border flex items-center justify-between group-hover:bg-primary/5 transition-colors">
                        <Link :href="`/posts/${post.id}`" class="text-[10px] font-black uppercase tracking-[0.15em] text-muted-foreground hover:text-primary transition-colors flex items-center gap-1.5">
                            View Story <ArrowUpRight class="w-3 h-3" />
                        </Link>
                        <Button variant="ghost" size="sm" class="h-8 rounded-lg text-[10px] font-black gap-1.5 hover:bg-card px-3 border-transparent border hover:border-border">
                            <Eye class="w-3.5 h-3.5" /> Preview
                        </Button>
                    </div>
                </Card>

                <!-- Add Card -->
                <Link href="/posts/create" class="rounded-2xl border-2 border-dashed border-border hover:border-primary/40 hover:bg-primary/5 transition-all group flex flex-col items-center justify-center p-8 text-center space-y-4 min-h-45">
                    <div class="w-14 h-14 rounded-2xl bg-muted flex items-center justify-center group-hover:bg-primary group-hover:text-muted transition-all border border-border/50 shadow-none">
                        <Plus class="w-7 h-7 stroke-[3px]" />
                    </div>
                    <div>
                        <p class="font-bold text-lg text-foreground leading-tight">Create Content</p>
                        <p class="text-[11px] text-muted-foreground font-medium max-w-45 mt-1 leading-relaxed">Launch your next social media campaign.</p>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
