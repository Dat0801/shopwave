<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    banner: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.banner.title || '',
    description: props.banner.description || '',
    button_text: props.banner.button_text || '',
    link: props.banner.link || '',
    start_date: props.banner.start_date ? new Date(props.banner.start_date).toISOString().slice(0, 16) : '',
    end_date: props.banner.end_date ? new Date(props.banner.end_date).toISOString().slice(0, 16) : '',
    placement: props.banner.placement || 'Homepage Hero (Top)',
    duration: props.banner.duration || 5000,
    is_active: Boolean(props.banner.is_active),
    image: null,
    mobile_image: null,
});

const imagePreview = ref(props.banner.image_path ? getImageUrl(props.banner.image_path) : null);
const mobileImagePreview = ref(props.banner.mobile_image_path ? getImageUrl(props.banner.mobile_image_path) : null);

const handleImageChange = (e, type) => {
    const file = e.target.files[0];
    if (file) {
        if (type === 'desktop') {
            form.image = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            form.mobile_image = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                mobileImagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.banners.update', props.banner.id));
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Edit Banner" />

        <template #header>
            <div class="mb-6">
                <Breadcrumb 
                    :items="[
                        { label: 'Marketing', href: '#' },
                        { label: 'Banners', href: route('admin.banners.index') },
                        { label: 'Edit Banner' }
                    ]" 
                    class="mb-2"
                />
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                    Edit Banner
                </h1>
                <p class="mt-2 text-base text-gray-600">
                    Update your marketing banner details.
                </p>
            </div>
        </template>

        <form @submit.prevent="submit" class="pb-24"> <!-- Added padding for sticky footer -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Banner Content Details -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Banner Content Details</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Banner Title" class="font-medium text-gray-700" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-50 border-gray-200 focus:bg-white transition-colors"
                                    v-model="form.title"
                                    placeholder="e.g., Summer Flash Sale"
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Subtitle / Description" class="font-medium text-gray-700" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-colors"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="e.g., Get up to 70% off on all seasonal footwear items. Limited time only."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- CTA & Link -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="button_text" value="CTA Button Text" class="font-medium text-gray-700" />
                                    <TextInput
                                        id="button_text"
                                        type="text"
                                        class="mt-1 block w-full bg-gray-50 border-gray-200 focus:bg-white"
                                        v-model="form.button_text"
                                        placeholder="e.g., Shop Now"
                                    />
                                    <InputError class="mt-2" :message="form.errors.button_text" />
                                </div>

                                <div>
                                    <InputLabel for="link" value="Target URL" class="font-medium text-gray-700" />
                                    <TextInput
                                        id="link"
                                        type="text"
                                        class="mt-1 block w-full bg-gray-50 border-gray-200 focus:bg-white"
                                        v-model="form.link"
                                        placeholder="e.g., /collections/seasonal"
                                    />
                                    <InputError class="mt-2" :message="form.errors.link" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Scheduling (Optional)</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="start_date" value="Start Date" class="font-medium text-gray-700" />
                                <div class="relative mt-1">
                                    <TextInput
                                        id="start_date"
                                        type="datetime-local"
                                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white"
                                        v-model="form.start_date"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>

                            <div>
                                <InputLabel for="end_date" value="End Date" class="font-medium text-gray-700" />
                                <div class="relative mt-1">
                                    <TextInput
                                        id="end_date"
                                        type="datetime-local"
                                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white"
                                        v-model="form.end_date"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.end_date" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Placement -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Placement & Visibility</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <InputLabel for="placement" value="Banner Placement" class="font-medium text-gray-700" />
                                <select
                                    id="placement"
                                    v-model="form.placement"
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="Homepage Hero (Top)">Homepage Hero (Top)</option>
                                    <option value="Sidebar">Sidebar</option>
                                    <option value="Footer">Footer</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.placement" />
                            </div>

                            <div>
                                <InputLabel for="duration" value="Slide Duration (ms)" class="font-medium text-gray-700" />
                                <TextInput
                                    id="duration"
                                    type="number"
                                    step="100"
                                    min="1000"
                                    class="mt-1 block w-full bg-gray-50 border-gray-200 focus:bg-white"
                                    v-model="form.duration"
                                    placeholder="e.g., 5000"
                                />
                                <InputError class="mt-2" :message="form.errors.duration" />
                            </div>

                            <div class="flex items-start gap-2 text-sm text-blue-600 bg-blue-50 p-3 rounded-lg">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p>Visibility can be toggled in the Banners list.</p>
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
                                <InputLabel value="Desktop Version (Main)" class="font-medium text-gray-700 mb-2" />
                                <div class="relative group">
                                    <div 
                                        class="w-full aspect-[2/1] rounded-lg border-2 border-dashed border-blue-100 bg-blue-50/50 flex flex-col items-center justify-center text-center p-4 transition-colors hover:border-blue-300 hover:bg-blue-50 overflow-hidden"
                                        :class="{'border-blue-300 bg-blue-50': imagePreview}"
                                    >
                                        <template v-if="imagePreview">
                                            <img :src="imagePreview" class="w-full h-full object-cover rounded-md" />
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <p class="text-white font-medium">Click to change</p>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <svg class="w-10 h-10 text-blue-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">Click to upload or drag and drop</p>
                                            <p class="text-xs text-gray-500 mt-1">Recommended size: 1920×600px (Max 5MB)</p>
                                        </template>
                                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" @change="(e) => handleImageChange(e, 'desktop')" />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.image" />
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div>
                                <InputLabel value="Mobile Version (Responsive)" class="font-medium text-gray-700 mb-2" />
                                <div class="relative group">
                                    <div 
                                        class="w-full aspect-[3/2] rounded-lg border-2 border-dashed border-blue-100 bg-blue-50/50 flex flex-col items-center justify-center text-center p-4 transition-colors hover:border-blue-300 hover:bg-blue-50 overflow-hidden"
                                        :class="{'border-blue-300 bg-blue-50': mobileImagePreview}"
                                    >
                                        <template v-if="mobileImagePreview">
                                            <img :src="mobileImagePreview" class="w-full h-full object-cover rounded-md" />
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <p class="text-white font-medium">Click to change</p>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <svg class="w-8 h-8 text-blue-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">Upload Mobile Image</p>
                                            <p class="text-xs text-gray-500 mt-1">Recommended: 800×800px (Max 2MB)</p>
                                        </template>
                                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" @change="(e) => handleImageChange(e, 'mobile')" />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.mobile_image" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer -->
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 z-50 md:pl-64 transition-all duration-300">
                <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Draft Status</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ form.is_active ? 'Published' : 'Not yet published' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('admin.banners.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2">
                            Discard
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            Update Banner
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
