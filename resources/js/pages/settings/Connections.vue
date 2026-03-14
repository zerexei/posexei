<script setup lang="ts">
import SocialAccountController from '@/actions/App/Http/Controllers/Social/SocialAccountController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { useToast } from '@/components/ui/toast/useToast';
import {
    AlertCircle,
    CheckCircle2,
    ExternalLink,
    Facebook,
    Info,
    Instagram,
    Linkedin,
    Plus,
    RefreshCw,
    Trash2,
    Twitter,
    XCircle,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Connection {
    id: number;
    provider: 'facebook' | 'linkedin' | 'twitter' | 'instagram';
    name: string;
    connected_at: string;
    status: 'active' | 'expired' | 'error';
    expires_at?: string;
}

interface Provider {
    id: 'facebook' | 'linkedin' | 'twitter' | 'instagram';
    name: string;
    icon: any;
    color: string;
    bg: string;
    borderColor: string;
}

const props = defineProps<{
    connections: Connection[];
}>();

const providers: Provider[] = [
    {
        id: 'facebook',
        name: 'Facebook',
        icon: Facebook,
        color: 'text-blue-600',
        bg: 'bg-blue-50 dark:bg-blue-950/30',
        borderColor: 'border-blue-100 dark:border-blue-900/50',
    },
    {
        id: 'instagram',
        name: 'Instagram',
        icon: Instagram,
        color: 'text-pink-600',
        bg: 'bg-pink-50 dark:bg-pink-950/30',
        borderColor: 'border-pink-100 dark:border-pink-900/50',
    },
    {
        id: 'linkedin',
        name: 'LinkedIn',
        icon: Linkedin,
        color: 'text-blue-700',
        bg: 'bg-indigo-50 dark:bg-indigo-950/30',
        borderColor: 'border-indigo-100 dark:border-indigo-900/50',
    },
    {
        id: 'twitter',
        name: 'Twitter / X',
        icon: Twitter,
        color: 'text-sky-500',
        bg: 'bg-sky-50 dark:bg-sky-950/30',
        borderColor: 'border-sky-100 dark:border-sky-900/50',
    },
];

const selectedProvider = ref<Provider | null>(null);
const isDialogOpen = ref(false);

const openConnectionDialog = (provider: Provider) => {
    selectedProvider.value = provider;
    isDialogOpen.value = true;
};

const closeConnectionDialog = () => {
    selectedProvider.value = null;
    isDialogOpen.value = false;
};

const { toast } = useToast();

const handleSuccess = () => {
    const providerName = selectedProvider.value?.name;
    closeConnectionDialog();
    toast({
        title: 'Account Connected',
        description: `Successfully connected to ${providerName}.`,
        variant: 'success',
    });
};

const handleError = () => {
    toast({
        title: 'Connection Failed',
        description: 'There was an error connecting your account.',
        variant: 'danger',
    });
};

const connectedProviders = computed(() => {
    const grouped = props.connections.reduce(
        (acc, conn) => {
            if (!acc[conn.provider]) acc[conn.provider] = [];
            acc[conn.provider].push(conn);
            return acc;
        },
        {} as Record<string, Connection[]>,
    );

    return providers
        .filter((p) => grouped[p.id])
        .map((p) => ({
            ...p,
            accounts: grouped[p.id],
        }));
});

const getStatusBadge = (status: Connection['status']) => {
    switch (status) {
        case 'active':
            return { variant: 'success', icon: CheckCircle2, text: 'Active' };
        case 'expired':
            return { variant: 'warning', icon: AlertCircle, text: 'Expired' };
        case 'error':
            return { variant: 'destructive', icon: XCircle, text: 'Error' };
        default:
            return { variant: 'secondary', icon: AlertCircle, text: 'Unknown' };
    }
};

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Connections',
        href: '/settings/connections',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Connections Settings" />

        <SettingsLayout>
            <div class="space-y-10">
                <div>
                    <HeadingSmall title="Social Connections" description="Link your social media accounts to start publishing content." />
                </div>

                <!-- Add Connection Grid -->
                <section class="space-y-4">
                    <h3 class="flex items-center gap-2 text-sm font-bold tracking-wider text-muted-foreground uppercase">
                        <Plus class="h-3.5 w-3.5" />
                        Connect New Platform
                    </h3>
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        <button
                            v-for="provider in providers"
                            :key="provider.id"
                            @click="openConnectionDialog(provider)"
                            class="group flex flex-col items-center justify-center rounded-2xl border border-border bg-card p-4 transition-all duration-200 hover:border-primary/50 hover:bg-primary/5"
                        >
                            <div :class="['mb-3 rounded-xl p-3 transition-transform group-hover:scale-110', provider.bg]">
                                <component :is="provider.icon" :class="['h-6 w-6', provider.color]" />
                            </div>
                            <span class="text-sm font-semibold">{{ provider.name }}</span>
                            <span class="mt-1 text-[10px] text-muted-foreground">Click to connect</span>
                        </button>
                    </div>
                </section>

                <Dialog v-if="selectedProvider" v-model:open="isDialogOpen">
                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle class="flex items-center gap-2">
                                <component :is="selectedProvider?.icon" :class="['h-5 w-5', selectedProvider?.color]" />
                                Connect {{ selectedProvider?.name }}
                            </DialogTitle>
                            <DialogDescription> Enter your access key to connect your {{ selectedProvider?.name }} account. </DialogDescription>
                        </DialogHeader>

                        <Form
                            v-bind="SocialAccountController.store.post()"
                            :reset-on-success="['access_token']"
                            @success="handleSuccess"
                            @error="handleError"
                            v-slot="{ errors, processing }"
                            class="space-y-4 py-4"
                        >
                            <div class="space-y-2">
                                <input type="hidden" name="provider" :value="selectedProvider.id" />
                                <Label for="access_token" class="text-xs font-bold tracking-tight text-muted-foreground uppercase"> Access Key </Label>
                                <Input id="access_token" name="access_token" placeholder="Enter your access key" :disabled="processing" />
                                <span v-if="errors.access_token" class="text-xs text-destructive">{{ errors.access_token }}</span>
                            </div>

                            <DialogFooter>
                                <Button type="submit" :disabled="processing" class="w-full">
                                    <template v-if="processing"> Connecting... </template>
                                    <template v-else> Connect Account </template>
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>

                <!-- Active Connections -->
                <section class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="flex items-center gap-2 text-sm font-bold tracking-wider text-muted-foreground uppercase">
                            <CheckCircle2 class="h-3.5 w-3.5" />
                            Your Connected Accounts
                        </h3>
                        <Badge variant="outline" class="font-mono text-[10px]">{{ connections.length }} Total</Badge>
                    </div>

                    <div v-if="connections.length > 0" class="space-y-8">
                        <div v-for="provider in connectedProviders" :key="provider.id" class="space-y-3">
                            <div class="flex items-center gap-2 px-1">
                                <component :is="provider.icon" :class="['h-4 w-4', provider.color]" />
                                <span class="text-xs font-bold tracking-tight text-foreground/70 uppercase">{{ provider.name }}</span>
                            </div>

                            <div class="grid gap-2">
                                <Card
                                    v-for="connection in provider.accounts"
                                    :key="connection.id"
                                    class="overflow-hidden border-border/60 shadow-none transition-colors hover:border-border"
                                >
                                    <CardContent class="p-3">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-muted">
                                                    <component :is="provider.icon" class="h-4 w-4 text-muted-foreground/60" />
                                                </div>
                                                <div class="flex min-w-0 flex-col">
                                                    <div class="flex items-center gap-2">
                                                        <span class="truncate text-sm font-bold">{{ connection.name }}</span>
                                                        <Badge
                                                            :variant="getStatusBadge(connection.status).variant as any"
                                                            class="h-4 px-1.5 text-[9px] font-black uppercase"
                                                        >
                                                            {{ getStatusBadge(connection.status).text }}
                                                        </Badge>
                                                    </div>
                                                    <span class="truncate text-[10px] text-muted-foreground">
                                                        Connected on {{ connection.connected_at }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1">
                                                <Button
                                                    v-if="connection.status !== 'active'"
                                                    variant="outline"
                                                    size="icon"
                                                    class="h-7 w-7"
                                                    title="Reconnect"
                                                >
                                                    <RefreshCw class="h-3.5 w-3.5" />
                                                </Button>
                                                <Button variant="ghost" size="icon" class="h-7 w-7" title="View Profile">
                                                    <ExternalLink class="h-3.5 w-3.5 text-muted-foreground" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    class="h-7 w-7 hover:bg-destructive/10 hover:text-destructive"
                                                    title="Remove"
                                                >
                                                    <Trash2 class="h-3.5 w-3.5" />
                                                </Button>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-border bg-muted/10 p-12 text-center"
                    >
                        <div class="mb-4 rounded-full bg-muted/50 p-4">
                            <Info class="h-8 w-8 text-muted-foreground/30" />
                        </div>
                        <h4 class="font-bold text-foreground">No accounts connected</h4>
                        <p class="mt-1 max-w-60 text-sm text-muted-foreground">
                            Connect your first social media account using the grid above to get started.
                        </p>
                    </div>
                </section>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
