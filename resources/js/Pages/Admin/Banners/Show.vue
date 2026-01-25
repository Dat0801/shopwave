<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    banner: {
        type: Object,
        required: true,
    },
});

const formatDate = (dateString) => {
    if (!dateString) return 'Not set';
    return new Date(dateString).toLocaleString();
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - View Banner" />

        <template #header>
            <div class="mb-6">
                <Breadcrumb 
                    :items="[
                        { label: 'Marketing', href: '#' },
                        { label: 'Banners', href: route('admin.banners.index') },
                        { label: 'View Banner' }
                    ]" 
                    class="mb-2"
                />
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                            {{ banner.title || 'Untitled Banner' }}
                        </h1>
                        <p class="mt-2 text-base text-gray-600">
                            Banner details and preview.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link 
                            :href="route('admin.banners.index')"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 transition-all"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </Link>
                        <Link 
                            :href="route('admin.banners.edit', banner.id)"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Banner
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Banner Content Details -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Banner Content</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Title</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ banner.title || '-' }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Subtitle / Description</h3>
                                <p class="mt-1 text-base text-gray-900 whitespace-pre-line">{{ banner.description || '-' }}</p>
                            </div>

                            <!-- CTA & Link -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">CTA Button Text</h3>
                                    <p class="mt-1 text-base text-gray-900">{{ banner.button_text || '-' }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Target URL</h3>
                                    <p class="mt-1 text-base text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded inline-block">{{ banner.link || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Banner Media</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Desktop -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Desktop Version</h3>
                                <div class="w-full rounded-lg border border-gray-200 overflow-hidden bg-gray-50">
                                    <img 
                                        v-if="banner.image_path" 
                                        :src="getImageUrl(banner.image_path)" 
                                        class="w-full h-auto object-cover" 
                                        alt="Desktop Banner" 
                                    />
                                    <div v-else class="h-32 flex items-center justify-center text-gray-400">
                                        No image uploaded
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Mobile Version</h3>
                                <div class="w-full max-w-sm rounded-lg border border-gray-200 overflow-hidden bg-gray-50">
                                    <img 
                                        v-if="banner.mobile_image_path" 
                                        :src="getImageUrl(banner.mobile_image_path)" 
                                        class="w-full h-auto object-cover" 
                                        alt="Mobile Banner" 
                                    />
                                    <div v-else class="h-32 flex items-center justify-center text-gray-400">
                                        No mobile image uploaded
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Status & Placement -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Status & Settings</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                <div class="mt-1">
                                    <span 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="banner.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ banner.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Placement</h3>
                                <p class="mt-1 text-base text-gray-900">{{ banner.placement || '-' }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Slide Duration</h3>
                                <p class="mt-1 text-base text-gray-900">{{ banner.duration }} ms</p>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Scheduling</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Start Date</h3>
                                <p class="mt-1 text-base text-gray-900">{{ formatDate(banner.start_date) }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">End Date</h3>
                                <p class="mt-1 text-base text-gray-900">{{ formatDate(banner.end_date) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>