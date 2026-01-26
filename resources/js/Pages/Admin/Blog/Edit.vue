<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    post: Object,
});

const form = useForm({
    title: props.post.title,
    excerpt: props.post.excerpt,
    content: props.post.content,
    image: props.post.image,
    category: props.post.category,
    status: props.post.status,
    published_at: props.post.published_at ? props.post.published_at.slice(0, 16) : '', // Format for datetime-local
});

const submit = () => {
    form.put(route('admin.blog.update', props.post.id));
};

const categories = ['Style Guide', 'Product News', 'UX Design', 'Sustainable Fashion', 'Trends', 'Lifestyle'];
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Edit Blog Post" />

        <template #header>
            <div class="mb-6">
                <Breadcrumb 
                    :items="[
                        { label: 'Dashboard', href: route('admin.dashboard') },
                        { label: 'Blog Management', href: route('admin.blog.index') },
                        { label: 'Edit Post' }
                    ]" 
                    class="mb-2"
                />
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                    Edit Post
                </h1>
                <p class="mt-2 text-base text-gray-600">
                    Update content and settings for this post.
                </p>
            </div>
        </template>

        <form @submit.prevent="submit" class="pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Post Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <InputLabel for="excerpt" value="Excerpt" />
                                <textarea
                                    id="excerpt"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.excerpt"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.excerpt" />
                            </div>

                            <!-- Content -->
                            <div>
                                <InputLabel for="content" value="Content" />
                                <textarea
                                    id="content"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                                    v-model="form.content"
                                    rows="15"
                                    required
                                ></textarea>
                                <p class="mt-1 text-sm text-gray-500">HTML is supported.</p>
                                <InputError class="mt-2" :message="form.errors.content" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Publishing -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Publishing</h3>
                        <div class="space-y-4">
                            <div>
                                <InputLabel for="status" value="Status" />
                                <select
                                    id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.status"
                                >
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="scheduled">Scheduled</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div>
                                <InputLabel for="published_at" value="Publish Date" />
                                <TextInput
                                    id="published_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    v-model="form.published_at"
                                />
                                <InputError class="mt-2" :message="form.errors.published_at" />
                            </div>
                        </div>
                    </div>

                    <!-- Organization -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Organization</h3>
                        <div class="space-y-4">
                            <div>
                                <InputLabel for="category" value="Category" />
                                <select
                                    id="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.category"
                                >
                                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category" />
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Featured Image</h3>
                        <div>
                            <InputLabel for="image" value="Image Upload" />
                            
                            <div class="mt-2 flex flex-col gap-4">
                                <div v-if="imagePreview" class="relative w-full aspect-video rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                                    <img :src="imagePreview" class="w-full h-full object-cover" />
                                    <button 
                                        type="button" 
                                        @click="imagePreview = null; form.image = null" 
                                        class="absolute top-2 right-2 bg-white rounded-full p-1.5 shadow-sm hover:bg-gray-100 transition-colors"
                                    >
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-center w-full">
                                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 2MB)</p>
                                        </div>
                                        <input id="dropzone-file" type="file" class="hidden" @change="handleImageChange" accept="image/*" />
                                    </label>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.image" />
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sticky Footer Actions -->
            <div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 p-4 sm:px-6 lg:px-8 shadow-lg lg:left-64 transition-all duration-300">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <Link :href="route('admin.blog.index')" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                        Cancel
                    </Link>
                    <div class="flex items-center gap-4">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
