<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || '',
    status: props.filters?.status || '',
    stock: props.filters?.stock || '',
});

const createForm = useForm({
    name: '',
    slug: '',
    price: '',
    sale_price: '',
    stock: 0,
    description: '',
    category_id: '',
    status: true,
    image: null,
});

const updateForm = useForm({
    id: null,
    name: '',
    slug: '',
    price: '',
    sale_price: '',
    stock: 0,
    description: '',
    category_id: '',
    status: true,
    image: null,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);

const submitFilters = () => {
    filterForm.get(route('admin.products.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters'],
    });
};

const handleImageChange = (event, form) => {
    const file = event.target.files[0];
    form.image = file ?? null;
};

const openCreateModal = () => {
    createForm.reset();
    createForm.clearErrors();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const submitCreate = () => {
    createForm.post(route('admin.products.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
            createForm.clearErrors();
        },
    });
};

const startEdit = (product) => {
    updateForm.id = product.id;
    updateForm.name = product.name;
    updateForm.slug = product.slug;
    updateForm.price = product.price;
    updateForm.sale_price = product.sale_price;
    updateForm.stock = product.stock;
    updateForm.description = product.description;
    updateForm.category_id = product.category_id;
    updateForm.status = product.status;
    updateForm.image = null;
    updateForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const submitUpdate = () => {
    if (!updateForm.id) return;

    updateForm.post(route('admin.products.update', updateForm.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            showEditModal.value = false;
            updateForm.reset();
            updateForm.clearErrors();
        },
    });
};

const destroyProduct = (product) => {
    if (!confirm('Delete this product?')) return;

    useForm({})
        .delete(route('admin.products.destroy', product.id), {
            preserveScroll: true,
        });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin - Products" />
        <template #header>
            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Products
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage your product catalog, inventory, and pricing.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form
                        class="flex flex-wrap items-end gap-2"
                        @submit.prevent="submitFilters"
                    >
                        <div class="w-full sm:w-40">
                            <Input
                                v-model="filterForm.search"
                                type="search"
                                placeholder="Search"
                            />
                        </div>
                        <div class="w-full sm:w-40">
                            <select
                                v-model="filterForm.category_id"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    All categories
                                </option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <select
                                v-model="filterForm.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    All status
                                </option>
                                <option value="active">
                                    Active
                                </option>
                                <option value="inactive">
                                    Inactive
                                </option>
                            </select>
                        </div>
                        <div class="w-full sm:w-36">
                            <select
                                v-model="filterForm.stock"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    All stock
                                </option>
                                <option value="in">
                                    In stock
                                </option>
                                <option value="low">
                                    Low stock (1–5)
                                </option>
                                <option value="out">
                                    Out of stock
                                </option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button type="submit">
                                Filter
                            </Button>
                            <button
                                type="button"
                                class="text-xs text-gray-500 hover:text-gray-900"
                                @click="
                                    filterForm.search = '';
                                    filterForm.category_id = '';
                                    filterForm.status = '';
                                    filterForm.stock = '';
                                    submitFilters();
                                "
                            >
                                Clear
                            </button>
                        </div>
                    </form>
                    <Button
                        type="button"
                        class="w-full justify-center sm:w-auto sm:inline-flex"
                        @click="openCreateModal"
                    >
                        Add product
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">

                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Existing products
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
                                            Category
                                        </th>
                                        <th class="px-3 py-2">
                                            Price
                                        </th>
                                        <th class="px-3 py-2">
                                            Stock
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
                                        v-for="product in products.data"
                                        :key="product.id"
                                        class="border-b last:border-b-0"
                                    >
                                        <td class="px-3 py-2">
                                            {{ product.name }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ product.category?.name }}
                                        </td>
                                        <td class="px-3 py-2">
                                            ${{ product.sale_price || product.price }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ product.stock }}
                                        </td>
                                        <td class="px-3 py-2 text-xs">
                                            <span
                                                v-if="product.status"
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
                                                @click="startEdit(product)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="text-red-600 hover:text-red-700"
                                                @click="destroyProduct(product)"
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
                            v-for="product in products.data"
                            :key="product.id"
                            class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ product.name }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ product.category?.name || 'Uncategorized' }}
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                    :class="product.status
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-gray-100 text-gray-700'"
                                >
                                    {{ product.status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="text-xs text-gray-700">
                                    <span class="font-medium">
                                        ${{ product.sale_price || product.price }}
                                    </span>
                                    <span class="ml-2 text-[11px] text-gray-500">
                                        Stock: {{ product.stock }}
                                    </span>
                                </div>
                                <div class="space-x-3 text-xs">
                                    <button
                                        type="button"
                                        class="font-medium text-indigo-600 hover:text-indigo-800"
                                        @click="startEdit(product)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="font-medium text-red-600 hover:text-red-700"
                                        @click="destroyProduct(product)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <Modal :show="showCreateModal" max-width="xl" @close="closeCreateModal">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Add product
                    </h2>
                    <button
                        type="button"
                        class="text-sm text-gray-500 hover:text-gray-900"
                        @click="closeCreateModal"
                    >
                        Close
                    </button>
                </div>

                <form class="mt-6 grid gap-4 md:grid-cols-4" @submit.prevent="submitCreate">
                    <div>
                        <Input
                            v-model="createForm.name"
                            placeholder="Name"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.name"
                        />
                    </div>
                    <div>
                        <Input
                            v-model="createForm.slug"
                            placeholder="Slug"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.slug"
                        />
                    </div>
                    <div>
                        <Input
                            v-model="createForm.price"
                            type="number"
                            step="0.01"
                            placeholder="Price"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.price"
                        />
                    </div>
                    <div>
                        <Input
                            v-model="createForm.sale_price"
                            type="number"
                            step="0.01"
                            placeholder="Sale price"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.sale_price"
                        />
                    </div>
                    <div>
                        <Input
                            v-model="createForm.stock"
                            type="number"
                            min="0"
                            placeholder="Stock"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.stock"
                        />
                    </div>
                    <div>
                        <select
                            v-model="createForm.category_id"
                            class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">
                                Category
                            </option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.category_id"
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
                    <div>
                        <input
                            type="file"
                            class="block w-full text-sm text-gray-700 file:mr-2 file:rounded-md file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-indigo-500"
                            @change="(event) => handleImageChange(event, createForm)"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.image"
                        />
                    </div>
                    <div class="md:col-span-4">
                        <textarea
                            v-model="createForm.description"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Description"
                        />
                        <InputError
                            class="mt-1"
                            :message="createForm.errors.description"
                        />
                    </div>
                    <div class="flex items-center justify-end md:col-span-4">
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

        <Modal :show="showEditModal" max-width="xl" @close="closeEditModal">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Edit product
                    </h2>
                    <button
                        type="button"
                        class="text-sm text-gray-500 hover:text-gray-900"
                        @click="closeEditModal"
                    >
                        Close
                    </button>
                </div>

                <form class="mt-6 grid gap-4 md:grid-cols-4" @submit.prevent="submitUpdate">
                    <div>
                        <Input
                            v-model="updateForm.name"
                            placeholder="Name"
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
                        <Input
                            v-model="updateForm.price"
                            type="number"
                            step="0.01"
                            placeholder="Price"
                        />
                        <InputError
                            class="mt-1"
                            :message="updateForm.errors.price"
                        />
                    </div>
                    <div>
                        <Input
                            v-model="updateForm.sale_price"
                            type="number"
                            step="0.01"
                            placeholder="Sale price"
                        />
                        <InputError
                            class="mt-1"
                            :message="updateForm.errors.sale_price"
                        />
                    </div>
                    <div>
                        <Input
                            v-model="updateForm.stock"
                            type="number"
                            min="0"
                            placeholder="Stock"
                        />
                        <InputError
                            class="mt-1"
                            :message="updateForm.errors.stock"
                        />
                    </div>
                    <div>
                        <select
                            v-model="updateForm.category_id"
                            class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">
                                Category
                            </option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-1"
                            :message="updateForm.errors.category_id"
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
                    <div>
                        <input
                            type="file"
                            class="block w-full text-sm text-gray-700 file:mr-2 file:rounded-md file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-indigo-500"
                            @change="(event) => handleImageChange(event, updateForm)"
                        />
                        <InputError
                            class="mt-1"
                            :message="updateForm.errors.image"
                        />
                    </div>
                    <div class="md:col-span-4">
                        <textarea
                            v-model="updateForm.description"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Description"
                        />
                        <InputError
                            class="mt-1"
                            :message="updateForm.errors.description"
                        />
                    </div>
                    <div class="flex items-center justify-end md:col-span-4">
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
    </AuthenticatedLayout>
</template>
