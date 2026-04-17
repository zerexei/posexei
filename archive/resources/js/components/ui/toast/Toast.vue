<script setup lang="ts">
import { AlertTriangle, CheckCircle2, X, XCircle } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import type { Toast } from './useToast';

const props = defineProps<{
    toast: Toast;
}>();

const emit = defineEmits<{
    (e: 'close', id: string): void;
}>();

const variants = {
    success: {
        container: 'bg-emerald-50 border-emerald-500/50 text-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-50',
        icon: CheckCircle2,
        iconColor: 'text-emerald-500',
    },
    warning: {
        container: 'bg-amber-50 border-amber-500/50 text-amber-900 dark:bg-amber-950/50 dark:text-amber-50',
        icon: AlertTriangle,
        iconColor: 'text-amber-500',
    },
    danger: {
        container: 'bg-destructive/10 border-destructive/50 text-destructive dark:bg-destructive/10 dark:text-destructive',
        icon: XCircle,
        iconColor: 'text-destructive',
    },
};

const variant = props.toast.variant ? variants[props.toast.variant] : null;
</script>

<template>
    <div
        :class="
            cn(
                'group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-2xl border p-4 shadow-lg transition-all',
                variant ? variant.container : 'bg-background border-border text-foreground',
            )
        "
    >
        <div class="flex gap-3">
            <component v-if="variant" :is="variant.icon" :class="cn('h-5 w-5', variant.iconColor)" />
            <div class="grid gap-1">
                <h4 v-if="toast.title" class="text-sm font-semibold leading-none">{{ toast.title }}</h4>
                <p v-if="toast.description" class="text-sm opacity-90 leading-tight">{{ toast.description }}</p>
            </div>
        </div>

        <button
            @click="emit('close', toast.id)"
            class="absolute right-2 top-2 rounded-md p-1 opacity-0 transition-opacity hover:bg-black/5 group-hover:opacity-100 focus:opacity-100 focus:outline-none dark:hover:bg-white/10"
        >
            <X class="h-4 w-4" />
        </button>
    </div>
</template>
