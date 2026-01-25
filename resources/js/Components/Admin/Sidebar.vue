<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    showingNavigationDropdown: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transition-transform duration-300 ease-in-out md:translate-x-0" :class="{ '-translate-x-full': !showingNavigationDropdown, 'translate-x-0': showingNavigationDropdown }">
        <div class="flex h-16 items-center justify-center border-b border-gray-100 px-6">
            <Link :href="route('admin.dashboard')" class="flex items-center gap-2">
                <div class="rounded-lg bg-blue-600 p-1">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold text-gray-900 leading-none">ShopWave</span>
                    <span class="text-[10px] text-gray-500 font-medium tracking-wider uppercase">Admin Console</span>
                </div>
            </Link>
        </div>

        <div class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] flex flex-col">
            <nav class="space-y-1 flex-1">
                <Link :href="route('admin.dashboard')" :class="{'bg-blue-50 text-blue-600': route().current('admin.dashboard'), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !route().current('admin.dashboard')}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </Link>

                <Link :href="route('admin.banners.index')" :class="{'bg-blue-50 text-blue-600': route().current('admin.banners.*'), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !route().current('admin.banners.*')}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Banners
                </Link>

                <Link :href="route('admin.orders.index')" :class="{'bg-blue-50 text-blue-600': route().current('admin.orders.*'), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !route().current('admin.orders.*')}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Orders
                </Link>

                <Link :href="route('admin.categories.index')" :class="{'bg-blue-50 text-blue-600': route().current('admin.categories.*'), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !route().current('admin.categories.*')}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Categories
                </Link>

                <Link :href="route('admin.products.index')" :class="{'bg-blue-50 text-blue-600': route().current('admin.products.*'), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !route().current('admin.products.*')}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Products
                </Link>

                <Link :href="route('admin.customers.index')" :class="{'bg-blue-50 text-blue-600': route().current('admin.customers.*'), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !route().current('admin.customers.*')}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Customers
                </Link>

                    <Link href="#" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Analytics
                </Link>

                <div class="my-2 border-t border-gray-100"></div>

                <Link :href="route('home')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    View Shop
                </Link>
            </nav>

            <div class="mt-auto pb-4">
                    <button class="w-full flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Report
                </button>
            </div>
        </div>
    </aside>
</template>
