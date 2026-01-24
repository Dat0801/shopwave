<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navigation = computed(() => [
    { 
        name: 'My Profile', 
        href: route('profile.edit'), 
        icon: 'UserIcon', 
        current: route().current('profile.edit') 
    },
    { 
        name: 'My Orders', 
        href: route('orders.index'), 
        icon: 'ShoppingBagIcon', 
        current: route().current('orders.index') 
    },
    { 
        name: 'Addresses', 
        href: route('addresses.index'), 
        icon: 'MapPinIcon', 
        current: route().current('addresses.index') 
    },
    { 
        name: 'Payment Methods', 
        href: route('payment-methods.index'), 
        icon: 'CreditCardIcon', 
        current: route().current('payment-methods.index') 
    },
    { 
        name: 'Wishlist', 
        href: route('wishlist.index'), 
        icon: 'HeartIcon', 
        current: route().current('wishlist.index') 
    },
]);
</script>

<template>
    <ShopLayout>
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-gray-500 mb-6">
            <Link :href="route('shop.index')" class="hover:text-gray-900">Home</Link>
            <span class="mx-2">›</span>
            <slot name="breadcrumb">
                <span class="text-gray-900 font-medium">My Profile</span>
            </slot>
        </nav>

        <div class="flex flex-col gap-8 lg:flex-row">
            <!-- Sidebar -->
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <!-- User Info -->
                    <div class="flex flex-col items-center text-center">
                        <div class="relative h-20 w-20 overflow-hidden rounded-full bg-gray-100 ring-2 ring-white">
                            <img 
                                v-if="user.avatar" 
                                :src="`/storage/${user.avatar}`" 
                                alt="User Avatar" 
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-indigo-100 text-indigo-600 font-bold text-2xl">
                                {{ user.name.charAt(0) }}
                            </div>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">{{ user.name }}</h3>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Premium Member</p>
                    </div>

                    <!-- Navigation -->
                    <nav class="mt-8 space-y-1">
                        <Link 
                            v-for="item in navigation" 
                            :key="item.name" 
                            :href="item.href"
                            :class="[
                                item.current ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors'
                            ]"
                        >
                            <span class="mr-3 h-5 w-5 flex-shrink-0" :class="item.current ? 'text-blue-600' : 'text-gray-400'">
                                <svg v-if="item.icon === 'UserIcon'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" /></svg>
                                <svg v-else-if="item.icon === 'ShoppingBagIcon'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" /></svg>
                                <svg v-else-if="item.icon === 'MapPinIcon'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                                <svg v-else-if="item.icon === 'CreditCardIcon'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" /><path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" /></svg>
                                <svg v-else-if="item.icon === 'HeartIcon'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
                                <svg v-else-if="item.icon === 'CogIcon'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.922-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" /></svg>
                            </span>
                            {{ item.name }}
                        </Link>
                    </nav>

                    <div class="mt-8 border-t border-gray-100 pt-6">
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button" 
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0116.5 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm10.72 4.72a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H9a.75.75 0 010-1.5h10.94l-1.72-1.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                            Logout
                        </Link>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </ShopLayout>
</template>
