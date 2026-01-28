<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const isLoggedIn = computed(() => !!page.props.auth.user);
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');
const cartCount = computed(() => (page.props.cart ? page.props.cart.count : 0));

const search = ref(page.props.filters?.search || '');
const isMobileMenuOpen = ref(false);

const handleSearch = () => {
    router.get(route('shop.index'), {
        search: search.value,
        category: page.props.filters?.category,
    }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <header class="sticky top-0 z-50 w-full bg-white shadow-sm border-b border-gray-100">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <Link :href="route('home')" class="flex items-center gap-2">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-600 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </div>
                <span class="font-bold text-2xl tracking-tight text-gray-900">ShopWave</span>
            </Link>

            <!-- Navigation & Search -->
            <div class="hidden flex-1 items-center justify-center gap-6 md:flex px-4">
                <nav class="flex items-center gap-5 text-sm font-semibold text-gray-900 whitespace-nowrap">
                    <Link v-if="isAdmin" :href="route('admin.dashboard')" class="text-indigo-600 hover:text-indigo-700 transition-colors">Admin</Link>
                    
                    <template v-if="page.props.navigation?.header?.length">
                        <div v-for="item in page.props.navigation.header" :key="item.id" class="relative group">
                            <!-- Dropdown Trigger or Link -->
                            <div v-if="item.children && item.children.length > 0">
                                <button class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                    {{ item.name }}
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div class="absolute top-full left-0 pt-4 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform group-hover:translate-y-0 translate-y-2">
                                    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-2">
                                        <Link 
                                            v-for="child in item.children" 
                                            :key="child.id" 
                                            :href="child.href" 
                                            class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                                        >
                                            {{ child.name }}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <Link v-else :href="item.href" class="hover:text-blue-600 transition-colors">
                                {{ item.name }}
                            </Link>
                        </div>
                    </template>

                    <!-- Fallback if no navigation items -->
                    <template v-else>
                         <Link :href="route('shop.index')" class="hover:text-blue-600 transition-colors">Shop</Link>
                         <Link :href="route('blog.index')" class="hover:text-blue-600 transition-colors">Blog</Link>
                         <Link :href="route('contact.index')" class="hover:text-blue-600 transition-colors">Contact Us</Link>
                    </template>
                </nav>
                <div class="relative w-full max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Search for products..."
                        class="w-full rounded-full border-0 bg-gray-100 py-2.5 pl-11 pr-4 text-sm text-gray-900 placeholder:text-gray-500 focus:bg-white focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6"
                        v-model="search"
                        @keydown.enter="handleSearch"
                    />
                </div>
            </div>

            <!-- Right Icons -->
            <div class="flex items-center gap-6">
                    <!-- Wishlist -->
                <Link :href="route('wishlist.index')" class="text-gray-900 hover:text-blue-600 transition-colors">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </Link>

                <!-- User -->
                <Link v-if="isLoggedIn" :href="route('profile.edit')" class="text-gray-900 hover:text-blue-600 transition-colors">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </Link>
                <Link v-else :href="route('login')" class="text-gray-900 hover:text-blue-600 transition-colors">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </Link>

                <!-- Admin Dashboard -->
                <Link v-if="isAdmin" :href="route('admin.dashboard')" class="text-gray-900 hover:text-blue-600 transition-colors" title="Admin Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                </Link>

                <!-- Cart -->
                <Link :href="route('cart.index')" class="group relative flex items-center">
                        <div class="relative">
                            <svg class="h-6 w-6 text-gray-900 group-hover:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span v-if="cartCount" class="absolute -top-1 -right-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white ring-2 ring-white">
                            {{ cartCount }}
                        </span>
                    </div>
                </Link>

                <!-- Mobile Menu Button -->
                <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="md:hidden text-gray-900 hover:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="isMobileMenuOpen" class="fixed inset-0 z-50 bg-white md:hidden overflow-y-auto">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between px-4 h-20 border-b border-gray-100">
                    <span class="font-bold text-2xl tracking-tight text-gray-900">Menu</span>
                    <button @click="isMobileMenuOpen = false" class="text-gray-900 hover:text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 py-6 px-4 space-y-6">
                    <!-- Search Mobile -->
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Search..."
                            class="w-full rounded-full border-0 bg-gray-100 py-3 pl-11 pr-4 text-base text-gray-900 placeholder:text-gray-500 focus:bg-white focus:ring-2 focus:ring-blue-600"
                            v-model="search"
                            @keydown.enter="handleSearch"
                        />
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex flex-col gap-4">
                        <template v-if="page.props.navigation?.mobile?.length">
                            <div v-for="item in page.props.navigation.mobile" :key="item.id">
                                <div v-if="item.children && item.children.length > 0">
                                    <div class="font-bold text-lg text-gray-900 mb-2">{{ item.name }}</div>
                                    <div class="pl-4 flex flex-col gap-3 border-l-2 border-gray-100">
                                        <Link v-for="child in item.children" :key="child.id" :href="child.href" class="text-base text-gray-600 hover:text-blue-600 block">
                                            {{ child.name }}
                                        </Link>
                                    </div>
                                </div>
                                <Link v-else :href="item.href" class="font-bold text-lg text-gray-900 hover:text-blue-600 block">
                                    {{ item.name }}
                                </Link>
                            </div>
                        </template>
                        <template v-else>
                            <Link :href="route('shop.index')" class="font-bold text-lg text-gray-900 hover:text-blue-600">Shop</Link>
                            <Link :href="route('blog.index')" class="font-bold text-lg text-gray-900 hover:text-blue-600">Blog</Link>
                            <Link :href="route('contact.index')" class="font-bold text-lg text-gray-900 hover:text-blue-600">Contact Us</Link>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </header>
</template>
