<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const isLoggedIn = computed(() => !!page.props.auth.user);
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <header class="border-b bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
                <div class="flex items-center gap-6">
                    <Link :href="route('shop.index')" class="flex items-center gap-2">
                        <ApplicationLogo class="h-8 w-auto text-indigo-600" />
                        <span class="font-semibold text-lg text-gray-900">Shopwave</span>
                    </Link>

                    <nav class="hidden md:flex items-center gap-4 text-sm text-gray-600">
                        <Link :href="route('shop.index')" :class="['hover:text-gray-900', { 'font-semibold text-gray-900': route().current('shop.index') }]">
                            Shop
                        </Link>
                        <Link v-if="isLoggedIn" :href="route('orders.index')" :class="['hover:text-gray-900', { 'font-semibold text-gray-900': route().current('orders.index') }]">
                            Orders
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <Link :href="route('cart.index')" class="inline-flex items-center text-sm text-gray-700 hover:text-gray-900">
                        <span>Cart</span>
                    </Link>

                    <template v-if="isLoggedIn">
                        <Link :href="route('admin.dashboard')" class="hidden sm:inline-flex text-sm text-gray-700 hover:text-gray-900">
                            Admin
                        </Link>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="inline-flex items-center rounded-md bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-800"
                        >
                            Logout
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium text-gray-700 hover:text-gray-900"
                        >
                            Login
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center rounded-md bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-800"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <footer class="border-t bg-white">
            <div class="mx-auto max-w-7xl px-4 py-4 text-xs text-gray-500 sm:px-6 lg:px-8">
                © {{ new Date().getFullYear() }} Shopwave.
            </div>
        </footer>
    </div>
</template>

