<script setup>
import { ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import AdminSidebar from '@/Components/Admin/Sidebar.vue';
import NotificationBell from '@/Components/Admin/NotificationBell.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Flash Message -->
        <FlashMessage />

        <!-- Sidebar -->
        <AdminSidebar :showing-navigation-dropdown="showingNavigationDropdown" />

        <!-- Main Content Wrapper -->
        <div class="md:pl-64 flex flex-col min-h-screen">
            <!-- Top Header -->
            <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6 lg:px-8">
                <div class="flex flex-1 items-center gap-4">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="relative w-full max-w-md hidden sm:block">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" class="block w-full rounded-full border-0 bg-gray-100 py-2 pl-10 pr-3 text-sm text-gray-900 focus:ring-2 focus:ring-blue-500 placeholder:text-gray-400 sm:leading-6" placeholder="Search orders, users..." />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                     <NotificationBell />

                    <Link :href="route('admin.settings.index')" class="rounded-full p-1 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Settings</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </Link>

                    <div class="h-8 w-px bg-gray-200"></div>

                    <!-- Profile Dropdown -->
                    <div class="relative ml-3">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center gap-3 rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <div class="flex flex-col items-end hidden sm:block">
                                        <span class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user.name }}</span>
                                        <span class="text-xs text-gray-500">Store Manager</span>
                                    </div>
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
                                         {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 py-8">
                 <div class="px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                    <div class="mt-8">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
