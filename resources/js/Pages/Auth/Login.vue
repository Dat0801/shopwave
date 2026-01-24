<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ShopHeader from '@/Components/Shop/Header.vue';

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

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen bg-[#F0F6FF] relative overflow-hidden font-sans text-gray-900 flex flex-col">
        <!-- Header -->
        <ShopHeader />

        <!-- Background Decorations -->
        <div class="absolute top-[-10%] left-[-5%] w-[300px] h-[300px] rounded-full bg-[#7CB9F9] opacity-100 hidden lg:block z-0"></div>
        <div class="absolute top-[10%] right-[10%] w-[200px] h-[200px] rounded-full bg-[#99C9FC] opacity-100 hidden lg:block z-0"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] rounded-full bg-[#7CB9F9] opacity-100 hidden lg:block z-0"></div>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center px-4 py-12 relative z-10">
            <div class="w-full max-w-[480px] rounded-2xl bg-white p-8 shadow-xl sm:p-10">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
                    <p class="mt-2 text-sm text-gray-500">Please enter your details to sign in</p>
                </div>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <InputLabel for="email" value="Email or Username" class="text-gray-900 font-semibold" />
                        <TextInput
                            id="email"
                            type="text"
                            class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.email"
                            required
                            autofocus
                            placeholder="name@example.com"
                            autocomplete="username"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <InputLabel for="password" value="Password" class="text-gray-900 font-semibold" />
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-semibold text-[#007AFF] hover:text-blue-600"
                            >
                                Forgot Password?
                            </Link>
                        </div>
                        <div class="relative">
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm pr-10 focus:border-blue-500 focus:ring-blue-500"
                                v-model="form.password"
                                required
                                placeholder="••••••••"
                                autocomplete="current-password"
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <Checkbox 
                            name="remember" 
                            v-model:checked="form.remember" 
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="ms-2 text-sm text-gray-600">Keep me logged in</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        class="w-full rounded-lg bg-[#1A73E8] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        Log In
                    </button>

                    <!-- Divider -->
                    <div class="relative py-2">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-white px-4 text-gray-400 font-medium tracking-wider">or continue with</span>
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </button>
                        <button type="button" class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="mt-6 text-center text-sm text-gray-600">
                        Don't have an account?
                        <Link :href="route('register')" class="font-semibold text-blue-600 hover:text-blue-500">
                            Sign up now
                        </Link>
                    </div>

                    <!-- Footer Text -->
                    <p class="text-center text-xs text-gray-400 mt-6 leading-relaxed">
                        By logging in, you agree to our 
                        <a href="#" class="underline hover:text-gray-600">Terms of Service</a> and 
                        <a href="#" class="underline hover:text-gray-600">Privacy Policy</a>.
                    </p>
                </form>
            </div>
        </div>

        <!-- Copyright -->
        <div class="absolute bottom-6 left-0 right-0 text-center">
             <p class="text-sm text-gray-400">© 2024 ShopWave E-commerce. All rights reserved.</p>
        </div>
    </div>
</template>
