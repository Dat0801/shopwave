<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    categories: Object,
    filters: Object,
    allCategories: Array,
});

const filterForm = useForm({
    search: props.filters?.search || '',
});

const createForm = useForm({
    name: '',
    slug: '',
    status: true,
    parent_id: '',
});

const updateForm = useForm({
    id: null,
    name: '',
    slug: '',
    status: true,
    parent_id: '',
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const createSlugTouched = ref(false);

const availableParentCategories = computed(() => {
    if (!Array.isArray(props.allCategories)) {
        return [];
    }

    return props.allCategories.filter(
        (category) => category && category.id !== updateForm.id,
    );
});

const slugify = (value) => {
    if (!value) return '';

    return value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
};

watch(
    () => createForm.name,
    (value) => {
        if (!createSlugTouched.value) {
            createForm.slug = slugify(value);
        }
    },
);

const openCreateModal = () => {
    createForm.reset();
    createForm.clearErrors();
    createSlugTouched.value = false;
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const submitFilters = () => {
    filterForm.get(route('admin.categories.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['categories', 'filters'],
    });
};

const submitCreate = () => {
    createForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createSlugTouched.value = false;
            createForm.reset();
            createForm.clearErrors();
        },
    });
};

const startEdit = (category) => {
    updateForm.id = category.id;
    updateForm.name = category.name;
    updateForm.slug = category.slug;
    updateForm.status = category.status;
    updateForm.parent_id = category.parent_id ?? '';
    updateForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const submitUpdate = () => {
    if (!updateForm.id) return;

    updateForm.put(route('admin.categories.update', updateForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            updateForm.reset();
            updateForm.clearErrors();
        },
    });
};

const destroyCategory = (category) => {
    if (!confirm('Delete this category?')) return;

    useForm({})
        .delete(route('admin.categories.destroy', category.id), {
            preserveScroll: true,
        });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Categories
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage categories with a parent-child structure.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form
                        class="flex items-center gap-2"
                        @submit.prevent="submitFilters"
                    >
                        <div class="w-48">
                            <Input
                                v-model="filterForm.search"
                                type="search"
                                placeholder="Search"
                            />
                        </div>
                        <Button type="submit">
                            Filter
                        </Button>
                    </form>
                    <Button
                        type="button"
                        class="w-full justify-center sm:w-auto sm:inline-flex"
                        @click="openCreateModal"
                    >
                        Add category
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Existing categories
                        </h2>
                    </div>

                    <div class="hidden md:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b text-xs uppercase tracking-wide text-gray-500">
                                        <th class="px-3 py-2">
                                            Name
                                        </th>
                                        <th class="px-3 py-2">
                                            Slug
                                        </th>
                                        <th class="px-3 py-2">
                                            Parent
                                        </th>
                                        <th class="px-3 py-2">
                                            Status
                                        </th>
                                        <th class="px-3 py-2 text-right">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="category in categories.data"
                                        :key="category.id"
                                        class="border-b last:border-b-0"
                                    >
                                        <td class="px-3 py-2">
                                            {{ category.name }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ category.slug }}
                                        </td>
                                        <td class="px-3 py-2 text-xs">
                                            <span
                                                v-if="category.parent"
                                                class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700"
                                            >
                                                {{ category.parent.name }}
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-[11px] font-medium text-gray-500"
                                            >
                                                Root
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-xs">
                                            <span
                                                v-if="category.status"
                                                class="rounded-full bg-green-100 px-2 py-0.5 text-green-700"
                                            >
                                                Active
                                            </span>
                                            <span
                                                v-else
                                                class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-700"
                                            >
                                                Inactive
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-right text-xs">
                                            <button
                                                type="button"
                                                class="mr-2 text-indigo-600 hover:text-indigo-800"
                                                @click="startEdit(category)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="text-red-600 hover:text-red-700"
                                                @click="destroyCategory(category)"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-3 md:hidden">
                        <div
                            v-for="category in categories.data"
                            :key="category.id"
                            class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ category.name }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ category.slug }}
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                    :class="category.status
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-gray-100 text-gray-700'"
                                >
                                    {{ category.status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-[11px] text-gray-500">
                                    <span v-if="category.parent">
                                        Parent: {{ category.parent.name }}
                                    </span>
                                    <span v-else>
                                        Root category
                                    </span>
                                </span>
                                <div class="space-x-3 text-xs">
                                    <button
                                        type="button"
                                        class="font-medium text-indigo-600 hover:text-indigo-800"
                                        @click="startEdit(category)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="font-medium text-red-600 hover:text-red-700"
                                        @click="destroyCategory(category)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <Modal :show="showCreateModal" max-width="lg" @close="closeCreateModal">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Add category
                        </h2>
                        <button
                            type="button"
                            class="text-sm text-gray-400 hover:text-gray-600"
                            @click="closeCreateModal"
                        >
                            Close
                        </button>
                    </div>

                    <form class="mt-6 grid gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
                        <div class="md:col-span-2">
                            <Input
                                v-model="createForm.name"
                                placeholder="Category name"
                            />
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.name"
                            />
                        </div>
                        <div>
                            <Input
                                v-model="createForm.slug"
                                placeholder="Slug (auto-generated from name, editable)"
                                @input="createSlugTouched = true"
                            />
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.slug"
                            />
                        </div>
                        <div>
                            <select
                                v-model="createForm.parent_id"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    No parent
                                </option>
                                <option
                                    v-for="category in allCategories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.parent_id"
                            />
                        </div>
                        <div>
                            <select
                                v-model="createForm.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option :value="true">
                                    Active
                                </option>
                                <option :value="false">
                                    Inactive
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.status"
                            />
                        </div>
                        <div class="mt-4 flex items-center justify-end md:col-span-2">
                            <Button
                                type="submit"
                                :disabled="createForm.processing"
                            >
                                Save
                            </Button>
                        </div>
                    </form>
                </div>
            </Modal>

            <Modal :show="showEditModal" max-width="lg" @close="closeEditModal">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Edit category
                        </h2>
                        <button
                            type="button"
                            class="text-sm text-gray-400 hover:text-gray-600"
                            @click="closeEditModal"
                        >
                            Close
                        </button>
                    </div>

                    <form class="mt-6 grid gap-4 md:grid-cols-2" @submit.prevent="submitUpdate">
                        <div class="md:col-span-2">
                            <Input
                                v-model="updateForm.name"
                                placeholder="Category name"
                            />
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.name"
                            />
                        </div>
                        <div>
                            <Input
                                v-model="updateForm.slug"
                                placeholder="Slug"
                            />
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.slug"
                            />
                        </div>
                        <div>
                            <select
                                v-model="updateForm.parent_id"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    No parent
                                </option>
                                <option
                                    v-for="category in availableParentCategories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.parent_id"
                            />
                        </div>
                        <div>
                            <select
                                v-model="updateForm.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option :value="true">
                                    Active
                                </option>
                                <option :value="false">
                                    Inactive
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.status"
                            />
                        </div>
                        <div class="mt-4 flex items-center justify-end md:col-span-2">
                            <Button
                                type="submit"
                                :disabled="updateForm.processing"
                            >
                                Update
                            </Button>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
