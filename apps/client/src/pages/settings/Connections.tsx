import * as React from 'react';
import { AppLayout } from '@/layouts/AppLayout';
import { SettingsLayout } from '@/layouts/SettingsLayout';
import { Heading } from '@/components/Heading';
import { Button, Card, CardContent, Badge, Input, Label, Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/core';
import { 
    Plus, 
    CheckCircle2, 
    RefreshCw, 
    ExternalLink, 
    Trash2,
    Info,
    AlertCircle,
    XCircle
} from 'lucide-react';
import { Facebook, Instagram, Linkedin, Twitter } from '@/components/SocialIcons';
import { cn } from '@/lib/utils';
import { useTitle } from '@/hooks/use-title';

interface Connection {
    id: number;
    provider: 'facebook' | 'linkedin' | 'twitter' | 'instagram';
    name: string;
    connected_at: string;
    status: 'connected' | 'processing' | 'disconnected' | 'expired';
}

const providers = [
    { id: 'facebook', name: 'Facebook', icon: Facebook, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-950/30' },
    { id: 'instagram', name: 'Instagram', icon: Instagram, color: 'text-pink-600', bg: 'bg-pink-50 dark:bg-pink-950/30' },
    { id: 'linkedin', name: 'LinkedIn', icon: Linkedin, color: 'text-blue-700', bg: 'bg-indigo-50 dark:bg-indigo-950/30' },
    { id: 'twitter', name: 'Twitter / X', icon: Twitter, color: 'text-sky-500', bg: 'bg-sky-50 dark:bg-sky-950/30' },
] as const;

export const Connections: React.FC = () => {
    useTitle('Social Connections');
    const [selectedProvider, setSelectedProvider] = React.useState<typeof providers[number] | null>(null);
    const [isDialogOpen, setIsDialogOpen] = React.useState(false);

    const connections: Connection[] = [
        { id: 1, provider: 'instagram', name: '@posexei', connected_at: 'Feb 15, 2026', status: 'connected' },
        { id: 2, provider: 'facebook', name: 'Posexei Official', connected_at: 'Feb 14, 2026', status: 'connected' },
    ];

    const breadcrumbs = [
        { title: 'Settings', href: '/settings/profile' },
        { title: 'Connections', href: '/settings/connections' },
    ];

    const getStatusBadge = (status: Connection['status']) => {
        switch (status) {
            case 'connected': return { variant: 'success' as const, icon: CheckCircle2, text: 'Connected' };
            case 'processing': return { variant: 'secondary' as const, icon: RefreshCw, text: 'Processing' };
            case 'expired': return { variant: 'warning' as const, icon: AlertCircle, text: 'Expired' };
            case 'disconnected': return { variant: 'destructive' as const, icon: XCircle, text: 'Disconnected' };
            default: return { variant: 'secondary' as const, icon: AlertCircle, text: 'Unknown' };
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <SettingsLayout>
                <div className="space-y-10 text-left">
                    <Heading 
                        title="Social Connections" 
                        description="Link your social media accounts to start publishing content." 
                    />

                    {/* Add Connection Grid */}
                    <section className="space-y-4">
                        <h3 className="flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground uppercase opacity-60 px-1">
                            <Plus className="h-3 w-3" />
                            Connect New Platform
                        </h3>
                        <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                            {providers.map((provider) => (
                                <button
                                    key={provider.id}
                                    onClick={() => {
                                        setSelectedProvider(provider);
                                        setIsDialogOpen(true);
                                    }}
                                    className="group flex flex-col items-center justify-center rounded-3xl border border-border bg-card p-6 transition-all duration-300 hover:border-primary/30 hover:bg-primary/5 hover:shadow-xl hover:shadow-primary/[0.02]"
                                >
                                    <div className={cn('mb-4 rounded-2xl p-4 transition-transform group-hover:scale-110 duration-500 shadow-sm', provider.bg)}>
                                        <provider.icon className={cn('h-7 w-7', provider.color)} />
                                    </div>
                                    <span className="text-sm font-bold tracking-tight">{provider.name}</span>
                                    <span className="mt-1 text-[9px] font-black uppercase tracking-widest text-muted-foreground opacity-40">Ready to link</span>
                                </button>
                            ))}
                        </div>
                    </section>

                    <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                        <DialogContent className="sm:max-w-[425px] rounded-[2.5rem] p-8">
                            <DialogHeader>
                                <DialogTitle className="flex items-center gap-3 text-2xl font-black">
                                    {selectedProvider && (
                                        <div className={cn('p-2.5 rounded-xl', selectedProvider.bg)}>
                                            <selectedProvider.icon className={cn('h-5 w-5', selectedProvider.color)} />
                                        </div>
                                    )}
                                    Connect {selectedProvider?.name}
                                </DialogTitle>
                                <DialogDescription className="pt-2 text-sm font-medium">
                                    Enter your developer access key or API token to establish a secure connection with {selectedProvider?.name}.
                                </DialogDescription>
                            </DialogHeader>

                            <div className="space-y-6 py-6">
                                <div className="space-y-2.5">
                                    <Label htmlFor="access_token" className="text-[10px] font-black uppercase tracking-widest text-muted-foreground px-1">Access Token / API Key</Label>
                                    <Input id="access_token" placeholder="Paste your token here..." className="h-12 rounded-2xl bg-muted/50 border-border px-4" />
                                </div>
                            </div>

                            <DialogFooter>
                                <Button className="w-full h-12 rounded-2xl font-bold shadow-lg shadow-primary/20" onClick={() => setIsDialogOpen(false)}>
                                    Authorize Connection
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>

                    {/* Active Connections */}
                    <section className="space-y-6">
                        <div className="flex items-center justify-between px-1">
                            <h3 className="flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground uppercase opacity-60">
                                <CheckCircle2 className="h-3 w-3" />
                                Your Connected Accounts
                            </h3>
                            <Badge variant="outline" className="font-bold text-[10px] rounded-full px-3">{connections.length} Accounts</Badge>
                        </div>

                        <div className="grid gap-3">
                            {connections.length > 0 ? (
                                connections.map((connection) => {
                                    const provider = providers.find(p => p.id === connection.provider)!;
                                    const status = getStatusBadge(connection.status);
                                    return (
                                        <Card key={connection.id} className="overflow-hidden border-border/50 bg-card shadow-sm hover:border-primary/10 transition-all rounded-[2rem]">
                                            <CardContent className="p-5">
                                                <div className="flex items-center justify-between gap-4">
                                                    <div className="flex min-w-0 items-center gap-4">
                                                        <div className={cn('flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl shadow-inner', provider.bg)}>
                                                            <provider.icon className={cn('h-5 w-5', provider.color)} />
                                                        </div>
                                                        <div className="flex min-w-0 flex-col">
                                                            <div className="flex items-center gap-3">
                                                                <span className="truncate text-base font-bold tracking-tight text-foreground">{connection.name}</span>
                                                                <Badge variant={status.variant} className="h-5 px-2 text-[9px] font-black uppercase tracking-wider rounded-full">
                                                                    {status.text}
                                                                </Badge>
                                                            </div>
                                                            <span className="truncate text-[10px] font-black uppercase tracking-widest text-muted-foreground opacity-40 mt-0.5">
                                                                Linked on {connection.connected_at}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div className="flex items-center gap-2">
                                                        <Button variant="ghost" size="icon" className="h-10 w-10 rounded-xl hover:bg-muted" title="View Profile">
                                                            <ExternalLink className="h-4 w-4 text-muted-foreground" />
                                                        </Button>
                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            className="h-10 w-10 rounded-xl hover:bg-rose-500/10 hover:text-rose-500"
                                                            title="Remove"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    );
                                })
                            ) : (
                                <div className="flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-border/60 bg-muted/20 p-16 text-center">
                                    <div className="mb-6 rounded-full bg-muted/50 p-6 shadow-inner">
                                        <Info className="h-10 w-10 text-muted-foreground/20" />
                                    </div>
                                    <h4 className="text-lg font-bold text-foreground">Discovery Mode</h4>
                                    <p className="mt-2 max-w-xs text-sm font-medium text-muted-foreground leading-relaxed">
                                        Connect your first social media account using the ecosystem grid above to start analyzing metrics.
                                    </p>
                                </div>
                            )}
                        </div>
                    </section>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
};

export default Connections;
