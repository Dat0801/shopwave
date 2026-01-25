<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    banners: Object,
});

const deleteBanner = (banner) => {
    if (confirm('Are you sure you want to delete this banner?')) {
        router.delete(route('admin.banners.destroy', banner.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Banners" />

        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Admin', href: route('admin.dashboard') },
                            { label: 'Banners' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">
                        Banners
                    </h1>
                </div>
                <Link :href="route('admin.banners.create')" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    Create Banner
                </Link>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-medium">Image</th>
                                <th scope="col" class="px-6 py-4 font-medium">Title</th>
                                <th scope="col" class="px-6 py-4 font-medium">Link</th>
                                <th scope="col" class="px-6 py-4 font-medium">Duration</th>
                                <th scope="col" class="px-6 py-4 font-medium">Order</th>
                                <th scope="col" class="px-6 py-4 font-medium">Status</th>
                                <th scope="col" class="px-6 py-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                            <tr v-for="banner in banners.data" :key="banner.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="h-12 w-20 overflow-hidden rounded-lg bg-gray-100">
                                        <img :src="getImageUrl(banner.image_path, 200, 120)" :alt="banner.title" class="h-full w-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ banner.title || 'No Title' }}
                                </td>
                                <td class="px-6 py-4">
                                    <a v-if="banner.link" :href="banner.link" target="_blank" class="text-blue-600 hover:underline truncate max-w-xs block">
                                        {{ banner.link }}
                                    </a>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ banner.duration || 5000 }}ms
                                </td>
                                <td class="px-6 py-4">
                                    {{ banner.order }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-medium"
                                        :class="banner.is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700'">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="banner.is_active ? 'bg-green-600' : 'bg-gray-500'"></span>
                                        {{ banner.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('admin.banners.edit', banner.id)" class="rounded-lg p-2 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button @click="deleteBanner(banner)" class="rounded-lg p-2 text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="banners.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No banners found. Click "Add Banner" to create one.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
