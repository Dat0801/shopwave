<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import draggable from 'vuedraggable';

const props = defineProps({
    banners: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const activeTab = ref(props.filters?.placement || 'All Banners');
const tabs = ['All Banners', 'Homepage Hero (Top)', 'Sidebar', 'Footer'];

const bannersList = ref(props.banners.data);

// Sync bannersList when props change (e.g. after filter)
watch(() => props.banners.data, (newBanners) => {
    bannersList.value = newBanners;
});

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

const onDragEnd = () => {
    const bannersToUpdate = bannersList.value.map((banner, index) => ({
        id: banner.id,
        order: index + 1,
    }));

    router.post(route('admin.banners.reorder'), {
        banners: bannersToUpdate
    }, {
        preserveScroll: true,
    });
};

watch(activeTab, (value) => {
    router.get(route('admin.banners.index'), {
        placement: value === 'All Banners' ? null : value,
        search: search.value,
    }, {
        preserveState: true,
        replace: true,
    });
});

watch(search, debounce((value) => {
    router.get(route('admin.banners.index'), {
        placement: activeTab.value === 'All Banners' ? null : activeTab.value,
        search: value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));
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
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            class="block w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Search banners..."
                        />
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <draggable 
                v-model="bannersList" 
                item-key="id"
                tag="div"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                @end="onDragEnd"
                handle=".drag-handle"
            >
                <template #item="{ element: banner }">
                    <div class="group relative flex flex-col overflow-hidden rounded-xl bg-white border border-gray-200 shadow-sm transition-all hover:shadow-md">
                        <!-- Image Area -->
                        <div class="relative aspect-[2/1] w-full overflow-hidden bg-gray-100">
                            <!-- Drag Handle -->
                            <div class="absolute left-3 top-3 z-20 cursor-move drag-handle opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 rounded p-1 text-white">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </div>
                            
                            <Link :href="route('admin.banners.show', banner.id)" class="absolute inset-0 z-10">
                                <img :src="getImageUrl(banner.image_path, 600, 300)" :alt="banner.title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </Link>
                            <!-- Badge -->
                            <div class="absolute right-3 top-3 z-20">
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

                                <!-- Location -->
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
                            
                            <div class="mt-4 flex gap-2 border-t border-gray-100 pt-4">
                                <Link :href="route('admin.banners.edit', banner.id)" class="text-xs font-medium text-blue-600 hover:text-blue-800">Edit Details</Link>
                                <button @click="deleteBanner(banner)" class="text-xs font-medium text-red-600 hover:text-red-800 ml-auto">Delete</button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <template #footer>
                     <!-- Create New Banner Card -->
                    <Link :href="route('admin.banners.create')" class="group flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center transition-all hover:border-blue-300 hover:bg-blue-50 min-h-[400px]">
                        <div class="mb-4 rounded-full bg-white p-4 shadow-sm group-hover:scale-110 transition-transform">
                            <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 font-serif">Create New Banner</h3>
                    </Link>
                </template>
            </draggable>

            <!-- Pagination (if needed) -->
             <div v-if="banners.links && banners.links.length > 3" class="flex justify-center pt-6">
                <div class="flex gap-1">
                    <Link
                        v-for="(link, i) in banners.links"
                        :key="i"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 rounded border"
                        :class="[
                            link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                    />
                </div>
            </div>

        </div>
    </AdminLayout>
</template>