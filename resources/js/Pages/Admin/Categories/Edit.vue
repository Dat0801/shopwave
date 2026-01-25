<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import CategoryTreeSelect from '@/Components/Admin/CategoryTreeSelect.vue';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    category: Object,
    allCategories: Array,
});

const form = useForm({
    _method: 'PUT',
    name: props.category.name,
    slug: props.category.slug,
    description: props.category.description || '',
    status: !!props.category.status,
    parent_id: props.category.parent_id ?? '',
    image: null,
    order: props.category.order ?? 0,
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
    }
};

const submit = () => {
    form.post(route('admin.categories.update', props.category.id), {
        preserveScroll: true,
    });
};

const destroy = () => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(route('admin.categories.destroy', props.category.id));
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Edit Category" />

        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Admin', href: route('admin.dashboard') },
                            { label: 'Categories', href: route('admin.categories.index') },
                            { label: 'Edit Category' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">Edit Category: {{ category.name }}</h1>
                </div>
                <button
                    @click="destroy"
                    class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50"
                >
                    Delete Category
                </button>
            </div>
        </template>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column (Main Content) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- General Information -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">General Information</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Category Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    placeholder="e.g. Electronics"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="slug" value="Slug" />
                                <TextInput
                                    id="slug"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.slug"
                                    placeholder="e.g. electronics"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.slug" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    rows="4"
                                    v-model="form.description"
                                    placeholder="Category description..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Category Image</h2>
                        
                        <div class="space-y-4">
                            <div v-if="category.image_path" class="mb-4">
                                <img :src="getImageUrl(category.image_path)" alt="Current Image" class="h-32 w-full object-cover rounded-lg" />
                            </div>
                            
                            <input
                                type="file"
                                @change="handleImageChange"
                                class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100"
                            />
                            <InputError :message="form.errors.image" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('admin.categories.index')"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-center text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            :disabled="form.processing"
                        >
                            Update Category
                        </button>
                    </div>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="space-y-8">
                    <!-- Status & Visibility -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Status & Organization</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="status"
                                    v-model:checked="form.status"
                                />
                                <InputLabel for="status" value="Active" class="!mb-0" />
                            </div>
                            <InputError :message="form.errors.status" />

                            <div>
                                <InputLabel for="parent_id" value="Parent Category" />
                                <CategoryTreeSelect
                                    v-model="form.parent_id"
                                    :options="allCategories"
                                    :exclude-id="category.id"
                                    class="mt-1"
                                />
                                <InputError class="mt-2" :message="form.errors.parent_id" />
                            </div>

                            <div>
                                <InputLabel for="order" value="Display Order" />
                                <TextInput
                                    id="order"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.order"
                                />
                                <InputError class="mt-2" :message="form.errors.order" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
