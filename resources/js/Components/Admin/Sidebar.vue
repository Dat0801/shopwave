<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    showingNavigationDropdown: {
        type: Boolean,
        default: false,
    },
});

const openGroups = ref({});

const toggleGroup = (groupName) => {
    openGroups.value[groupName] = !openGroups.value[groupName];
};

const navigation = [
    {
        name: 'Dashboard',
        href: route('admin.dashboard'),
        icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
        active: () => route().current('admin.dashboard')
    },
    {
        name: 'Product Management',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        children: [
            { name: 'Products', href: route('admin.products.index'), active: () => route().current('admin.products.*') },
            { name: 'Categories', href: route('admin.categories.index'), active: () => route().current('admin.categories.*') },
            { name: 'Reviews', href: route('admin.reviews.index'), active: () => route().current('admin.reviews.*') },
            { name: 'Coupons', href: route('admin.coupons.index'), active: () => route().current('admin.coupons.*') },
        ]
    },
    {
        name: 'Order Management',
        icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        children: [
            { name: 'Orders', href: route('admin.orders.index'), active: () => route().current('admin.orders.*') },
            { name: 'Customers', href: route('admin.customers.index'), active: () => route().current('admin.customers.*') },
        ]
    },
    {
        name: 'Content',
        icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        children: [
             { name: 'Banners', href: route('admin.banners.index'), active: () => route().current('admin.banners.*') },
             { name: 'Blog', href: route('admin.blog.index'), active: () => route().current('admin.blog.*') },
             { name: 'Pages', href: route('admin.pages.index'), active: () => route().current('admin.pages.*') },
             { name: 'Navigation', href: route('admin.navigations.index'), active: () => route().current('admin.navigations.*') },
        ]
    },
    {
        name: 'System',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        children: [
            { name: 'Settings', href: route('admin.settings.index'), active: () => route().current('admin.settings.*') },
        ]
    }
];

// Initialize open groups based on active route
onMounted(() => {
    navigation.forEach(item => {
        if (item.children) {
            const hasActiveChild = item.children.some(child => child.active());
            if (hasActiveChild) {
                openGroups.value[item.name] = true;
            }
        }
    });
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
                <template v-for="item in navigation" :key="item.name">
                    <!-- Single Link -->
                    <Link
                        v-if="!item.children"
                        :href="item.href"
                        :class="{'bg-blue-50 text-blue-600': item.active(), 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': !item.active()}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        {{ item.name }}
                    </Link>

                    <!-- Group -->
                    <div v-else class="space-y-1">
                        <button
                            @click="toggleGroup(item.name)"
                            class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                            :class="{ 'text-gray-900': openGroups[item.name] }"
                        >
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </div>
                            <svg
                                class="h-4 w-4 transition-transform duration-200"
                                :class="{ 'rotate-180': openGroups[item.name] }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            v-show="openGroups[item.name]"
                            class="space-y-1 pl-11 pr-2 transition-all duration-200"
                        >
                            <Link
                                v-for="child in item.children"
                                :key="child.name"
                                :href="child.href"
                                :class="{'text-blue-600 font-medium': child.active(), 'text-gray-500 hover:text-gray-900': !child.active()}"
                                class="block rounded-lg py-2 text-sm transition-colors"
                            >
                                {{ child.name }}
                            </Link>
                        </div>
                    </div>
                </template>

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
