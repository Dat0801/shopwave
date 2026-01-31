<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const formatDate = (dateString) => {
    if (!dateString) return 'Not set';
    return new Date(dateString).toLocaleString();
};

const getStatusColor = (status) => {
    switch (status) {
        case 'published': return 'bg-green-100 text-green-800 border-green-200';
        case 'scheduled': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'draft': return 'bg-gray-100 text-gray-800 border-gray-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - View Blog Post" />

        <template #header>
            <div class="mb-6">
                <Breadcrumb 
                    :items="[
                        { label: 'Blog', href: '#' },
                        { label: 'Posts', href: route('admin.blog.index') },
                        { label: 'View Post' }
                    ]" 
                    class="mb-2"
                />
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                            {{ post.title }}
                        </h1>
                        <p class="mt-2 text-base text-gray-600">
                            Blog post details and preview.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link 
                            :href="route('admin.blog.index')"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 transition-all"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </Link>
                        <Link 
                            :href="route('admin.blog.edit', post.id)"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Post
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Content -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Post Content</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Excerpt -->
                            <div v-if="post.excerpt">
                                <h3 class="text-sm font-medium text-gray-500">Excerpt</h3>
                                <p class="mt-1 text-base text-gray-900">{{ post.excerpt }}</p>
                            </div>

                            <!-- Body -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Content</h3>
                                <div class="prose max-w-none text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-100" v-html="post.content"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO & Meta -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">SEO Configuration</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Meta Title</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ post.meta_title || '-' }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Slug</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ post.slug }}</p>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Meta Description</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ post.meta_description || '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Status & Info -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Information</h2>
                        
                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                                <span 
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border capitalize"
                                    :class="getStatusColor(post.status)"
                                >
                                    {{ post.status }}
                                </span>
                            </div>

                            <!-- Category -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Category</h3>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-indigo-100 text-indigo-600">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </span>
                                    <span class="text-sm text-gray-900">{{ post.blog_category?.name || 'Uncategorized' }}</span>
                                </div>
                            </div>

                            <!-- Author -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Author</h3>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                                        {{ post.author?.name?.charAt(0) || '?' }}
                                    </div>
                                    <span class="text-sm text-gray-900">{{ post.author?.name || 'Unknown' }}</span>
                                </div>
                            </div>

                            <!-- Published At -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Published Date</h3>
                                <div class="mt-1 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm text-gray-900">{{ formatDate(post.published_at) }}</span>
                                </div>
                            </div>

                            <!-- Created At -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(post.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Featured Image</h2>
                        <div class="rounded-lg border border-gray-200 overflow-hidden bg-gray-50 aspect-video flex items-center justify-center relative group">
                            <img 
                                v-if="post.image" 
                                :src="post.image" 
                                :alt="post.title"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="text-center p-4">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-gray-500">No image set</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" v-if="post.tags && post.tags.length">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tags</h2>
                        <div class="flex flex-wrap gap-2">
                            <span 
                                v-for="tag in post.tags" 
                                :key="tag"
                                class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                            >
                                {{ tag }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>
