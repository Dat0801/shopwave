<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    stats: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const search = ref(props.filters?.search || '');
const categoryId = ref(props.filters?.category_id || '');
const status = ref(props.filters?.status || '');
const stock = ref(props.filters?.stock || '');

const filterForm = useForm({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || '',
    status: props.filters?.status || '',
    stock: props.filters?.stock || '',
});

const submitFilters = () => {
    filterForm.get(route('admin.products.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters'],
    });
};

// Debounce search
watch(search, debounce((value) => {
    filterForm.search = value;
    submitFilters();
}, 300));

watch([categoryId, status, stock], () => {
    filterForm.category_id = categoryId.value;
    filterForm.status = status.value;
    filterForm.stock = stock.value;
    submitFilters();
});

const getProductImageUrl = (path) => {
    return getImageUrl(path, 100, 100);
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
    <AdminLayout>
        <Head title="Admin - Products" />
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Dashboard', href: route('admin.dashboard') },
                            { label: 'Products' }
                        ]" 
                        class="mb-2"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                </div>
                <div>
                    <Link :href="route('admin.products.create')" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Product
                    </Link>
                </div>
            </div>
        </template>

        <!-- Stats Cards -->
        <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
             <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Inventory Value</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalValue) }}</h3>
                    </div>
                     <div class="rounded-lg bg-blue-50 p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
             </div>
              <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Low Stock Alerts</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ stats.lowStock }} Items</h3>
                    </div>
                     <div class="rounded-lg bg-orange-50 p-3">
                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
             </div>
              <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Categories</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ stats.activeCategories }}</h3>
                    </div>
                     <div class="rounded-lg bg-green-50 p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
             </div>
        </div>

        <!-- Filter Bar -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <div class="w-full sm:w-96">
                <div class="relative">
                     <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        v-model="search"
                        type="text" 
                        class="block w-full rounded-lg border-0 bg-gray-50 py-2.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
                        placeholder="Search by name, SKU or brand..."
                    >
                </div>
            </div>
            <div class="flex items-center gap-3">
                 <select v-model="categoryId" class="rounded-lg border-0 bg-gray-50 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <select v-model="stock" class="rounded-lg border-0 bg-gray-50 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Stock Status</option>
                    <option value="in">In Stock</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
                 <button class="rounded-lg border border-gray-200 p-2.5 hover:bg-gray-50">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
                 <button class="rounded-lg border border-gray-200 p-2.5 hover:bg-gray-50">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
             <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-500">
                         <tr>
                            <th class="px-6 py-4">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </th>
                            <th class="px-6 py-4">Product</th>
                            <th class="px-6 py-4">SKU</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Price</th>
                            <th class="px-6 py-4">Stock</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                             <td class="px-6 py-4">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 border border-gray-200">
                                         <img v-if="product.image_path" :src="getProductImageUrl(product.image_path)" class="h-full w-full object-cover" />
                                         <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                         </div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ product.name }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ product.description }}</div>
                                    </div>
                                </div>
                            </td>
                             <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                SKU-{{ String(product.id).padStart(5, '0') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ product.category?.name }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                ${{ product.sale_price || product.price }}
                            </td>
                             <td class="px-6 py-4 text-gray-600">
                                {{ product.stock }}
                            </td>
                             <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="product.status ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20'">
                                    {{ product.status ? 'In Stock' : 'Inactive' }}
                                </span>
                            </td>
                             <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('admin.products.edit', product.id)" class="text-gray-400 hover:text-indigo-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                     <button @click="destroyProduct(product)" class="text-gray-400 hover:text-red-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
             </div>
            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ products.from }}</span> to <span class="font-medium">{{ products.to }}</span> of <span class="font-medium">{{ products.total }}</span> results
                </div>
                 <div class="flex gap-1">
                     <template v-for="(link, key) in products.links" :key="key">
                        <div v-if="link.url === null"  class="px-3 py-1 text-sm text-gray-400 border rounded bg-white" v-html="link.label" />
                        <Link v-else :href="link.url" class="px-3 py-1 text-sm border rounded hover:bg-gray-50" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' : 'bg-white text-gray-700'" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
