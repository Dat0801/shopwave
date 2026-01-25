<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image';
import { ref } from 'vue';

const props = defineProps({
    banners: Object,
});

const activeTab = ref('All Banners');
const tabs = ['All Banners', 'Homepage Hero', 'Sidebar', 'Checkout', 'Scheduled'];

const toggleStatus = (banner) => {
    router.put(route('admin.banners.update', banner.id), {
        ...banner,
        is_active: !banner.is_active,
    }, {
        preserveScroll: true,
    });
};

const deleteBanner = (banner) => {
    if (confirm('Are you sure you want to delete this banner?')) {
        router.delete(route('admin.banners.destroy', banner.id));
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Banner Management" />

        <div class="space-y-6">
            <!-- Breadcrumb -->
            <Breadcrumb 
                :items="[
                    { label: 'Admin', href: route('admin.dashboard') },
                    { label: 'Banners' }
                ]" 
            />

            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 font-serif">
                        Banner Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Control your storefront visuals and scheduled marketing campaigns.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.banners.create')" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Banner
                    </Link>
                </div>
            </div>

            <!-- Tabs & Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-1">
                <nav class="flex gap-6 overflow-x-auto pb-2 sm:pb-0" aria-label="Tabs">
                    <button
                        v-for="tab in tabs"
                        :key="tab"
                        @click="activeTab = tab"
                        :class="[
                            activeTab === tab
                                ? 'border-blue-600 text-blue-900 font-bold'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium',
                            'whitespace-nowrap border-b-2 py-2 text-sm transition-colors'
                        ]"
                    >
                        {{ tab }}
                    </button>
                </nav>
                <div class="flex items-center gap-2">
                    <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        Recently Added
                    </button>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Banner Cards -->
                <div v-for="banner in banners.data" :key="banner.id" class="group relative flex flex-col overflow-hidden rounded-xl bg-white border border-gray-200 shadow-sm transition-all hover:shadow-md">
                    <!-- Image Area -->
                    <div class="relative aspect-[2/1] w-full overflow-hidden bg-gray-100">
                        <Link :href="route('admin.banners.show', banner.id)" class="absolute inset-0 z-10">
                            <img :src="getImageUrl(banner.image_path, 600, 300)" :alt="banner.title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </Link>
                        <!-- Badge -->
                        <div class="absolute right-3 top-3">
                            <span
                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-bold uppercase tracking-wider shadow-sm"
                                :class="banner.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                            >
                                {{ banner.is_active ? 'Active' : 'Expired' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 font-serif line-clamp-1" :title="banner.title">
                                <Link :href="route('admin.banners.show', banner.id)" class="hover:text-blue-600 transition-colors">
                                    {{ banner.title || 'Untitled Banner' }}
                                </Link>
                            </h3>
                            
                            <!-- Toggle -->
                            <button 
                                @click="toggleStatus(banner)"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="banner.is_active ? 'bg-blue-600' : 'bg-gray-200'"
                            >
                                <span class="sr-only">Use setting</span>
                                <span 
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="banner.is_active ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>

                        <div class="space-y-3 mt-auto">
                            <!-- Link -->
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </div>
                                <span class="truncate">{{ banner.link || 'No link' }}</span>
                            </div>

                            <!-- Location (Mocked) -->
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-purple-50 text-purple-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span>{{ banner.placement || 'Homepage Hero' }}</span>
                            </div>

                            <!-- Duration -->
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-yellow-50 text-yellow-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>{{ banner.duration ? (banner.duration / 1000) + 's' : '5s' }}</span>
                            </div>
                        </div>
                        
                        <!-- Actions overlay or hover actions could be added here, but sticking to design -->
                        <div class="mt-4 flex gap-2 border-t border-gray-100 pt-4">
                            <Link :href="route('admin.banners.edit', banner.id)" class="text-xs font-medium text-blue-600 hover:text-blue-800">Edit Details</Link>
                            <button @click="deleteBanner(banner)" class="text-xs font-medium text-red-600 hover:text-red-800 ml-auto">Delete</button>
                        </div>
                    </div>
                </div>

                <!-- Create New Banner Card -->
                <Link :href="route('admin.banners.create')" class="group flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center transition-all hover:border-blue-300 hover:bg-blue-50">
                    <div class="mb-4 rounded-full bg-white p-4 shadow-sm group-hover:scale-110 transition-transform">
                        <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 font-serif">Create New Banner</h3>
                </Link>

            </div>

            <!-- Load More -->
            <div class="flex justify-center pt-6">
                <button class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    Load More Banners
                </button>
            </div>

        </div>
    </AdminLayout>
</template>
