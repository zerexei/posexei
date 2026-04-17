<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BarChart3, 
    Calendar, 
    CheckCircle2, 
    Clock, 
    Facebook, 
    Instagram, 
    Layers, 
    Linkedin, 
    Plus, 
    Share2, 
    Target, 
    TrendingUp, 
    Twitter, 
    Zap,
    ChevronRight,
    Play
} from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { cn } from '@/lib/utils';

const platforms = [
    { name: 'Instagram', icon: Instagram, color: 'text-pink-600', bg: 'bg-pink-50 dark:bg-pink-950/30' },
    { name: 'Facebook', icon: Facebook, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-950/30' },
    { name: 'Twitter', icon: Twitter, color: 'text-sky-500', bg: 'bg-sky-50 dark:bg-sky-950/30' },
    { name: 'LinkedIn', icon: Linkedin, color: 'text-blue-700', bg: 'bg-indigo-50 dark:bg-indigo-950/30' },
];

const features = [
    {
        title: 'Smart Scheduling',
        description: 'Plan your content ahead and let Posexei handle the publishing across all your channels.',
        icon: Calendar,
        color: 'text-blue-500',
        bg: 'bg-blue-500/10'
    },
    {
        title: 'Actionable Insights',
        description: 'Understand your audience with deep analytics and engagement trends for every post.',
        icon: BarChart3,
        color: 'text-emerald-500',
        bg: 'bg-emerald-500/10'
    },
    {
        title: 'Goal Tracking',
        description: 'Set monthly targets and track your progress to stay consistent with your strategy.',
        icon: Target,
        color: 'text-purple-500',
        bg: 'bg-purple-500/10'
    },
    {
        title: 'Unified Inbox',
        description: 'Manage all your social interactions from one central dashboard without switching tabs.',
        icon: Share2,
        color: 'text-orange-500',
        bg: 'bg-orange-500/10'
    }
];
</script>

<template>
    <Head title="Posexei - Master Your Social Presence">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-foreground font-sans selection:bg-primary/20">
        <!-- Navigation -->
        <header class="fixed top-0 w-full z-50 border-b border-border/40 bg-background/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex aspect-square size-8 items-center justify-center rounded-lg p-2">
                        <AppLogoIcon class="size-4 fill-current text-white dark:text-black" />
                    </div>
                    <span class="text-lg font-black tracking-tight">Posexei</span>
                </div>

                <nav class="hidden md:flex items-center gap-8 text-sm font-bold text-muted-foreground uppercase tracking-widest">
                    <a href="#features" class="hover:text-primary transition-colors">Features</a>
                    <a href="#platforms" class="hover:text-primary transition-colors">Platforms</a>
                    <a href="#pricing" class="hover:text-primary transition-colors">Pricing</a>
                </nav>

                <div class="flex items-center gap-3">
                    <Link v-if="$page.props.auth.user" :href="dashboard()" class="hidden sm:block">
                        <Button variant="ghost" class="font-bold rounded-xl">Dashboard</Button>
                    </Link>
                    <template v-else>
                        <Link :href="login()">
                            <Button variant="ghost" class="font-bold rounded-xl text-xs uppercase tracking-widest px-6 h-10">Log in</Button>
                        </Link>
                        <Link :href="register()">
                            <Button class="font-black rounded-xl text-xs uppercase tracking-widest px-6 h-10 shadow-lg shadow-primary/20">Get Started</Button>
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="pt-32 pb-20">
            <!-- Hero Section -->
            <section class="max-w-7xl mx-auto px-6 text-center space-y-12">
                <div class="space-y-6 max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
                    <Badge variant="outline" class="rounded-full py-1.5 px-4 text-[10px] font-black uppercase tracking-[0.2em] border-primary/20 text-primary bg-primary/5 shadow-none">
                        Now in Early Access
                    </Badge>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-[1.05] text-foreground">
                        Master your <span class="text-primary italic">social presence</span> in one place.
                    </h1>
                    <p class="text-lg md:text-xl text-muted-foreground font-medium max-w-2xl mx-auto leading-relaxed">
                        Connect all your channels, schedule content with ease, and grow your audience with data-driven insights. 
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <Link :href="register()" class="w-full sm:w-auto">
                            <Button size="lg" class="w-full sm:w-auto rounded-2xl h-14 px-10 text-sm font-black uppercase tracking-widest shadow-xl shadow-primary/30 gap-2">
                                Start Scheduling <ChevronRight class="w-4 h-4 stroke-[3px]" />
                            </Button>
                        </Link>
                        <Button size="lg" variant="outline" class="w-full sm:w-auto rounded-2xl h-14 px-10 text-sm font-black uppercase tracking-widest gap-2 bg-background/50 border-border">
                            <Play class="w-3.5 h-3.5 fill-current" /> Watch Demo
                        </Button>
                    </div>
                </div>

                <!-- Dashboard Preview -->
                <div class="relative pt-10 group max-w-6xl mx-auto animate-in fade-in zoom-in-95 duration-1000 delay-300">
                    <div class="absolute inset-0 -top-20 -bottom-20 bg-linear-to-b from-primary/5 via-transparent to-transparent blur-3xl rounded-full opacity-50 group-hover:opacity-70 transition-opacity"></div>
                    <div class="relative rounded-[2.5rem] border border-border/50 bg-card p-4 md:p-6 shadow-2xl overflow-hidden ring-1 ring-border/20">
                        <!-- Mockup Sidebar -->
                        <div class="absolute left-6 top-6 bottom-6 w-16 hidden lg:flex flex-col items-center py-6 gap-6 bg-muted/30 rounded-3xl border border-border/50">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center p-2">
                                <AppLogoIcon class="size-4 fill-current text-primary" />
                            </div>
                            <div v-for="i in 4" :key="i" class="w-8 h-8 rounded-xl bg-border/40"></div>
                        </div>

                        <!-- Mockup Main Content -->
                        <div class="lg:pl-24 space-y-6 text-left">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border/50 pb-6">
                                <div>
                                    <div class="h-6 w-48 bg-muted rounded-lg mb-2"></div>
                                    <div class="flex gap-2">
                                        <div v-for="p in platforms" :key="p.name" :class="cn('w-6 h-6 rounded-full', p.bg)"></div>
                                    </div>
                                </div>
                                <div class="h-10 w-32 bg-primary/20 rounded-xl border border-primary/20"></div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div v-for="i in 3" :key="i" class="p-6 rounded-[2rem] border border-border/50 bg-muted/20">
                                    <div class="w-10 h-10 rounded-2xl bg-background border border-border mb-4"></div>
                                    <div class="h-6 w-24 bg-foreground/10 rounded-lg mb-2"></div>
                                    <div class="h-4 w-16 bg-muted-foreground/10 rounded-lg"></div>
                                </div>
                            </div>

                            <div class="p-8 rounded-[2rem] border border-border/50 bg-muted/10 h-[240px] flex items-end gap-3 px-6">
                                <div v-for="i in 12" :key="i" class="flex-1 bg-primary/10 rounded-t-xl" :style="{ height: `${Math.random() * 60 + 20}%` }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Social Proof / Platforms -->
            <section id="platforms" class="max-w-7xl mx-auto px-6 py-20 mt-10">
                <div class="text-center space-y-4 mb-12">
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-muted-foreground">Supported Platforms</h2>
                    <p class="text-2xl font-black text-foreground">Sync everything in seconds</p>
                </div>
                <div class="flex flex-wrap justify-center gap-6 md:gap-12 opacity-60">
                    <div v-for="p in platforms" :key="p.name" class="flex items-center gap-3 group grayscale hover:grayscale-0 transition-all duration-500 cursor-default">
                        <div :class="cn('p-3 rounded-2xl transition-all duration-500 group-hover:scale-110 shadow-sm', p.bg)">
                            <component :is="p.icon" :class="cn('w-6 h-6', p.color)" />
                        </div>
                        <span class="text-sm font-black tracking-widest uppercase">{{ p.name }}</span>
                    </div>
                </div>
            </section>

            <!-- Features Grid -->
            <section id="features" class="max-w-7xl mx-auto px-6 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <Badge variant="outline" class="rounded-full py-1 px-4 text-[9px] font-black uppercase tracking-widest border-emerald-500/20 text-emerald-600 bg-emerald-500/5 shadow-none">
                                Powerful Automation
                            </Badge>
                            <h2 class="text-4xl md:text-5xl font-black tracking-tight leading-[1.1]">Built for teams who want to <span class="text-primary italic">scale</span>.</h2>
                            <p class="text-lg text-muted-foreground font-medium leading-relaxed">
                                Manage multiple accounts, collaborate with your team, and never miss a peak engagement window again. Posexei is designed for modern social media managers.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div v-for="feature in features" :key="feature.title" class="space-y-3 p-2 group">
                                <div :class="cn('w-12 h-12 rounded-2xl flex items-center justify-center transition-all group-hover:scale-110 group-hover:shadow-lg', feature.bg)">
                                    <component :is="feature.icon" :class="cn('w-6 h-6', feature.color)" />
                                </div>
                                <h3 class="text-base font-bold text-foreground">{{ feature.title }}</h3>
                                <p class="text-sm text-muted-foreground font-medium leading-relaxed opacity-80">{{ feature.description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 bg-primary/10 blur-[100px] rounded-full opacity-30"></div>
                        <div class="relative space-y-4">
                            <!-- Feature Highlight Card 1 -->
                            <div class="p-6 rounded-[2rem] border border-border/50 bg-card shadow-xl ml-auto max-w-xs animate-in slide-in-from-right-8 duration-700">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600">
                                        <TrendingUp class="w-5 h-5" />
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-widest">Growth Streak</span>
                                </div>
                                <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 w-[78%]"></div>
                                </div>
                                <p class="text-[11px] font-bold text-muted-foreground mt-3 uppercase tracking-widest">+12.4% THIS WEEK</p>
                            </div>

                            <!-- Feature Highlight Card 2 -->
                            <div class="p-8 rounded-[2rem] border border-border/50 bg-primary text-primary-foreground shadow-2xl relative z-10 -mt-6">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Next Post</p>
                                        <h4 class="text-xl font-black">Summer Collection Launch</h4>
                                    </div>
                                    <Badge variant="outline" class="bg-white/10 border-white/20 text-white rounded-full px-3 py-1 font-black text-[9px] uppercase tracking-widest">Scheduled</Badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div v-for="i in 3" :key="i" class="w-8 h-8 rounded-full border-2 border-primary bg-white/20"></div>
                                    </div>
                                    <div class="flex items-center gap-2 font-black text-xs uppercase tracking-widest">
                                        <Clock class="w-4 h-4" /> 2h 45m
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Highlight Card 3 -->
                            <div class="p-6 rounded-[2rem] border border-border/50 bg-card shadow-xl max-w-xs animate-in slide-in-from-left-8 duration-700">
                                <div class="flex items-center gap-3">
                                    <div v-for="i in 3" :key="i" class="w-2 h-2 rounded-full bg-primary/20"></div>
                                    <div class="h-1 w-20 bg-muted rounded-full ml-auto"></div>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <div class="h-4 w-full bg-muted rounded-lg"></div>
                                    <div class="h-4 w-2/3 bg-muted rounded-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section id="pricing" class="max-w-7xl mx-auto px-6 py-20">
                <div class="text-center space-y-4 mb-16">
                    <Badge variant="outline" class="rounded-full py-1.5 px-4 text-[10px] font-black uppercase tracking-[0.2em] border-primary/20 text-primary bg-primary/5 shadow-none">
                        Simple Pricing
                    </Badge>
                    <h2 class="text-4xl md:text-6xl font-black tracking-tight">Plans for every <span class="text-primary italic">stage</span>.</h2>
                    <p class="text-lg text-muted-foreground font-medium max-w-2xl mx-auto">
                        Whether you're just starting out or managing a global brand, we have a plan that fits your needs.
                    </p>
                    
                    <!-- Billing Toggle -->
                    <div class="flex items-center justify-center gap-4 pt-8">
                        <span class="text-xs font-black uppercase tracking-widest text-muted-foreground">Monthly</span>
                        <div class="w-12 h-6 rounded-full bg-muted border border-border p-1 flex items-center cursor-pointer group">
                            <div class="w-4 h-4 rounded-full bg-primary shadow-sm group-hover:scale-110 transition-transform"></div>
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest text-foreground flex items-center gap-2">
                            Yearly <Badge class="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/10 border-none text-[8px] h-4">Save 20%</Badge>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Free Plan -->
                    <div class="rounded-[2.5rem] border border-border/50 bg-card p-8 flex flex-col hover:border-primary/20 transition-all">
                        <div class="space-y-4 mb-8">
                            <h3 class="text-xs font-black uppercase tracking-widest text-muted-foreground">Free</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-black tracking-tight">$0</span>
                                <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">/mo</span>
                            </div>
                            <p class="text-sm font-medium text-muted-foreground leading-relaxed">Perfect for personal hobby projects.</p>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li v-for="feat in ['1 Social Account', '10 Posts / Month', 'Basic Analytics', '7-Day History']" :key="feat" class="flex items-center gap-3 text-xs font-bold text-foreground">
                                <CheckCircle2 class="w-4 h-4 text-primary shrink-0" /> {{ feat }}
                            </li>
                        </ul>
                        <Link :href="register()" class="w-full">
                            <Button variant="outline" class="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest border-border hover:bg-muted">Get Started</Button>
                        </Link>
                    </div>

                    <!-- Starter Plan -->
                    <div class="rounded-[2.5rem] border border-border/50 bg-card p-8 flex flex-col hover:border-primary/20 transition-all ring-1 ring-border/20">
                        <div class="space-y-4 mb-8">
                            <h3 class="text-xs font-black uppercase tracking-widest text-muted-foreground">Starter</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-black tracking-tight">$19</span>
                                <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">/mo</span>
                            </div>
                            <p class="text-sm font-medium text-muted-foreground leading-relaxed">For growing creators and small brands.</p>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li v-for="feat in ['3 Social Accounts', 'Unlimited Posts', 'Growth Trends', '30-Day History', 'Custom Reports']" :key="feat" class="flex items-center gap-3 text-xs font-bold text-foreground">
                                <CheckCircle2 class="w-4 h-4 text-primary shrink-0" /> {{ feat }}
                            </li>
                        </ul>
                        <Link :href="register()" class="w-full">
                            <Button variant="outline" class="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest border-border hover:bg-muted">Start Trial</Button>
                        </Link>
                    </div>

                    <!-- Pro Plan (Featured) -->
                    <div class="rounded-[2.5rem] border-2 border-primary bg-card p-8 flex flex-col shadow-2xl shadow-primary/10 relative">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-primary-foreground px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">Most Popular</div>
                        <div class="space-y-4 mb-8">
                            <h3 class="text-xs font-black uppercase tracking-widest text-primary">Pro</h3>
                            <div class="flex items-baseline gap-1 text-foreground">
                                <span class="text-4xl font-black tracking-tight">$49</span>
                                <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">/mo</span>
                            </div>
                            <p class="text-sm font-medium text-muted-foreground leading-relaxed">Advanced tools for professional teams.</p>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li v-for="feat in ['10 Social Accounts', 'Team Collaboration', 'Advanced Insights', '1-Year History', 'Priority Support', 'White-labeling']" :key="feat" class="flex items-center gap-3 text-xs font-bold text-foreground">
                                <CheckCircle2 class="w-4 h-4 text-primary shrink-0" /> {{ feat }}
                            </li>
                        </ul>
                        <Link :href="register()" class="w-full">
                            <Button class="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20">Upgrade Now</Button>
                        </Link>
                    </div>

                    <!-- Business Plan -->
                    <div class="rounded-[2.5rem] border border-border/50 bg-card p-8 flex flex-col hover:border-primary/20 transition-all bg-linear-to-br from-card to-muted/20">
                        <div class="space-y-4 mb-8">
                            <h3 class="text-xs font-black uppercase tracking-widest text-muted-foreground">Business</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-black tracking-tight">$199</span>
                                <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">/mo</span>
                            </div>
                            <p class="text-sm font-medium text-muted-foreground leading-relaxed">Bespoke solutions for large organizations.</p>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li v-for="feat in ['Unlimited Accounts', 'Dedicated Account Manager', 'Custom Integrations', 'API Access', 'SSO & Security', '24/7 Phone Support']" :key="feat" class="flex items-center gap-3 text-xs font-bold text-foreground">
                                <CheckCircle2 class="w-4 h-4 text-primary shrink-0" /> {{ feat }}
                            </li>
                        </ul>
                        <Link :href="register()" class="w-full">
                            <Button variant="outline" class="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest border-border hover:bg-muted">Contact Sales</Button>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Final CTA -->
            <section id="cta" class="max-w-7xl mx-auto px-6 py-20">
                <div class="relative rounded-[3rem] bg-foreground text-background overflow-hidden p-12 md:p-24 text-center space-y-10 group shadow-3xl">
                    <div class="absolute inset-0 bg-primary/20 mix-blend-overlay opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/20 rounded-full blur-[100px] pointer-events-none"></div>
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary/30 rounded-full blur-[100px] pointer-events-none"></div>
                    
                    <div class="space-y-6 relative z-10 max-w-3xl mx-auto">
                        <h2 class="text-4xl md:text-6xl font-black tracking-tight leading-tight">Ready to take control of your <span class="text-primary underline decoration-4 underline-offset-8 decoration-primary/30">social game?</span></h2>
                        <p class="text-lg md:text-xl text-background/60 font-medium max-w-xl mx-auto">
                            Join over 2,000+ creators and brands who trust Posexei to power their social media presence.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10 pt-4">
                        <Link :href="register()" class="w-full sm:w-auto">
                            <Button size="lg" class="w-full sm:w-auto rounded-2xl h-16 px-12 text-base font-black uppercase tracking-widest shadow-2xl shadow-primary/20 gap-3">
                                Start Your Free Trial <ChevronRight class="w-5 h-5 stroke-[3.5px]" />
                            </Button>
                        </Link>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-background/40">No credit card required</p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-border/40 bg-muted/20 py-20">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="md:col-span-2 space-y-6">
                    <div class="flex items-center gap-2">
                        <div class="flex aspect-square size-8 items-center justify-center rounded-lg p-2">
                            <AppLogoIcon class="size-4 fill-current text-white dark:text-black" />
                        </div>
                        <span class="text-xl font-black tracking-tight uppercase tracking-widest">Posexei</span>
                    </div>
                    <p class="text-sm text-muted-foreground font-medium max-w-xs leading-relaxed">
                        The modern social media management tool built for teams who want to scale their presence across all major platforms.
                    </p>
                    <div class="flex items-center gap-4">
                        <div v-for="p in platforms" :key="p.name" class="p-2 rounded-xl bg-muted border border-border/50 text-muted-foreground hover:text-primary transition-colors cursor-pointer">
                            <component :is="p.icon" class="w-4 h-4" />
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-foreground">Product</h3>
                    <ul class="space-y-4 text-sm font-bold text-muted-foreground uppercase tracking-widest">
                        <li><a href="#" class="hover:text-primary transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Changelog</a></li>
                    </ul>
                </div>

                <div class="space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-foreground">Company</h3>
                    <ul class="space-y-4 text-sm font-bold text-muted-foreground uppercase tracking-widest">
                        <li><a href="#" class="hover:text-primary transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Privacy</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Terms</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="max-w-7xl mx-auto px-6 pt-20 mt-20 border-t border-border/20 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground opacity-50">
                    © 2026 Posexei Inc. All rights reserved.
                </p>
                <div class="flex items-center gap-6 text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground opacity-50">
                    <a href="#" class="hover:text-primary transition-colors">Twitter</a>
                    <a href="#" class="hover:text-primary transition-colors">GitHub</a>
                    <a href="#" class="hover:text-primary transition-colors">Discord</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.shadow-3xl {
    box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
}
</style>
