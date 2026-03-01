<script setup lang="ts">
import { cn } from '@/lib/utils';
import { TabsRoot, type TabsRootProps, useForwardPropsEmits } from 'reka-ui';

const props = defineProps<TabsRootProps & { class?: string }>();
const emits = defineEmits(['update:modelValue']);

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <TabsRoot v-bind="forwarded" :class="cn('flex flex-col', props.class)">
        <slot />
    </TabsRoot>
</template>
