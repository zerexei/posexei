<script setup lang="ts">
import { cn } from '@/lib/utils';
import { cva, type VariantProps } from 'class-variance-authority';

const badgeVariants = cva(
    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
    {
        variants: {
            variant: {
                default: 'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
                secondary: 'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
                destructive: 'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
                outline: 'text-foreground',
                success: 'border-transparent bg-emerald-500 text-white hover:bg-emerald-500/80',
                warning: 'border-transparent bg-amber-500 text-white hover:bg-amber-500/80',
                info: 'border-transparent bg-blue-500 text-white hover:bg-blue-500/80',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    },
);

interface BadgeProps extends /* @vue-ignore */ VariantProps<typeof badgeVariants> {
    class?: string;
    variant?: NonNullable<VariantProps<typeof badgeVariants>['variant']>;
}

defineProps<BadgeProps>();
</script>

<template>
    <div :class="cn(badgeVariants({ variant }), $props.class)">
        <slot />
    </div>
</template>
