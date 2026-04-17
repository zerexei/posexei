<script setup lang="ts">
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.user.avatar && props.user.avatar !== '');
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-full border border-border/50 shadow-sm">
        <AvatarImage v-if="showAvatar" :src="'/storage/' + user.avatar!" :alt="user.name" />
        <AvatarFallback class="rounded-full bg-muted font-black text-[10px] text-foreground">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-[11px] leading-tight ml-2">
        <span class="truncate font-black uppercase tracking-widest text-foreground">{{ user.name }}</span>
        <span v-if="showEmail" class="truncate text-[10px] font-bold text-muted-foreground/60 uppercase tracking-tighter">{{ user.email }}</span>
    </div>
</template>
