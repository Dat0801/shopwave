<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <ShopLayout>
        <Head title="Register" />

        <div class="flex justify-center">
            <div class="w-full max-w-md">
                <div
                    class="mt-12 rounded-2xl bg-white px-6 py-6 shadow-sm ring-1 ring-gray-100 sm:px-8 sm:py-8"
                >
                    <div class="mb-6 text-center">
                        <h1 class="text-xl font-semibold text-gray-900">
                            Create your account
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Shop faster and track your orders in one place.
                        </p>
                    </div>

                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Name" />

                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />

                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
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
                                autocomplete="new-password"
                            />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4">
                            <InputLabel
                                for="password_confirmation"
                                value="Confirm Password"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password_confirmation"
                            />
                        </div>

                        <div class="mt-6 flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                Already registered?
                            </span>

                            <Link
                                :href="route('login')"
                                class="rounded-md font-medium text-gray-700 underline underline-offset-4 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Log in
                            </Link>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <PrimaryButton
                                class="w-full justify-center sm:w-auto sm:px-6"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create account
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
