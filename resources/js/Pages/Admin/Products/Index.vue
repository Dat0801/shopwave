<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters?.search || '',
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

const submitCreate = () => {
    createForm.post(route('admin.products.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => createForm.reset(),
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
};

const submitUpdate = () => {
    if (!updateForm.id) return;

    updateForm.post(route('admin.products.update', updateForm.id), {
        preserveScroll: true,
        forceFormData: true,
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
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">
                    Products
                </h1>
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
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-800">
                        Create product
                    </h2>

                    <form
                        class="grid gap-4 md:grid-cols-4"
                        @submit.prevent="submitCreate"
                    >
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
                </section>

                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-800">
                        Existing products
                    </h2>

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
                </section>

                <section
                    v-if="updateForm.id"
                    class="rounded-lg bg-white p-4 shadow-sm"
                >
                    <h2 class="mb-4 text-sm font-semibold text-gray-800">
                        Edit product
                    </h2>

                    <form
                        class="grid gap-4 md:grid-cols-4"
                        @submit.prevent="submitUpdate"
                    >
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
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

