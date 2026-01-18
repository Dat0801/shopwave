<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <ShopLayout>
        <Head title="Log in" />

        <div class="flex justify-center">
            <div class="w-full max-w-md">
                <div
                    class="mt-12 rounded-2xl bg-white px-6 py-6 shadow-sm ring-1 ring-gray-100 sm:px-8 sm:py-8"
                >
                    <div class="mb-6 text-center">
                        <h1 class="text-xl font-semibold text-gray-900">
                            Welcome back
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Sign in to manage your orders and checkout faster.
                        </p>
                    </div>

                    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Password" />

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                            />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4 block">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                <span class="ms-2 text-sm text-gray-600">
                                    Remember me
                                </span>
                            </label>
                        </div>

                        <div class="mt-4 flex items-center justify-end">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="rounded-md text-sm text-gray-600 underline underline-offset-4 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Forgot your password?
                            </Link>

                            <PrimaryButton
                                class="ms-4"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Log in
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
