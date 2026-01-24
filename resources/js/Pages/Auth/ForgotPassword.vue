<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import ShopHeader from '@/Components/Shop/Header.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div class="min-h-screen bg-[#F0F6FF] relative overflow-hidden font-sans text-gray-900 flex flex-col">
        <Head title="Forgot Password" />

        <!-- Header -->
        <ShopHeader />

        <!-- Background Decorations -->
        <div class="absolute top-[-10%] left-[-5%] w-[300px] h-[300px] rounded-full bg-[#7CB9F9] opacity-20 hidden lg:block z-0 blur-3xl"></div>
        <div class="absolute top-[10%] right-[10%] w-[200px] h-[200px] rounded-full bg-[#99C9FC] opacity-20 hidden lg:block z-0 blur-3xl"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] rounded-full bg-[#7CB9F9] opacity-20 hidden lg:block z-0 blur-3xl"></div>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="max-w-[480px] w-full bg-white rounded-2xl shadow-xl overflow-hidden relative mb-8">
                <!-- Card Header with Waves -->
                <div class="h-40 bg-blue-50 relative overflow-hidden flex items-center justify-center">
                    <!-- Waves SVG -->
                    <svg class="absolute bottom-0 left-0 w-full text-white" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="height: 80px;">
                        <path fill="currentColor" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                    </svg>

                    <!-- Icon -->
                    <div class="bg-white p-3 rounded-2xl shadow-sm z-10 mb-2">
                        <div class="bg-blue-500 rounded-xl p-2.5">
                             <!-- Lock Icon with Refresh arrow hint (using standard refresh/sync icon for 'recovery' or just a lock) -->
                             <!-- Using a Lock with a refresh circular arrow if possible, or just a refresh icon as 'recovery' -->
                             <!-- Let's use a Padlock icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="px-8 pb-10 pt-2">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
                        <p class="text-gray-500 text-sm leading-relaxed px-4">
                            Don't worry, it happens. Enter your email below to recover your account.
                        </p>
                    </div>

                    <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center bg-green-50 p-3 rounded-lg border border-green-100">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email Field -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-sm font-semibold text-gray-700">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 transition-colors placeholder-gray-400"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    placeholder="e.g., alex@example.com"
                                />
                            </div>
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 hover:scale-[1.01]"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Send Reset Link
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="mt-8 text-center">
                        <Link :href="route('login')" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Back to Login
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
             <p class="text-xs text-gray-400 text-center">
                By continuing, you agree to ShopWave's <a href="#" class="underline hover:text-gray-600">Terms of Service</a> and <a href="#" class="underline hover:text-gray-600">Privacy Policy</a>.
             </p>
        </main>

        <!-- Footer -->
        <footer class="w-full py-6 px-4 sm:px-8 flex flex-col md:flex-row items-center justify-between text-xs text-gray-400 relative z-20 border-t border-gray-100 bg-white/50 backdrop-blur-sm">
            <div>&copy; 2024 ShopWave Inc. All rights reserved.</div>
            <div class="flex items-center gap-4 mt-2 md:mt-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer hover:text-gray-600">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer hover:text-gray-600">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
            </div>
        </footer>
    </div>
</template>
