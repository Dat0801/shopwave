<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const profileForm = useForm({
    name: user.value.name,
    email: user.value.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const deleteForm = useForm({
    password: '',
});

const passwordCurrentInput = ref(null);
const passwordNewInput = ref(null);
const deletePasswordInput = ref(null);
const showDeleteModal = ref(false);

const submitProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const submitPassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordNewInput.value?.focus();
            }

            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                passwordCurrentInput.value?.focus();
            }
        },
    });
};

const openDeleteModal = () => {
    showDeleteModal.value = true;

    nextTick(() => {
        deletePasswordInput.value?.focus();
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deleteForm.clearErrors();
    deleteForm.reset();
};

const submitDelete = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => deletePasswordInput.value?.focus(),
    });
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">
                        Profile
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage your account profile and security settings.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-5xl space-y-8 sm:px-6 lg:px-8">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Personal information
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Update your display name and email address.
                            </p>
                        </div>
                        <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 sm:inline-flex">
                            Profile
                        </span>
                    </div>

                    <form class="mt-6 grid gap-4 sm:grid-cols-2" @submit.prevent="submitProfile">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-medium text-gray-700">
                                Full name
                            </label>
                            <Input
                                v-model="profileForm.name"
                                type="text"
                                class="mt-1"
                                autocomplete="name"
                            />
                            <InputError class="mt-1 text-xs" :message="profileForm.errors.name" />
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-xs font-medium text-gray-700">
                                Email
                            </label>
                            <Input
                                v-model="profileForm.email"
                                type="email"
                                class="mt-1"
                                autocomplete="username"
                            />
                            <InputError class="mt-1 text-xs" :message="profileForm.errors.email" />

                            <div v-if="props.mustVerifyEmail && user && user.email_verified_at === null" class="mt-2 text-xs text-gray-600">
                                Your email address is unverified.
                                <Link
                                    :href="route('verification.send')"
                                    method="post"
                                    as="button"
                                    class="ml-1 text-indigo-600 underline hover:text-indigo-800"
                                >
                                    Click here to re-send the verification email.
                                </Link>
                                <p v-if="props.status === 'verification-link-sent'" class="mt-1 font-medium text-green-600">
                                    A new verification link has been sent to your email address.
                                </p>
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-2">
                            <p
                                v-if="profileForm.recentlySuccessful"
                                class="text-xs text-gray-500"
                            >
                                Saved.
                            </p>
                            <Button
                                type="submit"
                                :disabled="profileForm.processing"
                            >
                                Save changes
                            </Button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Change password
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Use a strong, unique password to keep your account secure.
                            </p>
                        </div>
                    </div>

                    <form class="mt-6 grid gap-4 sm:grid-cols-2" @submit.prevent="submitPassword">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">
                                Current password
                            </label>
                            <TextInput
                                id="current_password"
                                ref="passwordCurrentInput"
                                v-model="passwordForm.current_password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="current-password"
                            />
                            <InputError
                                :message="passwordForm.errors.current_password"
                                class="mt-1 text-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700">
                                New password
                            </label>
                            <TextInput
                                id="password"
                                ref="passwordNewInput"
                                v-model="passwordForm.password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <InputError
                                :message="passwordForm.errors.password"
                                class="mt-1 text-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700">
                                Confirm new password
                            </label>
                            <TextInput
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <InputError
                                :message="passwordForm.errors.password_confirmation"
                                class="mt-1 text-xs"
                            />
                        </div>

                        <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-2">
                            <p
                                v-if="passwordForm.recentlySuccessful"
                                class="text-xs text-gray-500"
                            >
                                Password updated.
                            </p>
                            <Button
                                type="submit"
                                :disabled="passwordForm.processing"
                            >
                                Update password
                            </Button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-red-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-red-700">
                                Delete account
                            </h3>
                            <p class="mt-1 text-xs text-red-500">
                                Deleting your account will permanently remove all related data.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            Make sure you have downloaded any important data before deleting.
                        </p>
                        <DangerButton @click="openDeleteModal">
                            Delete account
                        </DangerButton>
                    </div>
                </section>
            </div>
        </div>

        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-base font-semibold text-gray-900">
                    Are you sure you want to delete your account?
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Once your account is deleted, all of its resources and data will be permanently removed.
                    Enter your password to confirm this action.
                </p>

                <div class="mt-4">
                    <label class="sr-only" for="delete_password">Password</label>
                    <TextInput
                        id="delete_password"
                        ref="deletePasswordInput"
                        v-model="deleteForm.password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="Password"
                        @keyup.enter="submitDelete"
                    />
                    <InputError :message="deleteForm.errors.password" class="mt-2 text-xs" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="closeDeleteModal">
                        Cancel
                    </SecondaryButton>
                    <DangerButton
                        class="ms-1"
                        :class="{ 'opacity-25': deleteForm.processing }"
                        :disabled="deleteForm.processing"
                        @click="submitDelete"
                    >
                        Delete account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
