<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    page: Object,
});

const form = useForm({
    title: props.page.title,
    content: props.page.content, // Main story content
    meta: {
        story_headline: props.page.meta?.story_headline || '',
        team: props.page.meta?.team || [],
        values: props.page.meta?.values || [],
        seo_title: props.page.meta?.seo_title || '',
        seo_description: props.page.meta?.seo_description || '',
    },
    _method: 'PUT',
});

// Team Management
const addTeamMember = () => {
    form.meta.team.push({
        name: '',
        role: '',
        image: '',
        image_file: null, // For upload
    });
};

const removeTeamMember = (index) => {
    form.meta.team.splice(index, 1);
};

const handleTeamImageChange = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        form.meta.team[index].image_file = file;
        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            form.meta.team[index].image = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Values Management
const addValue = () => {
    form.meta.values.push({
        title: '',
        description: '',
        icon: 'heart', // Default
    });
};

const removeValue = (index) => {
    form.meta.values.splice(index, 1);
};

const icons = ['heart', 'leaf', 'star', 'shield', 'truck', 'users'];

const submit = () => {
    form.post(route('admin.pages.update', props.page.id), {
        forceFormData: true,
        onSuccess: () => {
            // Optional: reset file inputs or handle success state specifically if needed
        },
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Edit About Us" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Content Management' },
                            { label: 'Pages', href: route('admin.pages.index') },
                            { label: 'Edit About Us' }
                        ]" 
                        class="mb-2"
                    />
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                        Edit About Us Page
                    </h1>
                    <p class="text-sm text-gray-500">Manage your store's story, team, and company values.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="route('about')" target="_blank" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                        View Live Page
                    </a>
                    <button 
                        @click="submit" 
                        :disabled="form.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        Publish Changes
                    </button>
                </div>
            </div>

            <!-- Our Story Section -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Our Story Section</h3>
                    <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded">MODULE</span>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <InputLabel value="Headline" />
                        <TextInput v-model="form.meta.story_headline" class="w-full mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Main Content" />
                        <div class="mt-1 border border-gray-300 rounded-lg overflow-hidden focus-within:ring-1 focus-within:ring-blue-500 focus-within:border-blue-500">
                            <!-- Fake Toolbar -->
                            <div class="bg-gray-50 border-b border-gray-200 px-3 py-2 flex gap-2">
                                <button type="button" class="p-1 hover:bg-gray-200 rounded"><svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h8a4 4 0 100-8H6v8zm0 0h8a4 4 0 110 8H6v-8z"></path></svg></button>
                                <button type="button" class="p-1 hover:bg-gray-200 rounded"><svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg></button>
                                <button type="button" class="p-1 hover:bg-gray-200 rounded"><svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                            </div>
                            <textarea 
                                v-model="form.content" 
                                rows="6" 
                                class="w-full border-0 focus:ring-0 p-4 text-gray-700 resize-y"
                                placeholder="Write your story here..."
                            ></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                    <button type="button" class="text-sm text-gray-600 hover:text-gray-900">Discard</button>
                    <button type="button" @click="submit" class="text-sm font-medium text-white bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                </div>
            </div>

            <!-- Meet the Team -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Meet the Team</h3>
                    <button @click="addTeamMember" type="button" class="text-sm text-blue-600 font-medium hover:text-blue-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Member
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div v-for="(member, index) in form.meta.team" :key="index" class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg bg-gray-50/50">
                        <!-- Image Upload -->
                        <div class="shrink-0 relative group">
                            <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center border border-gray-300">
                                <img v-if="member.image" :src="member.image" class="w-full h-full object-cover" />
                                <svg v-else class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="file" @change="(e) => handleTeamImageChange(e, index)" class="absolute inset-0 opacity-0 cursor-pointer" />
                            <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white text-xs font-medium rounded-lg pointer-events-none">
                                Upload
                            </div>
                        </div>

                        <!-- Fields -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Member Name" />
                                <TextInput v-model="member.name" class="w-full mt-1" placeholder="e.g. Sarah Jenkins" />
                            </div>
                            <div>
                                <InputLabel value="Role" />
                                <TextInput v-model="member.role" class="w-full mt-1" placeholder="e.g. CEO & Founder" />
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-3">
                                <button type="button" @click="removeTeamMember(index)" class="text-xs text-red-600 hover:text-red-800 font-medium">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.meta.team.length === 0" class="text-center py-8 text-gray-500 text-sm">
                        No team members added yet.
                    </div>
                </div>
            </div>

            <!-- Company Values -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Company Values</h3>
                    <button @click="addValue" type="button" class="text-sm text-blue-600 font-medium hover:text-blue-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Value
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div v-for="(val, index) in form.meta.values" :key="index" class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg">
                        <!-- Icon Selector -->
                        <div class="shrink-0">
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                <!-- Dynamic Icon Render (Simple map) -->
                                <svg v-if="val.icon === 'heart'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                <svg v-else-if="val.icon === 'leaf'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="mt-2 grid grid-cols-3 gap-1">
                                <button 
                                    v-for="icon in icons" 
                                    :key="icon" 
                                    @click="val.icon = icon"
                                    type="button"
                                    class="w-4 h-4 rounded-full border border-gray-300 hover:border-blue-500"
                                    :class="{ 'bg-blue-500 border-blue-500': val.icon === icon }"
                                ></button>
                            </div>
                        </div>

                        <!-- Fields -->
                        <div class="flex-1 space-y-3">
                            <div>
                                <InputLabel value="Title" />
                                <TextInput v-model="val.title" class="w-full mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Description" />
                                <TextInput v-model="val.description" class="w-full mt-1" />
                            </div>
                             <div class="flex items-center gap-3">
                                <button type="button" @click="removeValue(index)" class="text-xs text-red-600 hover:text-red-800 font-medium">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.meta.values.length === 0" class="text-center py-8 text-gray-500 text-sm">
                        No values added yet.
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Search Engine Optimization</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <InputLabel value="Page Meta Title" />
                        <TextInput v-model="form.meta.seo_title" class="w-full mt-1" />
                        <p class="mt-1 text-xs text-gray-500">Recommended: 60 characters or less.</p>
                    </div>
                    <div>
                        <InputLabel value="Meta Description" />
                        <textarea v-model="form.meta.seo_description" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Recommended: 160 characters or less.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pb-8">
                 <button 
                    @click="submit" 
                    :disabled="form.processing"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm disabled:opacity-50"
                >
                    Save All Changes
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
