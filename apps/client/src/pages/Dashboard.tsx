import * as React from 'react';
import { 
    BarChart2, 
    TrendingUp, 
    Users, 
    Plus, 
    Zap, 
    Clock, 
    Target,
    ChevronRight,
    Calendar as CalendarIcon,
    ExternalLink
} from 'lucide-react';
import { Instagram, Facebook, Twitter, Linkedin } from '@/components/SocialIcons';
import { Link } from 'react-router-dom';

import { AppLayout } from '@/layouts/AppLayout';
import { 
    Card, 
    CardContent, 
    CardHeader, 
    CardTitle, 
    CardDescription,
    Button,
    Badge
} from '@/components/ui/core';
import { cn } from '@/lib/utils';
import { 
    mockUser, 
    mockConnections, 
    mockUpcoming, 
    mockLatestPosts, 
    mockStats 
} from '@/mocks';

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

import { useTitle } from '@/hooks/use-title';

export const Dashboard: React.FC = () => {
    useTitle('Dashboard');
    const user = mockUser;
    const connections = mockConnections;
    const upcoming = mockUpcoming;
    const latestPosts = mockLatestPosts;
    const stats = mockStats;

    const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }];

    const goalPercentage = stats ? Math.round((stats.posts_this_month / stats.monthly_target) * 100) : 0;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6 max-w-7xl mx-auto space-y-8 animate-in fade-in duration-500">
                
                {/* Header */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="text-left">
                        <h1 className="text-2xl font-black tracking-tight text-foreground">Welcome back, {user.name.split(' ')[0]}!</h1>
                        <div className="flex items-center gap-2 mt-1">
                            <div className="flex -space-x-1.5">
                                {connections.map((conn) => {
                                    const Icon = getPlatformIcon(conn.provider);
                                    const brand = getPlatformBrand(conn.provider);
                                    return (
                                        <div 
                                            key={conn.id} 
                                            className={cn('w-5 h-5 rounded-full border border-background flex items-center justify-center shadow-sm', brand.bg)}
                                        >
                                            <Icon className={cn('w-2.5 h-2.5', brand.color)} />
                                        </div>
                                    );
                                })}
                            </div>
                            <span className="text-[11px] font-bold text-muted-foreground uppercase tracking-wider">{connections.length} channels synced</span>
                        </div>
                    </div>
                    <div className="flex items-center gap-3">
                        <Link to="/posts/create">
                            <Button className="rounded-2xl h-11 px-6 font-bold gap-2 shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">
                                <Plus className="w-4 h-4 stroke-[3.5px]" /> New Post
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Main Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {/* Left Column: Metrics & Charts */}
                    <div className="lg:col-span-8 space-y-8">
                        {/* Metrics Row */}
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {dashboardStats.map((stat) => {
                                const Icon = stat.icon;
                                return (
                                    <div key={stat.label} className="p-5 rounded-3xl border border-border bg-card shadow-xs group hover:border-primary/20 transition-all text-left">
                                        <div className="flex items-center justify-between mb-4">
                                            <div className={cn('p-2.5 rounded-2xl transition-colors', stat.bg)}>
                                                <Icon className={cn('w-5 h-5', stat.color)} />
                                            </div>
                                            <Badge variant="outline" className="text-[9px] font-black uppercase border-emerald-500/20 text-emerald-600 bg-emerald-500/5 shadow-none">{stat.change}</Badge>
                                        </div>
                                        <div className="space-y-0.5">
                                            <h3 className="text-2xl font-black text-foreground">{stat.value}</h3>
                                            <p className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest opacity-60">{stat.label}</p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Performance Chart Placeholder */}
                        <Card className="rounded-[2rem] border-border bg-card shadow-none overflow-hidden">
                            <CardHeader className="p-8 pb-0 flex flex-row items-center justify-between text-left">
                                <div className="space-y-1">
                                    <CardTitle className="text-base font-bold">Engagement Trends</CardTitle>
                                    <CardDescription className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/50">Last 14 Days</CardDescription>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Button variant="outline" size="sm" className="rounded-xl h-8 text-[10px] font-black uppercase tracking-widest gap-2 bg-background border-border">
                                        <CalendarIcon className="w-3 h-3 opacity-50" /> Weekly
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent className="p-8 pt-6">
                                <div className="h-[240px] w-full flex items-end gap-2 px-2">
                                    {[...Array(14)].map((_, i) => (
                                        <div 
                                            key={i} 
                                            className="flex-1 bg-primary/10 hover:bg-primary/30 transition-all rounded-t-xl relative group" 
                                            style={{ height: `${Math.random() * 60 + 20}%` }}
                                        >
                                            <div className="absolute -top-10 left-1/2 -translate-x-1/2 bg-foreground text-background text-[9px] font-black py-1.5 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap shadow-xl z-20">
                                                {Math.floor(Math.random() * 500)} eng
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <div className="flex justify-between mt-6 px-2 pt-4 border-t border-border/50 text-[9px] font-black text-muted-foreground uppercase tracking-[0.2em] opacity-40">
                                    <span>Feb 15</span>
                                    <span>Today</span>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Recent Activity Feed */}
                        <div className="space-y-4 text-left">
                            <div className="flex items-center justify-between px-2">
                                <h2 className="text-[11px] font-black uppercase tracking-[0.2em] text-muted-foreground/60">Recent Activity Feed</h2>
                                <Link to="/posts" className="text-[10px] font-black uppercase tracking-widest text-primary hover:text-primary/80 transition-colors">View full history</Link>
                            </div>
                            <div className="grid grid-cols-1 gap-2">
                                {latestPosts.map((post) => (
                                    <div 
                                        key={post.id} 
                                        className="flex items-center justify-between p-3 rounded-2xl bg-card border border-border/50 hover:bg-muted/30 hover:border-primary/10 transition-all group cursor-default"
                                    >
                                        <div className="flex items-center gap-4 min-w-0">
                                            <div className="flex -space-x-2 shrink-0">
                                                {post.platforms.map((p) => {
                                                    const Icon = getPlatformIcon(p);
                                                    const brand = getPlatformBrand(p);
                                                    return (
                                                        <div 
                                                            key={p} 
                                                            className={cn('w-8 h-8 rounded-full border-2 border-background flex items-center justify-center shadow-xs', brand.bg)}
                                                        >
                                                            <Icon className={cn('w-3.5 h-3.5', brand.color)} />
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                            <div className="min-w-0 text-left">
                                                <p className="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{post.title}</p>
                                                <div className="flex items-center gap-2 mt-0.5">
                                                    <span className="text-[9px] font-black text-muted-foreground uppercase opacity-50">{post.date}</span>
                                                    <span className="w-1 h-1 rounded-full bg-border"></span>
                                                    <span className="text-[9px] font-bold text-emerald-500 uppercase tracking-tighter">Successfully Published</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-3 shrink-0">
                                            <div className="hidden sm:flex flex-col items-end pr-4 border-r border-border/50">
                                                <span className="text-[10px] font-black text-foreground">{Math.floor(Math.random() * 500 + 100)}</span>
                                                <span className="text-[8px] font-black text-muted-foreground uppercase tracking-widest opacity-40">Eng.</span>
                                            </div>
                                            <Link to={`/posts/${post.id}`}>
                                                <Button variant="ghost" size="icon" className="h-8 w-8 rounded-xl hover:bg-primary/10 group-hover:text-primary transition-all">
                                                    <ExternalLink className="w-3.5 h-3.5" />
                                                </Button>
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Goal & Timeline */}
                    <div className="lg:col-span-4 space-y-6">
                        {/* Goal Progress Widget */}
                        <Card className="rounded-[2rem] border-border bg-linear-to-br from-card to-muted/20 shadow-none overflow-hidden text-foreground text-left">
                            <CardHeader className="p-6 pb-2">
                                <CardTitle className="text-sm font-bold flex items-center gap-2">
                                    <Target className="w-4 h-4 text-primary" /> Monthly Goal
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="p-6 pt-2 space-y-5">
                                <div className="space-y-3">
                                    <div className="flex items-end justify-between">
                                        <h4 className="text-3xl font-black text-foreground">{goalPercentage}%</h4>
                                        {stats && <span className="text-[10px] font-black text-muted-foreground uppercase mb-1">{stats.posts_this_month} / {stats.monthly_target} Posts</span>}
                                    </div>
                                    <div className="h-2 w-full bg-muted rounded-full overflow-hidden">
                                        <div className="h-full bg-primary transition-all duration-1000 shadow-[0_0_8px_rgba(var(--primary),0.4)]" style={{ width: `${goalPercentage}%` }}></div>
                                    </div>
                                </div>
                                {stats && (
                                    <p className="text-[11px] text-muted-foreground font-medium leading-relaxed italic border-l-2 border-primary/20 pl-3">
                                        You're on track! Only {stats.monthly_target - stats.posts_this_month} more posts to hit your target.
                                    </p>
                                )}
                            </CardContent>
                        </Card>

                        {/* Compact Timeline */}
                        <Card className="rounded-[2rem] border-border bg-card shadow-none flex-1 overflow-hidden text-left">
                            <CardHeader className="p-6 pb-4 border-b border-border/50 bg-muted/10">
                                <CardTitle className="text-xs font-black uppercase tracking-widest text-muted-foreground flex items-center gap-2">
                                    <Clock className="w-3.5 h-3.5" /> Next in Queue
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="p-0">
                                {upcoming.length > 0 ? (
                                    <div className="divide-y divide-border/30">
                                        {upcoming.slice(0, 3).map((post) => (
                                            <div 
                                                key={post.id} 
                                                className="p-5 hover:bg-muted/30 transition-all flex items-center justify-between gap-4 cursor-pointer group"
                                            >
                                                <div className="min-w-0">
                                                    <p className="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{post.title}</p>
                                                    <p className="text-[9px] font-black text-muted-foreground uppercase mt-1 opacity-60 tracking-wider font-sans">{post.date.split(',')[0]}</p>
                                                </div>
                                                <div className="flex -space-x-1.5 shrink-0 transition-transform group-hover:translate-x-1">
                                                    {post.platforms.map((p) => {
                                                        const Icon = getPlatformIcon(p);
                                                        const brand = getPlatformBrand(p);
                                                        return (
                                                            <div key={p} className={cn('w-6 h-6 rounded-full border-2 border-background flex items-center justify-center shadow-xs', brand.bg)}>
                                                                <Icon className={cn('w-3 h-3', brand.color)} />
                                                            </div>
                                                        );
                                                    })}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="py-12 text-center">
                                        <p className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest leading-relaxed">Queue empty</p>
                                    </div>
                                )}
                            </CardContent>
                            <div className="p-4 bg-muted/20 border-t border-border/50">
                                <Link to="/posts" className="block">
                                    <Button variant="ghost" className="w-full rounded-xl h-10 text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-muted gap-2">
                                        Manage Schedule <ChevronRight className="w-3 h-3" />
                                    </Button>
                                </Link>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default Dashboard;
