<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}
defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = computed(() => page.props.auth.user);

const avatarInput = ref<HTMLInputElement | null>(null);
const preview = ref<string>('');

const userAvatar = computed(() => {
    if (preview.value) return preview.value;

    if (user.value.avatar) {
        return '/storage/' + user.value.avatar;
    }

    return '/storage/avatars/default.png';
});

const handleAvatarChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    // Revoke the old preview URL if it exists to avoid memory leaks
    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = URL.createObjectURL(file);
};

const resetAvatarInput = () => {
    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }

    if (avatarInput.value) {
        avatarInput.value.value = '';
    }

    preview.value = '';
};

watch(
    () => user.value.avatar,
    () => {
        resetAvatarInput();
    },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <Form v-bind="ProfileController.update.form()" class="space-y-6" v-slot="{ errors, processing, recentlySuccessful }">
                    <div class="grid gap-2">
                        <Label for="avatar">Avatar</Label>

                        <div class="relative h-37 w-36 border-2 border-dashed">
                            <label for="avatar" class="group hover:cursor-pointer">
                                <input
                                    ref="avatarInput"
                                    id="avatar"
                                    type="file"
                                    class="hidden"
                                    name="avatar"
                                    accept="image/*"
                                    @change="handleAvatarChange"
                                />
                                <img :src="userAvatar" alt="user avatar" class="h-full w-full object-cover" />

                                <div class="flex-center absolute inset-0 bg-black/80 opacity-0 transition group-hover:opacity-100">
                                    <span class="text-sm text-white">Change</span>
                                </div>
                            </label>

                            <button
                                v-show="preview"
                                @click="resetAvatarInput"
                                type="button"
                                class="absolute -top-4 -right-4 cursor-pointer text-red-500 hover:text-red-600"
                            >
                                &times;
                            </button>
                        </div>

                        <InputError class="mt-2" :message="errors.avatar" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            :default-value="user.email"
                            disabled
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
