<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { ChevronLeft, Edit2, BarChart2, Calendar, Globe, History, Instagram, Twitter, Linkedin, MessageSquare, Heart, Share2, Eye } from 'lucide-vue-next';

const props = defineProps<{
    id: string;
}>();

const breadcrumbs = [
    { title: 'Posts', href: '/posts' },
    { title: 'Post Details', href: `/posts/${props.id}` },
];

const post = {
    id: props.id,
    title: 'Launch Announcement',
    content: 'We are excited to announce the launch of our new Social Post Manager! 🚀 Manage all your social media channels from one place with advanced analytics and AI-powered content generation. #socialmedia #saas #launch #productivity',
    type: 'text',
    status: 'published',
    platforms: [
        { id: 'instagram', name: 'Instagram', status: 'success', link: '#', likes: '425', comments: '32' },
        { id: 'twitter', name: 'X (Twitter)', status: 'success', link: '#', likes: '128', comments: '14' },
        { id: 'linkedin', name: 'LinkedIn', status: 'success', link: '#', likes: '89', comments: '7' }
    ],
    created_at: '2026-02-27 10:00:00',
    published_at: '2026-02-27 10:05:00',
    engagement: '1.2k',
    reach: '8.4k',
    impressions: '12.5k'
};

const getPlatformIcon = (id: string) => {
    switch (id) {
        case 'instagram': return Instagram;
        case 'twitter': return Twitter;
        case 'linkedin': return Linkedin;
        default: return Globe;
    }
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="post.title" />

        <div class="flex flex-col gap-6 p-6 max-w-6xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/posts">
                        <Button variant="outline" size="icon" class="rounded-full bg-card border border-border shadow-sm">
                            <ChevronLeft class="w-5 h-5" />
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ post.title }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <Badge variant="success" class="rounded-full px-3">Published</Badge>
                            <span class="text-xs text-muted-foreground font-medium">ID: #{{ post.id }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link href="/posts/analytics">
                        <Button variant="outline" class="bg-card border border-border shadow-sm rounded-xl">
                            <BarChart2 class="w-4 h-4 mr-2" />
                            Full Analytics
                        </Button>
                    </Link>
                    <Button class="rounded-xl px-6 shadow-md transition-all hover:translate-y-[-1px]">
                        <Edit2 class="w-4 h-4 mr-2" />
                        Edit Post
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <Card class="rounded-2xl shadow-sm border border-border bg-card overflow-hidden">
                        <CardHeader class="border-b border-border bg-muted/20">
                            <CardTitle class="text-lg">Content Preview</CardTitle>
                        </CardHeader>
                        <CardContent class="p-0 bg-card">
                            <div class="p-8 whitespace-pre-wrap text-lg leading-relaxed text-foreground">
                                {{ post.content }}
                            </div>
                            <div class="p-6 bg-muted/30 border-t border-border flex flex-wrap gap-2">
                                <Badge v-for="tag in ['socialmedia', 'saas', 'launch', 'productivity']" :key="tag" variant="secondary" class="rounded-full bg-card border border-border text-foreground">
                                    #{{ tag }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <h3 class="text-lg font-bold mt-8 text-foreground">Platform Distribution</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Card v-for="platform in post.platforms" :key="platform.id" class="rounded-2xl shadow-sm border border-border bg-card hover:shadow-md transition-all group">
                            <CardContent class="p-5 space-y-5">
                                <div class="flex items-center justify-between">
                                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                        <component :is="getPlatformIcon(platform.id)" class="w-6 h-6" />
                                    </div>
                                    <Badge variant="success" class="h-5 text-[10px] rounded-full uppercase tracking-widest px-2">Live</Badge>
                                </div>
                                <div>
                                    <p class="font-bold capitalize text-foreground">{{ platform.name }}</p>
                                    <p class="text-xs text-muted-foreground font-medium">Published successfully</p>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-border">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-foreground">
                                            <Heart class="w-3.5 h-3.5 text-rose-500" /> {{ platform.likes }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-foreground">
                                            <MessageSquare class="w-3.5 h-3.5 text-blue-500" /> {{ platform.comments }}
                                        </div>
                                    </div>
                                    <Button variant="ghost" size="icon" class="h-8 w-8 rounded-full hover:bg-muted">
                                        <Eye class="w-4 h-4" />
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <Card class="rounded-2xl shadow-sm border border-border bg-card">
                        <CardHeader>
                            <CardTitle class="text-lg">Performance Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-7">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Impressions</p>
                                    <p class="text-2xl font-bold text-foreground">{{ post.impressions }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Reach</p>
                                    <p class="text-2xl font-bold text-foreground">{{ post.reach }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Engagement</p>
                                    <p class="text-2xl font-bold text-foreground">{{ post.engagement }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Avg. Rate</p>
                                    <p class="text-2xl font-bold text-emerald-500">4.8%</p>
                                </div>
                            </div>
                            <Button variant="outline" class="w-full rounded-xl border-border bg-muted/20 hover:bg-muted/50 h-11 font-bold">View Detailed Analytics</Button>
                        </CardContent>
                    </Card>

                    <Card class="rounded-2xl shadow-sm border border-border bg-card">
                        <CardHeader>
                            <CardTitle class="text-lg">Post Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-5">
                            <div class="flex items-center gap-4 group">
                                <div class="p-2.5 rounded-xl bg-muted border border-border transition-colors group-hover:bg-primary/5 group-hover:text-primary group-hover:border-primary/20">
                                    <Calendar class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-[10px] text-muted-foreground uppercase font-black tracking-widest">Created</p>
                                    <p class="text-sm font-bold text-foreground">{{ post.created_at }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 group">
                                <div class="p-2.5 rounded-xl bg-muted border border-border transition-colors group-hover:bg-primary/5 group-hover:text-primary group-hover:border-primary/20">
                                    <History class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-[10px] text-muted-foreground uppercase font-black tracking-widest">Published</p>
                                    <p class="text-sm font-bold text-foreground">{{ post.published_at }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 group">
                                <div class="p-2.5 rounded-xl bg-muted border border-border transition-colors group-hover:bg-primary/5 group-hover:text-primary group-hover:border-primary/20">
                                    <Globe class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-[10px] text-muted-foreground uppercase font-black tracking-widest">Visibility</p>
                                    <p class="text-sm font-bold text-foreground uppercase tracking-tight">Public</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
