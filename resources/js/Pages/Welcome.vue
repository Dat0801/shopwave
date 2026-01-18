<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Shopwave" />
    <div class="min-h-screen bg-gradient-to-b from-indigo-50 via-white to-white">
        <div
            class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-6 sm:px-8 lg:px-10"
        >
            <header class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white"
                    >
                        <span class="text-lg font-semibold">
                            Sw
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-tight text-gray-900">
                            Shopwave
                        </p>
                        <p class="text-xs text-gray-500">
                            Simple shop experience
                        </p>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-3 text-sm">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-full px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-100"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-full px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-100"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-full bg-gray-900 px-3 py-1.5 font-medium text-white hover:bg-gray-800"
                        >
                            Sign up
                        </Link>
                    </template>
                </nav>
            </header>

            <main
                class="mt-12 flex flex-1 flex-col gap-12 lg:mt-16 lg:flex-row lg:items-center lg:gap-16"
            >
                <section class="max-w-xl space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-medium text-indigo-700">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-[10px] text-white">
                            ●
                        </span>
                        Smart storefront starter for small teams
                    </div>
                    <h1
                        class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl"
                    >
                        Manage products and orders
                        <span class="block text-indigo-600">
                            in one clean, simple interface.
                        </span>
                    </h1>
                    <p class="text-sm leading-relaxed text-gray-600 sm:text-base">
                        Shopwave gives you a focused shopping flow: browse products, add
                        to cart and check out without extra clutter. Perfect for small
                        stores and side projects.
                    </p>

                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            :href="route('shop.index')"
                            class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            Start shopping
                        </Link>

                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('orders.index')"
                            class="inline-flex items-center justify-center rounded-full border border-transparent px-4 py-2 text-sm font-medium text-gray-700 hover:border-gray-200 hover:bg-gray-50"
                        >
                            View your orders
                        </Link>
                        <Link
                            v-else
                            :href="route('login')"
                            class="inline-flex items-center justify-center rounded-full border border-transparent px-4 py-2 text-sm font-medium text-gray-700 hover:border-gray-200 hover:bg-gray-50"
                        >
                            Or log in to manage your orders
                        </Link>
                    </div>

                    <dl
                        class="mt-6 grid grid-cols-2 gap-4 text-xs text-gray-500 sm:text-sm"
                    >
                        <div>
                            <dt class="font-medium text-gray-700">
                                Product discovery
                            </dt>
                            <dd class="mt-1">
                                Filter by category, search quickly and see stock in real time.
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">
                                Cart & checkout
                            </dt>
                            <dd class="mt-1">
                                Add to cart, adjust quantities and check out in just a few steps.
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="flex-1">
                    <div
                        class="rounded-2xl border border-indigo-100 bg-white/80 p-5 shadow-sm backdrop-blur-sm sm:p-6 lg:p-7"
                    >
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">
                                    Storefront preview
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    A compact look at how your product list and cart work together.
                                </p>
                            </div>
                            <span
                                class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700"
                            >
                                Realtime cart
                            </span>
                        </div>

                        <div class="space-y-3">
                            <div
                                class="grid grid-cols-[minmax(0,2fr)_minmax(0,1fr)] gap-3 rounded-xl bg-gray-50 p-3"
                            >
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-medium text-gray-800">
                                            Product list
                                        </p>
                                        <span
                                            class="rounded-full bg-white px-2 py-0.5 text-[10px] font-medium text-gray-500 shadow-sm"
                                        >
                                            Filters · Search
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="rounded-lg bg-white p-2 shadow-sm">
                                            <div
                                                class="aspect-[4/3] rounded-md bg-gray-100"
                                            />
                                            <p
                                                class="mt-2 text-xs font-medium text-gray-900"
                                            >
                                                Basic Tee
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                $19.00 · In stock
                                            </p>
                                        </div>
                                        <div class="rounded-lg bg-white p-2 shadow-sm">
                                            <div
                                                class="aspect-[4/3] rounded-md bg-gray-100"
                                            />
                                            <p
                                                class="mt-2 text-xs font-medium text-gray-900"
                                            >
                                                Cotton Hoodie
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                $39.00 · In stock
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col justify-between rounded-lg bg-gray-900 p-3 text-xs text-white"
                                >
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-100">
                                            Cart
                                        </p>
                                        <ul class="mt-2 space-y-1.5 text-[11px]">
                                            <li
                                                class="flex items-center justify-between"
                                            >
                                                <span class="truncate pr-2">
                                                    Basic Tee × 2
                                                </span>
                                                <span>
                                                    $38.00
                                                </span>
                                            </li>
                                            <li
                                                class="flex items-center justify-between"
                                            >
                                                <span class="truncate pr-2">
                                                    Cotton Hoodie × 1
                                                </span>
                                                <span>
                                                    $39.00
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="mt-3 border-t border-indigo-500/40 pt-3">
                                        <div
                                            class="flex items-center justify-between text-[11px]"
                                        >
                                            <span class="text-indigo-100">
                                                Total
                                            </span>
                                            <span class="font-semibold">
                                                $77.00
                                            </span>
                                        </div>
                                        <button class="mt-2 inline-flex w-full items-center justify-center rounded-full bg-indigo-500 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-indigo-400">
                                            Proceed to checkout
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="grid gap-3 text-xs text-gray-600 sm:grid-cols-3"
                            >
                                <div
                                    class="rounded-xl border border-gray-100 bg-white p-3"
                                >
                                    <p class="text-[11px] font-medium text-gray-700">
                                        Admin dashboard
                                    </p>
                                    <p class="mt-1 text-[11px]">
                                        Manage products, stock levels and orders in a focused back office.
                                    </p>
                                </div>
                                <div
                                    class="rounded-xl border border-gray-100 bg-white p-3"
                                >
                                    <p class="text-[11px] font-medium text-gray-700">
                                        Simple checkout flow
                                    </p>
                                    <p class="mt-1 text-[11px]">
                                        From browsing products to payment in a handful of clear steps.
                                    </p>
                                </div>
                                <div
                                    class="rounded-xl border border-gray-100 bg-white p-3"
                                >
                                    <p class="text-[11px] font-medium text-gray-700">
                                        Built with Laravel
                                    </p>
                                    <p class="mt-1 text-[11px]">
                                        Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer
                class="mt-10 flex items-center justify-between border-t border-gray-100 pt-4 text-xs text-gray-500"
            >
                <p>
                    © {{ new Date().getFullYear() }} Shopwave. All rights reserved.
                </p>
                <p class="hidden sm:block">
                    Log in to access the admin dashboard and track your orders.
                </p>
            </footer>
        </div>
    </div>
</template>
