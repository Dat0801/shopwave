<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    title: '',
    excerpt: '',
    content: '',
    image: null,
    category: '',
    status: 'draft',
    published_at: '',
    meta_title: '',
    meta_description: '',
    tags: [],
});

const imagePreview = ref(null);
const newTag = ref('');
const isSeoOpen = ref(false);

// Mock word count
const wordCount = computed(() => {
    if (!form.content) return 0;
    return form.content.trim().split(/\s+/).length;
});

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

const submit = (status = null) => {
    if (status) {
        form.status = status;
    }
    form.post(route('admin.blog.store'));
};

const categories = ['Style Guide', 'Product News', 'UX Design', 'Sustainable Fashion', 'Trends', 'Lifestyle'];
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Create Blog Post" />

        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Dashboard', href: route('admin.dashboard') },
                            { label: 'Blog', href: route('admin.blog.index') },
                            { label: 'Create Post' }
                        ]" 
                        class="mb-2"
                    />
                    <div class="flex items-center gap-4">
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                            Create New Blog Post
                        </h1>
                        <span class="text-xs text-gray-500 italic hidden sm:inline-block">Last saved 2 mins ago</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Preview
                    </button>
                    <button 
                        @click="submit('published')" 
                        :disabled="form.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        Publish Post
                    </button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
            
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Title & Content -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
                    <!-- Title Input -->
                    <div>
                        <InputLabel for="title" value="POST TITLE" class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2" />
                        <input
                            id="title"
                            type="text"
                            class="block w-full border-0 p-0 text-3xl font-bold text-gray-900 placeholder-gray-300 focus:ring-0"
                            placeholder="Enter post title..."
                            v-model="form.title"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Rich Text Editor Mock -->
                    <div class="border-t border-gray-100 pt-6">
                        <!-- Toolbar -->
                        <div class="flex items-center gap-1 mb-4 border-b border-gray-100 pb-2 overflow-x-auto">
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Bold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6V4zm0 8h9a4 4 0 014 4 4 4 0 01-4 4H6v-8z"></path></svg></button>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Italic"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4M6 16l-4-4m0 0l4-4m-4 4h14"></path></svg></button>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg></button>
                            <div class="w-px h-4 bg-gray-200 mx-2"></div>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Link"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg></button>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Image"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></button>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Video"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
                            <div class="w-px h-4 bg-gray-200 mx-2"></div>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="List"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Ordered List"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h12M7 12h12M7 17h12M3 7h.01M3 12h.01M3 17h.01"></path></svg></button>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Quote"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg></button>
                            <div class="flex-1"></div>
                            <button type="button" class="p-2 text-gray-500 hover:bg-gray-100 rounded" title="Code"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4M6 16l-4-4m0 0l4-4m-4 4h14"></path></svg></button>
                        </div>

                        <!-- Textarea Content -->
                        <textarea
                            id="content"
                            class="block w-full border-0 p-0 text-gray-600 placeholder-gray-300 focus:ring-0 min-h-[400px] resize-none text-lg leading-relaxed"
                            placeholder="Start writing your story..."
                            v-model="form.content"
                            required
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.content" />

                        <!-- Editor Footer -->
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 text-xs font-medium text-gray-400 uppercase tracking-wide">
                            <span>Draft Saved</span>
                            <span>{{ wordCount }} Words</span>
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

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                
                <!-- Publish Details -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Publish Details</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Visibility
                            </span>
                            <span class="font-medium text-blue-600 cursor-pointer">Public</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Schedule
                            </span>
                            <span class="font-medium text-gray-900">Now</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Status
                            </span>
                            <span class="px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded uppercase">Draft</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button 
                            type="button" 
                            @click="submit('draft')"
                            :disabled="form.processing"
                            class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                            Save as Draft
                        </button>
                        <button 
                            type="button" 
                            @click="submit('published')"
                            :disabled="form.processing"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Publish Now
                        </button>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Featured Image</h3>
                    
                    <div class="mt-2">
                        <div v-if="imagePreview" class="relative w-full aspect-video rounded-lg overflow-hidden border border-gray-200 bg-gray-50 mb-4">
                            <img :src="imagePreview" class="w-full h-full object-cover" />
                            <button 
                                type="button" 
                                @click="imagePreview = null; form.image = null" 
                                class="absolute top-2 right-2 bg-white rounded-full p-1.5 shadow-sm hover:bg-gray-100 transition-colors"
                            >
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors" :class="{'hidden': imagePreview}">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="mb-1 text-sm text-gray-500">Drag and drop or <span class="text-blue-600 hover:underline font-medium">browse</span> to upload</p>
                                <p class="text-xs text-gray-400">Recommended size: 1200x630px</p>
                            </div>
                            <input id="dropzone-file" type="file" class="hidden" @change="handleImageChange" accept="image/*" />
                        </label>
                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>
                </div>

                <!-- Categories & Tags -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Categories & Tags</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="category" value="CATEGORY" class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2" />
                            <select
                                id="category"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                v-model="form.category"
                            >
                                <option value="" disabled>Select Category</option>
                                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.category" />
                        </div>

                        <div>
                            <InputLabel for="tags" value="TAGS" class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2" />
                            <div class="flex flex-wrap gap-2 mb-2">
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

            </div>
        </div>
    </AdminLayout>
</template>
