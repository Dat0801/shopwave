<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue';

const props = defineProps({
    post: Object,
    categories: Array,
});

const form = useForm({
    title: props.post.title || '',
    excerpt: props.post.excerpt || '',
    content: props.post.content || '',
    image: props.post.image || null,
    blog_category_id: props.post.blog_category_id || '',
    status: props.post.status || 'draft',
    published_at: props.post.published_at ? props.post.published_at.slice(0, 16) : '', // Format for datetime-local
    meta_title: props.post.meta_title || '',
    meta_description: props.post.meta_description || '',
    tags: props.post.tags || [],
});

const imagePreview = ref(props.post.image);
const newTag = ref('');
const isSeoOpen = ref(false);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const addTag = () => {
    const tag = newTag.value.trim();
    if (tag && !form.tags.includes(tag)) {
        form.tags.push(tag);
        newTag.value = '';
    }
};

const removeTag = (index) => {
    form.tags.splice(index, 1);
};

const submit = () => {
    // If image is a string (existing URL), don't send it to backend unless we want to keep it, 
    // but Inertia handles file uploads differently. 
    // If we are using FormData (which Inertia does automatically for files), we might need to handle this.
    // However, the backend logic: if (request->hasFile('image')) checks for file.
    // If form.image is a string, it won't be treated as a file.
    
    // NOTE: In Vue Inertia, if we put() with a file, we might need to use post() with _method: 'put'.
    // But let's stick to simple first. If form.image is string, we can send it or null.
    // Actually, usually we use a separate key for new image or just check type.
    
    form.post(route('admin.blog.update', props.post.id), {
        _method: 'put',
    });
};
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
                                <RichTextEditor 
                                    v-model="form.content" 
                                    placeholder="Write something amazing..."
                                    class="mt-1"
                                />
                                <InputError class="mt-2" :message="form.errors.content" />
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <button 
                            type="button" 
                            class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors text-left"
                            @click="isSeoOpen = !isSeoOpen"
                        >
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span class="font-bold text-gray-900">SEO Settings</span>
                            </div>
                            <span class="text-sm text-blue-600 font-medium" v-if="!isSeoOpen">Preview Result</span>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="{ 'rotate-180': isSeoOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div v-show="isSeoOpen" class="p-6 border-t border-gray-100 space-y-4">
                             <div>
                                <InputLabel for="meta_title" value="Meta Title" />
                                <TextInput
                                    id="meta_title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.meta_title"
                                />
                                <p class="mt-1 text-xs text-gray-500">Recommended length: 50-60 characters</p>
                            </div>
                            <div>
                                <InputLabel for="meta_description" value="Meta Description" />
                                <textarea
                                    id="meta_description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    rows="3"
                                    v-model="form.meta_description"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">Recommended length: 150-160 characters</p>
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
                                <div class="flex items-center justify-between">
                                    <InputLabel for="blog_category_id" value="Category" />
                                    <Link :href="route('admin.blog-categories.create')" class="text-xs font-medium text-blue-600 hover:text-blue-500">
                                        + Add New
                                    </Link>
                                </div>
                                <select
                                    id="blog_category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.blog_category_id"
                                >
                                    <option value="" disabled>Select Category</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.blog_category_id" />
                            </div>

                            <div>
                                <InputLabel for="tags" value="Tags" />
                                <div class="flex flex-wrap gap-2 mb-2 mt-2">
                                    <span v-for="(tag, index) in form.tags" :key="index" class="inline-flex items-center px-2.5 py-0.5 rounded text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ tag }}
                                        <button type="button" @click="removeTag(index)" class="ml-1.5 inline-flex items-center justify-center text-blue-400 hover:text-blue-600 focus:outline-none">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </span>
                                </div>
                                <TextInput
                                    id="tags"
                                    type="text"
                                    class="block w-full"
                                    placeholder="Add a tag..."
                                    v-model="newTag"
                                    @keydown.enter.prevent="addTag"
                                />
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
