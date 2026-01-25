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
    banner: Object,
});

const form = useForm({
    title: props.banner?.title || '',
    link: props.banner?.link || '',
    description: props.banner?.description || '',
    is_active: props.banner !== undefined ? Boolean(props.banner.is_active) : true,
    order: props.banner?.order || 0,
    duration: props.banner?.duration || 5000,
    image: null,
});

const imagePreview = ref(props.banner?.image_path ? getImageUrl(props.banner.image_path) : null);

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

const submit = () => {
    if (props.banner) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.banners.update', props.banner.id));
    } else {
        form.post(route('admin.banners.store'));
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="props.banner ? 'Admin - Edit Banner' : 'Admin - Create Banner'" />

        <template #header>
            <div>
                <Breadcrumb 
                    :items="[
                        { label: 'Admin', href: route('admin.dashboard') },
                        { label: 'Banners', href: route('admin.banners.index') },
                        { label: props.banner ? 'Edit' : 'Create' }
                    ]" 
                    class="mb-1"
                />
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ props.banner ? 'Edit Banner' : 'Create Banner' }}
                </h1>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Form -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    
                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            placeholder="Summer Sale"
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Image -->
                    <div>
                        <InputLabel for="image" value="Banner Image" />
                        <div class="mt-2 flex items-center gap-x-3">
                            <div v-if="imagePreview" class="h-32 w-52 overflow-hidden rounded-lg border border-gray-200">
                                <img :src="imagePreview" alt="Preview" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="h-32 w-52 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400">
                                <span class="text-sm">No image</span>
                            </div>
                            <label for="image" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 cursor-pointer">
                                <span>Change</span>
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" @change="handleImageChange" />
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>

                    <!-- Link -->
                    <div>
                        <InputLabel for="link" value="Link URL (Optional)" />
                        <TextInput
                            id="link"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.link"
                            placeholder="https://example.com/shop"
                        />
                        <p class="mt-1 text-sm text-gray-500">The URL to redirect to when the banner is clicked.</p>
                        <InputError class="mt-2" :message="form.errors.link" />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel for="description" value="Description (Optional)" />
                        <textarea
                            id="description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.description"
                            rows="3"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Order -->
                        <div>
                            <InputLabel for="order" value="Display Order" />
                            <TextInput
                                id="order"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.order"
                            />
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                            <InputError class="mt-2" :message="form.errors.order" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <InputLabel for="duration" value="Duration (ms)" />
                            <TextInput
                                id="duration"
                                type="number"
                                step="100"
                                class="mt-1 block w-full"
                                v-model="form.duration"
                            />
                            <p class="mt-1 text-sm text-gray-500">Duration in milliseconds (e.g. 5000 = 5s)</p>
                            <InputError class="mt-2" :message="form.errors.duration" />
                        </div>

                        <!-- Status -->
                        <div class="flex items-center pt-6">
                            <div class="flex items-center h-5">
                                <input
                                    id="is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                                    v-model="form.is_active"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-900">Active</label>
                                <p class="text-gray-500">Display this banner on the home page.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-6">
                        <Link :href="route('admin.banners.index')" class="text-sm font-semibold leading-6 text-gray-900">Cancel</Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-blue-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ props.banner ? 'Update Banner' : 'Create Banner' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
