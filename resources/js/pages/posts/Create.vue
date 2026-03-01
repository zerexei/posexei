<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Label from '@/components/ui/label/Label.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import { ref, computed } from 'vue';
import { 
    Image as ImageIcon, 
    Video, 
    Type, 
    Instagram, 
    Facebook, 
    Twitter, 
    Linkedin, 
    Youtube,
    Plus,
    X,
    Check,
    Wand2,
    Hash,
    Eye,
    Clock,
    Globe,
    MessageSquare,
    Heart,
    Share2,
    BarChart2,
    ThumbsUp,
    MoreHorizontal,
    Music2,
    Send,
    Link2,
    PlusCircle
} from 'lucide-vue-next';
import { cn } from '@/lib/utils';

interface Connection {
    id: number;
    provider: 'facebook' | 'linkedin' | 'twitter' | 'instagram';
    name: string;
    connected_at: string;
    status: 'active' | 'expired' | 'error';
    expires_at?: string;
}

const props = defineProps<{
    connections: Connection[];
}>();

const breadcrumbs = [
    { title: 'Posts', href: '/posts' },
    { title: 'Create Post', href: '/posts/create' },
];

const postType = ref('text');
const title = ref('');
const content = ref('');
const tags = ref<string[]>([]);
const newTag = ref('');

// Scheduling
const scheduleDate = ref('');
const scheduleTime = ref('');

const getPlatformIcon = (provider: string) => {
    switch (provider) {
        case 'facebook': return Facebook;
        case 'instagram': return Instagram;
        case 'twitter': return Twitter;
        case 'linkedin': return Linkedin;
        default: return Globe;
    }
};

const platforms = computed(() => {
    return props.connections.map(conn => {
        const brand = {
            facebook: { color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-950/30', border: 'border-blue-200 dark:border-blue-800/50' },
            instagram: { color: 'text-pink-600', bg: 'bg-pink-50 dark:bg-pink-950/30', border: 'border-pink-200 dark:border-pink-800/50' },
            twitter: { color: 'text-sky-500', bg: 'bg-sky-50 dark:bg-sky-950/30', border: 'border-sky-200 dark:border-sky-800/50' },
            linkedin: { color: 'text-blue-700', bg: 'bg-indigo-50 dark:bg-indigo-950/30', border: 'border-indigo-200 dark:border-indigo-800/50' },
        }[conn.provider] || { color: 'text-foreground', bg: 'bg-muted', border: 'border-border' };

        return {
            id: conn.id.toString(),
            name: conn.provider.charAt(0).toUpperCase() + conn.provider.slice(1),
            provider: conn.provider,
            icon: getPlatformIcon(conn.provider),
            account: conn.name,
            status: conn.status,
            ...brand
        };
    });
});

const selectedPlatforms = ref<string[]>([]);

const togglePlatform = (id: string) => {
    if (selectedPlatforms.value.includes(id)) {
        selectedPlatforms.value = selectedPlatforms.value.filter(p => p !== id);
    } else {
        selectedPlatforms.value.push(id);
    }
};

const addTag = () => {
    if (newTag.value && !tags.value.includes(newTag.value)) {
        tags.value.push(newTag.value);
        newTag.value = '';
    }
};

const removeTag = (tag: string) => {
    tags.value = tags.value.filter(t => t !== tag);
};

const charCount = computed(() => content.value.length);

const getValidationWarning = (pId: string) => {
    const platform = platforms.value.find(p => p.id === pId);
    if (!platform) return null;
    
    if (platform.provider === 'twitter' && charCount.value > 280) return 'X has a 280 character limit.';
    if (platform.provider === 'instagram' && postType.value === 'text') return 'Instagram requires media.';
    return null;
};

const activePreviewTab = ref('');
const openPreview = () => {
    if (selectedPlatforms.value.length > 0) {
        if (!activePreviewTab.value || !selectedPlatforms.value.includes(activePreviewTab.value)) {
            activePreviewTab.value = selectedPlatforms.value[0];
        }
    }
};

const showSuccessModal = ref(false);
const isPublishing = ref(false);

const publishPost = () => {
    isPublishing.value = true;
    setTimeout(() => {
        isPublishing.value = false;
        showSuccessModal.value = true;
    }, 1500);
};

const getActivePreviewProvider = computed(() => {
    const p = platforms.value.find(p => p.id === activePreviewTab.value);
    return p?.provider || '';
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create Social Post" />

        <div class="flex flex-col gap-6 p-6 max-w-7xl w-full mx-auto h-[calc(100vh-64px)] overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between shrink-0">
                <div class="space-y-1 text-foreground">
                    <h1 class="text-2xl font-bold tracking-tight">Compose Post</h1>
                    <p class="text-muted-foreground text-xs font-medium">Create and distribute content everywhere.</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <Dialog v-if="selectedPlatforms.length > 0">
                        <DialogTrigger asChild>
                            <Button variant="outline" class="bg-card border-border gap-2 h-10 px-4 font-bold" @click="openPreview">
                                <Eye class="w-4 h-4" /> Preview
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-3xl rounded-2xl border border-border bg-card p-0 overflow-hidden shadow-2xl">
                            <div class="flex flex-col h-[600px] bg-card text-foreground">
                                <div class="p-6 border-b border-border flex items-center justify-between bg-muted/20 shrink-0">
                                    <h3 class="font-bold text-lg text-foreground">Post Preview</h3>
                                    <div class="flex gap-1 p-1 bg-background rounded-lg border border-border overflow-x-auto max-w-md no-scrollbar shrink-0">
                                        <button 
                                            v-for="pId in selectedPlatforms" 
                                            :key="pId"
                                            @click="activePreviewTab = pId"
                                            :class="cn(
                                                'px-3 py-1 rounded-md text-[10px] font-black uppercase transition-all whitespace-nowrap',
                                                activePreviewTab === pId ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted'
                                            )"
                                        >
                                            {{ platforms.find(p => p.id === pId)?.provider }}
                                        </button>
                                    </div>
                                </div>
                                <div class="flex-1 overflow-y-auto p-10 flex justify-center bg-background/50 text-foreground">
                                    <!-- MOCK INSTAGRAM -->
                                    <div v-if="getActivePreviewProvider === 'instagram'" class="w-[340px] bg-card border border-border rounded-xl h-fit shadow-sm overflow-hidden animate-in fade-in zoom-in duration-300 text-foreground">
                                        <div class="p-3 flex items-center gap-2 border-b border-border/50 text-foreground">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 p-[1.5px]">
                                                <div class="w-full h-full rounded-full bg-card border-2 border-card"></div>
                                            </div>
                                            <span class="text-xs font-bold text-foreground">{{ platforms.find(p => p.id === activePreviewTab)?.account }}</span>
                                        </div>
                                        <div class="aspect-square bg-muted flex items-center justify-center">
                                            <ImageIcon v-if="postType !== 'video'" class="w-12 h-12 text-muted-foreground/20" />
                                            <Video v-else class="w-12 h-12 text-muted-foreground/20" />
                                        </div>
                                        <div class="p-4 space-y-2 text-foreground">
                                            <div class="flex items-center gap-3">
                                                <Heart class="w-5 h-5 text-foreground" /> <MessageSquare class="w-5 h-5 text-foreground" /> <Share2 class="w-5 h-5 text-foreground" />
                                            </div>
                                            <p class="text-[13px] leading-relaxed text-foreground">
                                                <span class="font-bold mr-1 text-foreground">posexei</span> {{ content || 'Write your post...' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- MOCK FACEBOOK -->
                                    <div v-else-if="getActivePreviewProvider === 'facebook'" class="w-[400px] bg-card border border-border rounded-xl h-fit shadow-sm animate-in fade-in zoom-in duration-300 text-foreground">
                                        <div class="p-4 flex items-center gap-3 text-foreground">
                                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shrink-0">P</div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold truncate text-foreground">{{ platforms.find(p => p.id === activePreviewTab)?.account }}</p>
                                                <p class="text-xs text-muted-foreground">Just now · <Globe class="inline w-3 h-3" /></p>
                                            </div>
                                            <MoreHorizontal class="w-5 h-5 text-muted-foreground" />
                                        </div>
                                        <div class="px-4 pb-3">
                                            <p class="text-sm text-foreground leading-relaxed whitespace-pre-wrap">{{ content || 'What’s on your mind?' }}</p>
                                        </div>
                                        <div v-if="postType !== 'text'" class="aspect-video bg-muted border-y border-border/50 flex items-center justify-center">
                                            <ImageIcon v-if="postType === 'image'" class="w-12 h-12 text-muted-foreground/20" />
                                            <Video v-else class="w-12 h-12 text-muted-foreground/20" />
                                        </div>
                                        <div class="p-2 border-t border-border/50 flex items-center justify-around text-muted-foreground font-semibold text-xs shrink-0">
                                            <div class="flex items-center gap-2 py-2 px-4 hover:bg-muted rounded-lg transition-colors"><ThumbsUp class="w-4 h-4 text-muted-foreground" /> Like</div>
                                            <div class="flex items-center gap-2 py-2 px-4 hover:bg-muted rounded-lg transition-colors"><MessageSquare class="w-4 h-4 text-muted-foreground" /> Comment</div>
                                            <div class="flex items-center gap-2 py-2 px-4 hover:bg-muted rounded-lg transition-colors"><Share2 class="w-4 h-4 text-muted-foreground" /> Share</div>
                                        </div>
                                    </div>

                                    <!-- MOCK TWITTER / X -->
                                    <div v-else-if="getActivePreviewProvider === 'twitter'" class="w-[400px] bg-card border border-border rounded-2xl p-5 h-fit shadow-sm animate-in fade-in zoom-in duration-300 text-foreground">
                                        <div class="flex gap-3 text-foreground">
                                            <div class="w-10 h-10 rounded-full bg-muted shrink-0 flex items-center justify-center font-bold text-foreground">P</div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1">
                                                    <span class="font-bold text-[14px] truncate text-foreground">{{ platforms.find(p => p.id === activePreviewTab)?.account }}</span>
                                                    <span class="text-muted-foreground text-[12px] shrink-0">· 1m</span>
                                                </div>
                                                <p class="text-[14px] mt-1 whitespace-pre-wrap text-foreground leading-relaxed">{{ content || 'What’s happening?' }}</p>
                                                <div v-if="postType !== 'text'" class="mt-3 aspect-video bg-muted rounded-xl border border-border flex items-center justify-center">
                                                    <ImageIcon v-if="postType === 'image'" class="w-10 h-10 text-muted-foreground/20" />
                                                    <Video v-else class="w-10 h-10 text-muted-foreground/20" />
                                                </div>
                                                <div class="flex items-center justify-between mt-4 text-muted-foreground max-w-xs">
                                                    <MessageSquare class="w-4.5 h-4.5" /> <Share2 class="w-4.5 h-4.5" /> <Heart class="w-4.5 h-4.5" /> <BarChart2 class="w-4.5 h-4.5" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MOCK LINKEDIN -->
                                    <div v-else-if="getActivePreviewProvider === 'linkedin'" class="w-[420px] bg-card border border-border rounded-xl h-fit shadow-sm animate-in fade-in zoom-in duration-300 overflow-hidden text-foreground">
                                        <div class="p-4 flex items-center gap-3">
                                            <div class="w-12 h-12 rounded bg-muted flex items-center justify-center font-bold shrink-0 text-foreground">P</div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold truncate text-foreground">{{ platforms.find(p => p.id === activePreviewTab)?.account }}</p>
                                                <p class="text-[11px] text-muted-foreground truncate">Professional Update</p>
                                                <p class="text-[11px] text-muted-foreground flex items-center gap-1">Just now · <Globe class="w-3 h-3 text-muted-foreground" /></p>
                                            </div>
                                            <MoreHorizontal class="w-5 h-5 text-muted-foreground" />
                                        </div>
                                        <div class="px-4 pb-4">
                                            <p class="text-[13px] leading-relaxed whitespace-pre-wrap text-foreground">{{ content || 'Share your professional updates...' }}</p>
                                        </div>
                                        <div v-if="postType !== 'text'" class="aspect-video bg-muted flex items-center justify-center border-y border-border/50">
                                            <ImageIcon v-if="postType === 'image'" class="w-12 h-12 text-muted-foreground/20" />
                                            <Video v-else class="w-12 h-12 text-muted-foreground/20" />
                                        </div>
                                        <div class="px-4 py-2 border-t border-border/50 flex items-center gap-6 text-muted-foreground font-semibold text-xs shrink-0 overflow-x-auto no-scrollbar">
                                            <div class="flex items-center gap-1 py-2 hover:bg-muted px-2 rounded transition-colors whitespace-nowrap text-muted-foreground"><ThumbsUp class="w-4 h-4" /> Like</div>
                                            <div class="flex items-center gap-1 py-2 hover:bg-muted px-2 rounded transition-colors whitespace-nowrap text-muted-foreground"><MessageSquare class="w-4 h-4" /> Comment</div>
                                            <div class="flex items-center gap-1 py-2 hover:bg-muted px-2 rounded transition-colors whitespace-nowrap text-muted-foreground"><Share2 class="w-4 h-4" /> Share</div>
                                            <div class="flex items-center gap-1 py-2 hover:bg-muted px-2 rounded transition-colors whitespace-nowrap text-muted-foreground"><Send class="w-4 h-4" /> Send</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </DialogContent>
                    </Dialog>

                    <div class="h-6 w-px bg-border mx-1"></div>

                    <Button variant="ghost" class="text-muted-foreground hover:text-foreground h-10 px-4 font-bold text-xs">Save Draft</Button>
                    
                    <Dialog>
                        <DialogTrigger asChild>
                            <Button variant="outline" size="icon" class="bg-card border-border h-10 w-10 rounded-xl" :disabled="selectedPlatforms.length === 0">
                                <Clock :class="cn('w-4 h-4 text-foreground')" />
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[350px] rounded-2xl border border-border bg-card p-6 shadow-2xl">
                            <DialogHeader>
                                <DialogTitle class="font-bold text-foreground">Schedule Post</DialogTitle>
                            </DialogHeader>
                            <div class="grid gap-4 py-4 text-foreground">
                                <div class="space-y-1.5">
                                    <Label class="text-[10px] font-bold uppercase text-muted-foreground px-1">Date</Label>
                                    <Input type="date" v-model="scheduleDate" class="h-11 rounded-xl bg-muted/50 border-border text-foreground" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-[10px] font-bold uppercase text-muted-foreground px-1">Time</Label>
                                    <Input type="time" v-model="scheduleTime" class="h-11 rounded-xl bg-muted/50 border-border text-foreground" />
                                </div>
                            </div>
                            <Button class="w-full rounded-xl font-bold h-11 text-foreground" @click="() => {}">Confirm Time</Button>
                        </DialogContent>
                    </Dialog>

                    <Button @click="publishPost" :disabled="isPublishing || selectedPlatforms.length === 0" class="rounded-xl px-8 h-10 font-bold shadow-sm shadow-primary/20">
                        <span v-if="isPublishing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2 text-foreground"></span>
                        Publish
                    </Button>
                </div>
            </div>

            <!-- Flattened Grid Body -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 flex-1 min-h-0">
                <!-- Composition Column -->
                <div class="lg:col-span-8 flex flex-col min-h-0 space-y-4">
                    <Card class="rounded-2xl border-border bg-card overflow-hidden flex flex-col flex-1 min-h-0 border">
                        <CardContent class="p-6 space-y-6 flex flex-col h-full text-foreground">
                            <div class="space-y-2 shrink-0">
                                <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground px-1">Internal Reference</Label>
                                <Input 
                                    v-model="title" 
                                    placeholder="Campaign or post title..." 
                                    class="text-lg font-bold py-6 rounded-xl border-0 bg-muted/30 focus-visible:ring-2 focus-visible:ring-primary/20 text-foreground px-4 placeholder:text-muted-foreground/30"
                                />
                            </div>

                            <div class="space-y-4 flex-1 flex flex-col min-h-0 text-foreground">
                                <div class="flex items-center justify-between shrink-0 px-1 text-foreground">
                                    <div class="flex gap-1 p-1 bg-muted/50 rounded-lg border border-border/50 text-foreground">
                                        <button 
                                            v-for="type in [
                                                { id: 'text', label: 'Post', icon: Type },
                                                { id: 'image', label: 'Media', icon: ImageIcon },
                                                { id: 'video', label: 'Video', icon: Video },
                                            ]"
                                            :key="type.id"
                                            @click="postType = type.id"
                                            :class="cn(
                                                'flex items-center gap-2 px-4 py-1.5 rounded-md text-[10px] font-black uppercase transition-all text-foreground',
                                                postType === type.id ? 'bg-card text-foreground border border-border shadow-sm' : 'text-muted-foreground hover:text-foreground'
                                            )"
                                        >
                                            <component :is="type.icon" class="w-3.5 h-3.5" /> {{ type.label }}
                                        </button>
                                    </div>

                                    <Button variant="ghost" size="sm" class="text-[10px] text-primary font-black uppercase tracking-widest bg-primary/5 hover:bg-primary/10 rounded-full h-7 px-3">
                                        ✨ AI Generate
                                    </Button>
                                </div>

                                <div class="border border-border rounded-2xl overflow-hidden bg-muted/20 focus-within:ring-2 focus-within:ring-primary/20 transition-all flex flex-col flex-1 min-h-0 text-foreground">
                                    <div class="bg-card/50 border-b border-border px-4 py-2 flex items-center gap-3 shrink-0 text-foreground">
                                        <Button variant="ghost" size="icon" class="h-8 w-8 text-foreground hover:bg-card"><span class="font-bold text-base text-foreground">B</span></Button>
                                        <Button variant="ghost" size="icon" class="h-8 w-8 text-foreground hover:bg-card"><span class="italic text-foreground">I</span></Button>
                                        <div class="w-px h-5 bg-border/50 mx-1 text-foreground"></div>
                                        <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:bg-card"><Hash class="w-4 h-4" /></Button>
                                    </div>
                                    <Textarea 
                                        v-model="content" 
                                        placeholder="What do you want to share?" 
                                        class="border-0 focus-visible:ring-0 rounded-none min-h-[200px] resize-none p-6 text-base bg-transparent text-foreground placeholder:text-muted-foreground/50 leading-relaxed flex-1 min-h-0"
                                    />
                                    <div v-if="postType !== 'text'" class="p-4 border-t border-border/50 bg-card/30 shrink-0 text-foreground">
                                        <div class="border-2 border-dashed border-border rounded-xl p-8 flex flex-col items-center justify-center text-center gap-2 hover:bg-primary/5 transition-colors cursor-pointer group text-foreground">
                                            <Plus class="w-6 h-6 text-muted-foreground group-hover:text-primary transition-colors text-foreground" />
                                            <span class="text-xs font-bold text-muted-foreground uppercase tracking-widest text-foreground">Select {{ postType }} file</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center px-1 shrink-0 text-foreground">
                                    <div class="flex gap-2 text-foreground">
                                        <template v-for="pId in selectedPlatforms" :key="pId">
                                            <div v-if="getValidationWarning(pId)" class="flex items-center gap-2 p-2 rounded-xl bg-amber-500/5 text-amber-600 dark:text-amber-400 text-[10px] font-bold border border-amber-500/10">
                                                <component :is="platforms.find(p => p.id === pId)?.icon" class="w-3.5 h-3.5 shrink-0" />
                                                limit exceeded
                                            </div>
                                        </template>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase text-muted-foreground opacity-50 tracking-tighter">{{ charCount }} characters</span>
                                </div>
                            </div>

                            <div class="space-y-3 pt-2 shrink-0 px-1 text-foreground">
                                <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground px-1 text-foreground">Tags</Label>
                                <div class="flex flex-wrap gap-2 items-center text-foreground">
                                    <Badge v-for="tag in tags" :key="tag" variant="secondary" class="rounded-full border border-border bg-muted/50 px-3 py-1 gap-2 font-bold text-[10px] text-foreground">
                                        #{{ tag }} <X class="w-3 h-3 cursor-pointer hover:text-primary transition-colors text-foreground" @click="removeTag(tag)" />
                                    </Badge>
                                    <div class="flex items-center bg-muted/30 rounded-xl px-3 border border-border h-8 text-foreground focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                                        <Input v-model="newTag" @keyup.enter="addTag" placeholder="Add tag..." class="w-24 border-0 bg-transparent text-xs focus-visible:ring-0 h-full font-bold p-0 text-foreground placeholder:text-muted-foreground/50" />
                                        <Plus class="w-3.5 h-3.5 text-muted-foreground/50" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-4 flex flex-col min-h-0 space-y-4 text-foreground">
                    <Card class="rounded-2xl border-border bg-card overflow-hidden flex flex-col flex-1 min-h-0 border text-foreground">
                        <CardHeader class="pb-3 border-b border-border bg-muted/10 shrink-0 space-y-0 flex flex-row items-center justify-between text-foreground">
                            <div class="space-y-0.5 text-foreground">
                                <CardTitle class="text-lg font-bold text-foreground">Channels</CardTitle>
                                <CardDescription class="text-[10px] uppercase font-black text-muted-foreground tracking-widest">Target accounts</CardDescription>
                            </div>
                            <Badge v-if="platforms.length > 0" variant="outline" class="rounded-full text-[9px] font-black uppercase bg-primary/5 text-primary border-primary/20">
                                {{ selectedPlatforms.length }} / {{ platforms.length }}
                            </Badge>
                        </CardHeader>
                        <CardContent class="p-2 space-y-1 overflow-y-auto flex-1 text-foreground">
                            <template v-if="platforms.length > 0">
                                <button
                                    v-for="platform in platforms"
                                    :key="platform.id"
                                    @click="togglePlatform(platform.id)"
                                    :class="cn(
                                        'flex items-center justify-between w-full p-3 rounded-xl transition-all border border-transparent group text-foreground',
                                        selectedPlatforms.includes(platform.id) ? cn(platform.bg, platform.border, 'shadow-sm') : 'hover:bg-muted/50'
                                    )"
                                >
                                    <div class="flex items-center gap-3 text-foreground min-w-0">
                                        <div :class="cn(
                                            'w-10 h-10 rounded-xl flex items-center justify-center transition-all border border-border shadow-sm bg-card shrink-0',
                                            selectedPlatforms.includes(platform.id) ? cn('bg-card', platform.color, 'border-current/20 shadow-lg scale-110') : 'bg-card text-muted-foreground'
                                        )">
                                            <component :is="platform.icon" class="w-5 h-5" />
                                        </div>
                                        <div class="text-left text-foreground min-w-0">
                                            <p class="text-xs font-bold leading-tight">{{ platform.name }}</p>
                                            <p class="text-[10px] text-muted-foreground font-medium truncate uppercase tracking-tighter mt-0.5 text-foreground">{{ platform.account }}</p>
                                        </div>
                                    </div>
                                    <div v-if="selectedPlatforms.includes(platform.id)" :class="cn('rounded-full p-1 shadow-none ring-2 ring-card shrink-0', platform.color.replace('text-', 'bg-').replace('600', '500').replace('700', '600'))">
                                        <Check class="w-3.5 h-3.5 text-white stroke-[3.5px]" />
                                    </div>
                                </button>
                                
                                <div class="pt-4 px-2">
                                    <Link href="/settings/connections">
                                        <Button variant="outline" class="w-full border-dashed border-border bg-muted/20 hover:bg-muted/50 rounded-xl h-12 text-[10px] font-black uppercase tracking-widest gap-2">
                                            <PlusCircle class="w-4 h-4" />
                                            Connect More
                                        </Button>
                                    </Link>
                                </div>
                            </template>
                            
                            <div v-else class="flex flex-col items-center justify-center p-8 text-center h-full">
                                <div class="w-16 h-16 rounded-full bg-muted flex items-center justify-center mb-4">
                                    <Link2 class="w-8 h-8 text-muted-foreground/30" />
                                </div>
                                <h3 class="font-bold text-foreground">No channels connected</h3>
                                <p class="text-[11px] text-muted-foreground mt-2 mb-6">You need to connect at least one social media account to start posting.</p>
                                <Link href="/settings/connections">
                                    <Button class="w-full rounded-xl font-bold h-11 px-6">
                                        Connect your first account
                                    </Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-md p-4 text-foreground">
            <Card class="w-full max-w-sm rounded-3xl border border-border bg-card text-card-foreground animate-in zoom-in duration-300 shadow-2xl border text-foreground">
                <CardContent class="p-10 text-center space-y-6 text-foreground">
                    <div class="w-16 h-16 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto shadow-inner border border-emerald-500/20 text-foreground">
                        <Check class="w-8 h-8 stroke-[2.5px]" />
                    </div>
                    <div class="space-y-1.5 text-foreground">
                        <h2 class="text-2xl font-black tracking-tight text-foreground">Post Confirmed</h2>
                        <p class="text-muted-foreground text-sm font-medium text-foreground">Distribution has started for {{ selectedPlatforms.length }} channels.</p>
                    </div>
                    <Link href="/posts" class="block pt-2 text-foreground">
                        <Button class="w-full h-12 rounded-xl font-bold transition-all hover:scale-[1.02] shadow-lg shadow-primary/20 border-0 text-foreground">Return to Dashboard</Button>
                    </Link>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
