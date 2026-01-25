<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedIds = ref([]);

const filterForm = useForm({
    search: props.filters?.search || '',
});

const submitFilters = () => {
    filterForm.get(route('admin.categories.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['categories', 'filters'],
    });
};

watch(search, debounce((value) => {
    filterForm.search = value;
    submitFilters();
}, 300));

const isAllSelected = computed(() => {
    return props.categories.data.length > 0 && selectedIds.value.length === props.categories.data.length;
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.categories.data.map(cat => cat.id);
    }
};

const bulkUpdateStatus = (status) => {
    if (selectedIds.value.length === 0) return;
    
    if (!confirm(`Are you sure you want to set ${selectedIds.value.length} categories to ${status ? 'Active' : 'Inactive'}?`)) return;

    router.post(route('admin.categories.bulk-update-status'), {
        ids: selectedIds.value,
        status: status
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedIds.value = [];
        }
    });
};

const destroyCategory = (category) => {
    if (!confirm('Are you sure you want to delete this category?')) return;

    router.delete(route('admin.categories.destroy', category.id), {
        preserveScroll: true,
    });
};

const getCategoryImageUrl = (path) => {
    return getImageUrl(path, 100, 100);
};
</script>

<template>
    <AdminLayout>
        <Head title="Categories" />
        
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Dashboard', href: route('admin.dashboard') },
                            { label: 'Categories' }
                        ]" 
                        class="mb-2"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                </div>
                <div>
                    <Link :href="route('admin.categories.create')" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Category
                    </Link>
                </div>
            </div>
        </template>

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
                        placeholder="Search categories..."
                    >
                </div>
            </div>
            
            <div class="flex items-center gap-2" v-if="selectedIds.length > 0">
                <span class="text-sm text-gray-500 mr-2">{{ selectedIds.length }} selected</span>
                <button @click="bulkUpdateStatus(true)" class="rounded-lg bg-green-50 px-3 py-1.5 text-sm font-medium text-green-700 hover:bg-green-100">
                    Set Active
                </button>
                <button @click="bulkUpdateStatus(false)" class="rounded-lg bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100">
                    Set Inactive
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-500">
                        <tr>
                            <th class="px-6 py-4 w-10">
                                <input 
                                    type="checkbox" 
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                    :checked="isAllSelected"
                                    @change="toggleSelectAll"
                                >
                            </th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4">Parent Category</th>
                            <th class="px-6 py-4">Products</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        <tr v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input 
                                    type="checkbox" 
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                    :value="category.id"
                                    v-model="selectedIds"
                                >
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 border border-gray-200">
                                        <img v-if="category.image_path" :src="getCategoryImageUrl(category.image_path)" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="font-medium text-gray-900">{{ category.name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                                {{ category.description || '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ category.parent?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ category.products_count }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="category.status ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20'">
                                    {{ category.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('admin.categories.edit', category.id)" class="text-gray-400 hover:text-indigo-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                    <button @click="destroyCategory(category)" class="text-gray-400 hover:text-red-600">
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

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ categories.from }}</span> to <span class="font-medium">{{ categories.to }}</span> of <span class="font-medium">{{ categories.total }}</span> results
                </div>
                <div class="flex gap-1">
                    <template v-for="(link, key) in categories.links" :key="key">
                        <div v-if="link.url === null" class="px-3 py-1 text-sm text-gray-400 border rounded bg-white" v-html="link.label" />
                        <Link v-else :href="link.url" class="px-3 py-1 text-sm border rounded hover:bg-gray-50" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' : 'bg-white text-gray-700'" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
