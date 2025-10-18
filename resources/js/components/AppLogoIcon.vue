<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img src="https://avatars.githubusercontent.com/u/69749146" alt="logo" class="size-full object-contain rounded-md" />
</template>
