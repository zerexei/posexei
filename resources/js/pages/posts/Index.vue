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
    ArrowUpRight,
    TrendingUp,
    Check,
    Heart,
    LayoutGrid,
    List,
    Clock,
    XCircle,
    Calendar,
    ChevronDown,
    Zap
} from 'lucide-vue-next';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import { ref, computed } from 'vue';
import { cn } from '@/lib/utils';

const breadcrumbs = [{ title: 'Content Feed', href: '/posts' }];

const viewMode = ref<'grid' | 'list'>('grid');
const searchQuery = ref('');
const activePlatformFilter = ref<string | null>(null);

const props = defineProps<{
    posts: any[];
}>();

const posts = computed(() => props.posts);
const filteredPosts = computed(() => {
    return posts.value.filter(post => {
        const title = post.title || '';
        const matchesSearch = title.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesPlatform = !activePlatformFilter.value || post.platforms.includes(activePlatformFilter.value);
        return matchesSearch && matchesPlatform;
    });
});

const getStatusConfig = (status: string) => {
    switch (status) {
        case 'published': return { color: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20', icon: Check, label: 'Published' };
        case 'scheduled': return { color: 'bg-blue-500/10 text-blue-600 border-blue-500/20', icon: Clock, label: 'Scheduled' };
        case 'draft': return { color: 'bg-muted/50 text-muted-foreground border-border', icon: Type, label: 'Draft' };
        case 'failed': return { color: 'bg-rose-500/10 text-rose-600 border-rose-500/20', icon: XCircle, label: 'Failed' };
        default: return { color: 'bg-muted text-foreground border-border', icon: Zap, label: status };
    }
};

const getPlatformBrand = (platform: string) => {
    switch (platform) {
        case 'facebook': return { color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-950/30', border: 'border-blue-100', icon: Facebook };
        case 'instagram': return { color: 'text-pink-600', bg: 'bg-pink-50 dark:bg-pink-950/30', border: 'border-pink-100', icon: Instagram };
        case 'twitter': return { color: 'text-sky-500', bg: 'bg-sky-50 dark:bg-sky-950/30', border: 'border-sky-100', icon: Twitter };
        case 'linkedin': return { color: 'text-blue-700', bg: 'bg-indigo-50 dark:bg-indigo-950/30', border: 'border-indigo-100', icon: Linkedin };
        case 'youtube': return { color: 'text-red-600', bg: 'bg-red-50 dark:bg-red-950/30', border: 'border-red-100', icon: Youtube };
        default: return { color: 'text-muted-foreground', bg: 'bg-muted', border: 'border-border', icon: Zap };
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

        <div class="flex flex-col gap-8 p-6 max-w-7xl w-full mx-auto pb-20">
            <!-- Modern Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-black tracking-tight text-foreground">Content Feed</h1>
                    <p class="text-muted-foreground font-medium text-sm">Monitor, analyze and distribute your brand presence.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex p-1 bg-muted/50 rounded-xl border border-border/50">
                        <button 
                            @click="viewMode = 'grid'"
                            :class="cn('p-2 rounded-lg transition-all', viewMode === 'grid' ? 'bg-card text-primary shadow-sm' : 'text-muted-foreground hover:text-foreground')"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>
                        <button 
                            @click="viewMode = 'list'"
                            :class="cn('p-2 rounded-lg transition-all', viewMode === 'list' ? 'bg-card text-primary shadow-sm' : 'text-muted-foreground hover:text-foreground')"
                        >
                            <List class="w-4 h-4" />
                        </button>
                    </div>
                    <Link href="/posts/create">
                        <Button class="rounded-2xl px-6 h-11 gap-2 font-bold shadow-lg shadow-primary/20 border-0 transition-transform active:scale-95">
                            <Plus class="w-4 h-4 stroke-[3px]" /> Create Post
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Stats & Quick Filters Row -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Search & Platform Filters -->
                <div class="flex-1 space-y-4">
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="relative flex-1 w-full group">
                            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <Input 
                                v-model="searchQuery"
                                placeholder="Search by title or content..." 
                                class="pl-11 h-12 rounded-2xl bg-card border-border shadow-none focus-visible:ring-primary/10 transition-all text-foreground" 
                            />
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" class="h-12 px-6 rounded-2xl border-border bg-card font-bold text-xs uppercase tracking-widest gap-2 shrink-0">
                                    <Filter class="w-4 h-4" /> Filters <ChevronDown class="w-3 h-3 opacity-50" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56 rounded-2xl border-border bg-card p-2 shadow-2xl">
                                <DropdownMenuItem class="rounded-xl font-bold text-xs uppercase tracking-widest py-3">All Content</DropdownMenuItem>
                                <DropdownMenuItem class="rounded-xl font-bold text-xs uppercase tracking-widest py-3">Published Only</DropdownMenuItem>
                                <DropdownMenuItem class="rounded-xl font-bold text-xs uppercase tracking-widest py-3">Scheduled Posts</DropdownMenuItem>
                                <DropdownMenuSeparator class="bg-border/50" />
                                <DropdownMenuItem class="rounded-xl font-bold text-xs uppercase tracking-widest py-3 text-rose-500">Reset Filters</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    
                    <!-- Platform Quick Access -->
                    <div class="flex flex-wrap gap-2">
                        <button 
                            v-for="p in ['instagram', 'facebook', 'twitter', 'linkedin', 'youtube']" 
                            :key="p"
                            @click="activePlatformFilter = activePlatformFilter === p ? null : p"
                            :class="cn(
                                'flex items-center gap-2 px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all',
                                activePlatformFilter === p 
                                    ? cn(getPlatformBrand(p).bg, getPlatformBrand(p).color, 'border-current/30 scale-105 shadow-sm')
                                    : 'border-border bg-card text-muted-foreground hover:border-primary/20'
                            )"
                        >
                            <component :is="getPlatformBrand(p).icon" class="w-3.5 h-3.5" />
                            {{ p }}
                        </button>
                    </div>
                </div>

                <!-- Summary Analytics -->
                <div class="grid grid-cols-2 gap-3 lg:w-80">
                    <div class="bg-emerald-500/[0.03] border border-emerald-500/10 p-4 rounded-3xl flex flex-col justify-between group hover:bg-emerald-500/[0.05] transition-colors">
                        <span class="text-[10px] font-black uppercase tracking-tighter text-emerald-600/60">Live Posts</span>
                        <h4 class="text-2xl font-black text-foreground">124</h4>
                    </div>
                    <div class="bg-muted/30 border border-border/50 p-4 rounded-3xl flex flex-col justify-between group hover:bg-muted/50 transition-colors">
                        <span class="text-[10px] font-black uppercase tracking-tighter text-muted-foreground">Drafts</span>
                        <h4 class="text-2xl font-black text-foreground">12</h4>
                    </div>
                </div>
            </div>

            <!-- Content Rendering -->
            <div v-if="filteredPosts.length > 0">
                <!-- GRID VIEW -->
                <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card v-for="post in filteredPosts" :key="post.id" class="rounded-[2.5rem] border-border bg-card hover:border-primary/20 transition-all group overflow-hidden flex flex-col shadow-none hover:shadow-xl hover:shadow-primary/5">
                        <div class="p-7 flex-1 space-y-6">
                            <div class="flex items-center justify-between">
                                <div :class="cn('w-12 h-12 rounded-2xl bg-muted flex items-center justify-center border border-border/50 group-hover:bg-primary/5 transition-colors')">
                                    <component :is="getTypeIcon(post.type)" class="w-6 h-6 text-muted-foreground group-hover:text-primary transition-colors" />
                                </div>
                                <Badge variant="outline" :class="cn('rounded-full text-[9px] font-black uppercase px-3 py-1 border shadow-none flex items-center gap-1.5', getStatusConfig(post.status).color)">
                                    <component :is="getStatusConfig(post.status).icon" class="w-3 h-3" />
                                    {{ getStatusConfig(post.status).label }}
                                </Badge>
                            </div>

                            <div class="space-y-2">
                                <h3 class="font-bold text-lg leading-snug group-hover:text-primary transition-colors line-clamp-2 text-foreground">{{ post.title }}</h3>
                                <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed opacity-80">{{ post.content }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-border/40">
                                <div class="flex -space-x-2">
                                    <div 
                                        v-for="p in post.platforms" 
                                        :key="p" 
                                        :class="cn('w-9 h-9 rounded-full border-[3px] border-background flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm', getPlatformBrand(p).bg)" 
                                        :title="p"
                                    >
                                        <component :is="getPlatformBrand(p).icon" class="w-4 h-4" :class="getPlatformBrand(p).color" />
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <div v-if="post.status === 'published'" class="space-y-0.5">
                                        <p class="text-xs font-black text-foreground leading-none">{{ post.reach }}</p>
                                        <p class="text-[8px] font-black uppercase text-muted-foreground tracking-tighter opacity-60">Reach</p>
                                    </div>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 rounded-full hover:bg-muted">
                                                <MoreHorizontal class="w-4 h-4 text-muted-foreground" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="w-48 rounded-2xl border-border bg-card shadow-2xl p-2">
                                            <DropdownMenuItem class="gap-2 py-3 rounded-xl cursor-pointer font-bold text-[10px] uppercase tracking-widest"><Edit2 class="w-3.5 h-3.5" /> Edit</DropdownMenuItem>
                                            <DropdownMenuItem class="gap-2 py-3 rounded-xl cursor-pointer font-bold text-[10px] uppercase tracking-widest"><BarChart2 class="w-3.5 h-3.5" /> Insights</DropdownMenuItem>
                                            <DropdownMenuSeparator class="my-1 border-border/50" />
                                            <DropdownMenuItem class="gap-2 py-3 rounded-xl cursor-pointer text-rose-500 focus:text-rose-500 font-bold text-[10px] uppercase tracking-widest"><Trash2 class="w-3.5 h-3.5" /> Delete</DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </div>
                        </div>

                        <div class="px-7 py-4 bg-muted/20 border-t border-border/50 flex items-center justify-between group-hover:bg-primary/[0.02] transition-colors">
                            <span class="text-[10px] font-black uppercase tracking-[0.15em] text-muted-foreground opacity-50">{{ post.created_at }}</span>
                            <Link :href="`/posts/${post.id}`">
                                <Button variant="ghost" size="sm" class="h-8 rounded-xl text-[10px] font-black gap-1.5 hover:bg-card px-3 border-transparent border hover:border-border text-foreground">
                                    <Eye class="w-3.5 h-3.5" /> Preview Post
                                </Button>
                            </Link>
                        </div>
                    </Card>
                </div>

                <!-- LIST VIEW -->
                <div v-else class="bg-card border border-border rounded-[2rem] overflow-hidden shadow-none">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-muted/20 border-b border-border/50">
                                <tr>
                                    <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Content</th>
                                    <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Channels</th>
                                    <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground text-center">Status</th>
                                    <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Performance</th>
                                    <th class="p-5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/30">
                                <tr v-for="post in filteredPosts" :key="post.id" class="group hover:bg-muted/10 transition-colors">
                                    <td class="p-5">
                                        <div class="flex items-center gap-4 min-w-[300px]">
                                            <div class="w-10 h-10 rounded-xl bg-muted flex items-center justify-center border border-border/50 shrink-0">
                                                <component :is="getTypeIcon(post.type)" class="w-5 h-5 text-muted-foreground" />
                                            </div>
                                            <div class="space-y-0.5">
                                                <p class="font-bold text-sm text-foreground truncate group-hover:text-primary transition-colors">{{ post.title }}</p>
                                                <p class="text-[10px] text-muted-foreground font-medium uppercase tracking-widest opacity-60">{{ post.created_at }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex -space-x-1.5">
                                            <div v-for="p in post.platforms" :key="p" :class="cn('w-7 h-7 rounded-full border-2 border-background flex items-center justify-center', getPlatformBrand(p).bg)">
                                                <component :is="getPlatformBrand(p).icon" class="w-3 h-3" :class="getPlatformBrand(p).color" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        <Badge variant="outline" :class="cn('rounded-full text-[9px] font-black uppercase px-2.5 py-0.5 border shadow-none inline-flex items-center gap-1', getStatusConfig(post.status).color)">
                                            <component :is="getStatusConfig(post.status).icon" class="w-2.5 h-2.5" />
                                            {{ getStatusConfig(post.status).label }}
                                        </Badge>
                                    </td>
                                    <td class="p-5">
                                        <div v-if="post.status === 'published'" class="flex items-center gap-4">
                                            <div class="text-center">
                                                <p class="text-xs font-black text-foreground">{{ post.reach }}</p>
                                                <p class="text-[8px] font-black uppercase text-muted-foreground tracking-tighter opacity-50 leading-none">Reach</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-xs font-black text-foreground">{{ post.engagement }}</p>
                                                <p class="text-[8px] font-black uppercase text-muted-foreground tracking-tighter opacity-50 leading-none">Eng.</p>
                                            </div>
                                        </div>
                                        <span v-else class="text-[10px] font-black text-muted-foreground/30 uppercase tracking-widest">—</span>
                                    </td>
                                    <td class="p-5 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link :href="`/posts/${post.id}`">
                                                <Button variant="ghost" size="icon" class="h-8 w-8 rounded-lg hover:bg-primary/5 hover:text-primary">
                                                    <ArrowUpRight class="w-4 h-4 text-foreground" />
                                                </Button>
                                            </Link>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 rounded-lg hover:bg-muted">
                                                <MoreHorizontal class="w-4 h-4 text-muted-foreground" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="py-20 text-center space-y-6">
                <div class="w-20 h-20 rounded-[2rem] bg-muted/50 flex items-center justify-center mx-auto border border-border shadow-inner">
                    <Search class="w-10 h-10 text-muted-foreground opacity-20" />
                </div>
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-foreground">No matches found</h3>
                    <p class="text-sm text-muted-foreground max-w-xs mx-auto">We couldn't find any posts matching your current filters or search query.</p>
                </div>
                <Button variant="outline" class="rounded-2xl h-11 px-8 font-bold border-dashed border-border" @click="searchQuery = ''; activePlatformFilter = null">
                    Clear all filters
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
