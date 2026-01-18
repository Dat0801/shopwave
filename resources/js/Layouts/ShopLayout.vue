<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useFlash } from '@/Composables/useFlash';

const page = usePage();

const isLoggedIn = computed(() => !!page.props.auth.user);
const cartCount = computed(() => (page.props.cart ? page.props.cart.count : 0));

const { success, error } = useFlash();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100 flex flex-col">
        <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
                <div class="flex items-center gap-3 sm:gap-6">
                    <Link :href="route('shop.index')" class="flex items-center gap-2">
                        <ApplicationLogo class="h-8 w-auto text-indigo-600" />
                        <span class="font-semibold text-lg tracking-tight text-gray-900">Shopwave</span>
                    </Link>

                    <nav class="hidden md:flex items-center gap-3 lg:gap-4 text-sm text-gray-600">
                        <Link
                            :href="route('shop.index')"
                            :class="[
                                'rounded-full px-3 py-1 transition',
                                route().current('shop.index')
                                    ? 'bg-gray-900 text-white shadow-sm'
                                    : 'hover:bg-gray-100 hover:text-gray-900'
                            ]"
                        >
                            Shop
                        </Link>
                        <Link
                            v-if="isLoggedIn"
                            :href="route('orders.index')"
                            :class="[
                                'rounded-full px-3 py-1 transition',
                                route().current('orders.index')
                                    ? 'bg-gray-900 text-white shadow-sm'
                                    : 'hover:bg-gray-100 hover:text-gray-900'
                            ]"
                        >
                            Orders
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <Link
                        :href="route('cart.index')"
                        :class="[
                            'inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium transition',
                            route().current('cart.index')
                                ? 'bg-gray-900 text-white shadow-sm'
                                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                        ]"
                    >
                        <span class="flex items-center gap-1.5">
                            <span>Cart</span>
                            <span
                                v-if="cartCount"
                                class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 text-xs font-semibold text-white"
                            >
                                {{ cartCount }}
                            </span>
                        </span>
                    </Link>

                    <template v-if="isLoggedIn">
                        <Link
                            :href="route('admin.dashboard')"
                            class="hidden sm:inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Admin
                        </Link>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Logout
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            :class="[
                                'inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium transition',
                                route().current('login')
                                    ? 'bg-gray-900 text-white shadow-sm'
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                            ]"
                        >
                            Login
                        </Link>
                        <Link
                            :href="route('register')"
                            :class="[
                                'inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium transition',
                                route().current('register')
                                    ? 'bg-gray-900 text-white shadow-sm'
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                            ]"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div
                    v-if="success || error"
                    class="mb-6 space-y-3"
                >
                    <div
                        v-if="success"
                        class="flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm"
                    >
                        <span class="mt-0.5 h-2 w-2 rounded-full bg-emerald-500" />
                        <p class="flex-1">
                            {{ success }}
                        </p>
                    </div>
                    <div
                        v-if="error"
                        class="flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm"
                    >
                        <span class="mt-0.5 h-2 w-2 rounded-full bg-rose-500" />
                        <p class="flex-1">
                            {{ error }}
                        </p>
                    </div>
                </div>

                <slot />
            </div>
        </main>

        <footer class="border-t border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 py-4 text-xs text-gray-500 sm:px-6 lg:px-8">
                © {{ new Date().getFullYear() }} Shopwave. All rights reserved.
            </div>
        </footer>
    </div>
</template>
