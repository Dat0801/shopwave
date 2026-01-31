<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    page: Object,
    contactSettings: Object,
});

const form = useForm({
    title: props.page.title,
    content: props.page.content,
    contact_email: props.contactSettings?.contact_email || '',
    contact_phone: props.contactSettings?.contact_phone || '',
    address: props.contactSettings?.address || '',
    meta: {
        header_title: props.page.meta?.header_title || '',
        header_description: props.page.meta?.header_description || '',
        subjects: props.page.meta?.subjects || [],
        seo_title: props.page.meta?.seo_title || '',
        seo_description: props.page.meta?.seo_description || '',
    },
    _method: 'PUT',
});

// Subject Management
const addSubject = () => {
    if (!form.meta.subjects) {
        form.meta.subjects = [];
    }
    form.meta.subjects.push('');
};

const removeSubject = (index) => {
    form.meta.subjects.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.pages.update', props.page.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Edit Contact Us" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Content Management' },
                            { label: 'Pages', href: route('admin.pages.index') },
                            { label: 'Edit Contact Us' }
                        ]" 
                        class="mb-2"
                    />
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                        Edit Contact Us Page
                    </h1>
                    <p class="text-sm text-gray-500">Manage your contact page content and form settings.</p>
                </div>
                <div class="flex items-center gap-3">
                     <a :href="route('contact.index')" target="_blank" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center">
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

            <!-- Header Section -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Header Section</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <InputLabel value="Header Title" />
                        <TextInput v-model="form.meta.header_title" class="w-full mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Header Description" />
                        <textarea 
                            v-model="form.meta.header_description"
                            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            rows="3"
                        ></textarea>
                    </div>
                </div>
            </div>

             <!-- Form Subjects -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Form Subjects</h3>
                    <button @click="addSubject" type="button" class="text-sm text-blue-600 font-medium hover:text-blue-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Subject
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div v-for="(subject, index) in form.meta.subjects" :key="index" class="flex items-center gap-3">
                         <TextInput v-model="form.meta.subjects[index]" class="w-full" placeholder="e.g. Order Support" />
                         <button type="button" @click="removeSubject(index)" class="text-xs text-red-600 hover:text-red-800 font-medium">Remove</button>
                    </div>
                     <div v-if="!form.meta.subjects || form.meta.subjects.length === 0" class="text-center py-8 text-gray-500 text-sm">
                        No subjects added yet.
                    </div>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Contact Details</h3>
                    <p class="text-sm text-gray-500">Displayed on the left side of the Contact page.</p>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <InputLabel value="Contact Email" />
                        <TextInput v-model="form.contact_email" type="email" class="w-full mt-1" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel value="Contact Phone" />
                        <TextInput v-model="form.contact_phone" class="w-full mt-1" />
                    </div>
                    <div class="sm:col-span-6">
                        <InputLabel value="Contact Address" />
                        <TextInput v-model="form.address" class="w-full mt-1" />
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
             <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">SEO Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <InputLabel value="Meta Title" />
                        <TextInput v-model="form.meta.seo_title" class="w-full mt-1" />
                    </div>
                    <div>
                        <InputLabel value="Meta Description" />
                        <textarea 
                            v-model="form.meta.seo_description"
                            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            rows="2"
                        ></textarea>
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
