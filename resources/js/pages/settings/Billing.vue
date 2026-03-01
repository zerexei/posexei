<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { Head } from '@inertiajs/vue3';
import { 
    Check, 
    CreditCard, 
    Download, 
    Zap, 
    Clock, 
    AlertCircle, 
    ShieldCheck,
    Star
} from 'lucide-vue-next';
import { cn } from '@/lib/utils';

interface Plan {
    name: string;
    price: string;
    description: string;
    features: string[];
    current: boolean;
}

interface Invoice {
    id: string;
    date: string;
    amount: string;
    status: string;
}

interface CurrentPlan {
    name: string;
    price: string;
    interval: string;
    status: string;
    renews_at: string;
    features: string[];
    payment_method?: {
        brand: string;
        last4: string;
    };
}

defineProps<{
    currentPlan: CurrentPlan;
    invoices: Invoice[];
    plans: Plan[];
}>();

const breadcrumbItems = [
    { title: 'Billing', href: '/settings/billing' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Billing & Subscription" />

        <SettingsLayout class="">
            <div class="space-y-10 pb-20">
                <!-- Header -->
                <div>
                    <HeadingSmall title="Billing & Subscription" description="Manage your organization's plan, payment methods, and invoices." />
                </div>

                <!-- Current Plan Overview -->
                <section class="space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-muted-foreground px-1">Current Status</h3>
                    <Card class="rounded-3xl border-border bg-linear-to-br from-primary/5 to-transparent shadow-none overflow-hidden relative group">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all duration-700"></div>
                        <CardContent class="p-8 relative z-10">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-3 rounded-2xl bg-primary text-primary-foreground shadow-lg shadow-primary/20">
                                            <ShieldCheck class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-black text-foreground">{{ currentPlan.name }}</h4>
                                            <p class="text-sm text-muted-foreground font-medium">Your organization is currently on the {{ currentPlan.name }} package.</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-4 pt-2">
                                        <div class="flex items-center gap-2 text-xs font-bold text-foreground bg-card border border-border px-3 py-2 rounded-xl">
                                            <Badge variant="success" class="h-2 w-2 rounded-full p-0" /> Active
                                        </div>
                                        <div class="flex items-center gap-2 text-xs font-bold text-muted-foreground bg-card/50 border border-border px-3 py-2 rounded-xl">
                                            <Clock class="w-3.5 h-3.5" /> Renews on {{ currentPlan.renews_at }}
                                        </div>
                                        <div v-if="currentPlan.payment_method" class="flex items-center gap-2 text-xs font-bold text-muted-foreground bg-card/50 border border-border px-3 py-2 rounded-xl">
                                            <CreditCard class="w-3.5 h-3.5" /> Card Payment
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 min-w-[160px]">
                                    <div class="text-center md:text-right mb-2">
                                        <span class="text-4xl font-black text-foreground">{{ currentPlan.price }}</span>
                                        <span class="text-sm text-muted-foreground font-bold italic">/{{ currentPlan.interval }}</span>
                                    </div>
                                    <Button class="w-full rounded-2xl font-bold h-11 border-0 shadow-md">Manage Subscription</Button>
                                    <Button variant="ghost" class="w-full rounded-2xl font-bold h-11 text-xs text-muted-foreground">Cancel Plan</Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </section>

                <!-- Comparison Table / Plan Grid -->
                <section class="space-y-6">
                    <div class="flex items-center justify-between px-1">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-muted-foreground">Available Plans</h3>
                        <Badge variant="outline" class="font-black text-[10px] rounded-full px-3 py-1">Annual Save 20%</Badge>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Card v-for="plan in plans" :key="plan.name" 
                              :class="cn('rounded-[2.5rem] border-border shadow-none transition-all flex flex-col', 
                                         plan.current ? 'ring-2 ring-primary bg-primary/[0.02]' : 'hover:border-primary/20')">
                            <CardHeader class="p-8 pb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black uppercase tracking-[0.2em] text-muted-foreground opacity-60">{{ plan.name }}</span>
                                    <Star v-if="plan.name === 'Pro'" class="w-4 h-4 text-primary fill-primary" />
                                </div>
                                <CardTitle class="text-3xl font-black text-foreground">{{ plan.price }}</CardTitle>
                                <CardDescription class="text-xs font-medium pt-2 leading-relaxed">{{ plan.description }}</CardDescription>
                            </CardHeader>
                            <CardContent class="p-8 pt-0 flex-1 flex flex-col">
                                <div class="space-y-4 mb-10 flex-1">
                                    <div v-for="feature in plan.features" :key="feature" class="flex items-start gap-3">
                                        <div class="p-0.5 rounded-full bg-primary/10 text-primary mt-0.5">
                                            <Check class="w-3 h-3 stroke-[3px]" />
                                        </div>
                                        <span class="text-xs font-medium text-foreground/80 leading-tight">{{ feature }}</span>
                                    </div>
                                </div>
                                <Button :variant="plan.current ? 'secondary' : 'outline'" 
                                        :disabled="plan.current"
                                        class="w-full rounded-2xl font-black h-12 uppercase tracking-widest text-[10px]">
                                    {{ plan.current ? 'Current Plan' : 'Upgrade' }}
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </section>

                <!-- Invoice History -->
                <section class="space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-muted-foreground px-1">Payment History</h3>
                    <Card class="rounded-3xl border-border bg-card shadow-none overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-border/50 bg-muted/20">
                                        <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Invoice ID</th>
                                        <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Date</th>
                                        <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Amount</th>
                                        <th class="p-5 font-black uppercase tracking-widest text-[10px] text-muted-foreground">Status</th>
                                        <th class="p-5"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border/30">
                                    <tr v-for="invoice in invoices" :key="invoice.id" class="hover:bg-muted/10 transition-colors group">
                                        <td class="p-5 font-bold text-foreground">{{ invoice.id }}</td>
                                        <td class="p-5 text-muted-foreground font-medium">{{ invoice.date }}</td>
                                        <td class="p-5 font-black text-foreground">{{ invoice.amount }}</td>
                                        <td class="p-5">
                                            <Badge variant="secondary" class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 font-black text-[9px] uppercase">
                                                {{ invoice.status }}
                                            </Badge>
                                        </td>
                                        <td class="p-5 text-right">
                                            <Button variant="ghost" size="icon" class="rounded-xl hover:bg-card hover:shadow-sm">
                                                <Download class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </Card>
                    <p class="text-center text-[10px] font-medium text-muted-foreground pt-2">
                        Tax is calculated based on your organization's location.
                    </p>
                </section>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
