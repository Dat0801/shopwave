<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { useForm, Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { debounce } from 'lodash';

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
    description: '',
    status: true,
    parent_id: '',
    image: null,
});

const updateForm = useForm({
    id: null,
    name: '',
    slug: '',
    description: '',
    status: true,
    parent_id: '',
    image: null,
    _method: 'PUT',
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const createSlugTouched = ref(false);
const expandedCategories = ref(new Set());

// Initialize expanded categories from local storage or default
const toggleExpand = (categoryId) => {
    if (expandedCategories.value.has(categoryId)) {
        expandedCategories.value.delete(categoryId);
    } else {
        expandedCategories.value.add(categoryId);
    }
};

const collapseAll = () => {
    expandedCategories.value.clear();
};

const isSearching = computed(() => !!filterForm.search);

const flattenCategories = computed(() => {
    if (isSearching.value) {
        return props.categories.data.map(cat => ({
            ...cat,
            depth: 0,
            hasChildren: false, // In search results, we show flat list
        }));
    }

    const result = [];
    
    const process = (cats, depth = 0) => {
        if (!cats) return;
        
        cats.forEach(cat => {
            const hasChildren = cat.children && cat.children.length > 0;
            const isExpanded = expandedCategories.value.has(cat.id);
            
            result.push({
                ...cat,
                depth,
                hasChildren,
                isExpanded
            });

            if (hasChildren && isExpanded) {
                process(cat.children, depth + 1);
            }
        });
    };

    process(props.categories.data);
    return result;
});

const availableParentCategories = computed(() => {
    if (!Array.isArray(props.allCategories)) {
        return [];
    }
    // Filter out self and children to avoid cycles (simple check: exclude self)
    // For deeper cycle checks, we'd need more logic, but this is basic.
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

watch(() => createForm.name, (value) => {
    if (!createSlugTouched.value) {
        createForm.slug = slugify(value);
    }
});

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

watch(() => filterForm.search, debounce(() => {
    submitFilters();
}, 300));

const submitCreate = () => {
    createForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createSlugTouched.value = false;
            createForm.reset();
        },
    });
};

const startEdit = (category) => {
    updateForm.id = category.id;
    updateForm.name = category.name;
    updateForm.slug = category.slug;
    updateForm.description = category.description || '';
    updateForm.status = !!category.status;
    updateForm.parent_id = category.parent_id ?? '';
    updateForm.image = null; // Don't preset image file
    updateForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const submitUpdate = () => {
    if (!updateForm.id) return;

    updateForm.post(route('admin.categories.update', updateForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            updateForm.reset();
        },
    });
};

const destroyCategory = (category) => {
    if (!confirm('Delete this category?')) return;
    router.delete(route('admin.categories.destroy', category.id), {
        preserveScroll: true,
    });
};

const toggleStatus = (category) => {
    router.post(route('admin.categories.update', category.id), {
        _method: 'PUT',
        name: category.name,
        slug: category.slug,
        description: category.description,
        parent_id: category.parent_id,
        status: !category.status,
    }, {
        preserveScroll: true,
    });
};

const handleImageUpload = (e, form) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Categories" />
        
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Category Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Organize and manage your product hierarchy and sub-categories.</p>
                </div>
                <div>
                    <button @click="openCreateModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Category
                    </button>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 overflow-x-auto pb-2 sm:pb-0">
                    <button class="rounded-full bg-white px-4 py-1.5 text-sm font-medium text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">
                        All Categories
                    </button>
                    <button class="rounded-full px-4 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-900">
                        Active
                    </button>
                    <button class="rounded-full px-4 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-900">
                        Hidden
                    </button>
                    <div class="h-6 w-px bg-gray-200"></div>
                    <button @click="collapseAll" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        Collapse All
                    </button>
                </div>

                <div class="relative w-full sm:w-72">
                     <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        v-model="filterForm.search"
                        type="text" 
                        class="block w-full rounded-lg border-0 bg-white py-2 pl-10 pr-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" 
                        placeholder="Search categories..."
                    >
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Thumbnail</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Category Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Products</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="category in flattenCategories" :key="category.id" class="hover:bg-gray-50">
                                <!-- Thumbnail -->
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="h-10 w-10 overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center">
                                        <img v-if="category.image_path" :src="category.image_path" :alt="category.name" class="h-full w-full object-cover">
                                        <svg v-else class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </td>
                                <!-- Name -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center" :style="{ paddingLeft: `${category.depth * 24}px` }">
                                        <!-- Expand/Collapse Button -->
                                        <button 
                                            v-if="category.hasChildren"
                                            @click="toggleExpand(category.id)"
                                            class="mr-2 rounded p-0.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                        >
                                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': category.isExpanded }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                        <div v-else class="mr-2 w-5"></div> <!-- Spacer -->

                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                            <div v-if="category.description" class="text-xs text-gray-500">{{ category.description }}</div>
                                            <div v-else-if="category.depth === 0" class="text-xs text-gray-500">Major category</div>
                                            <div v-else class="text-xs text-gray-500">SUB-CATEGORY</div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Products -->
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                        {{ category.products_count }} products
                                    </span>
                                </td>
                                <!-- Status -->
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" 
                                         :class="category.status ? 'bg-blue-600' : 'bg-gray-200'"
                                         role="switch" 
                                         :aria-checked="category.status"
                                         @click="toggleStatus(category)"
                                    >
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" 
                                              :class="category.status ? 'translate-x-5' : 'translate-x-0'"
                                        ></span>
                                    </div>
                                </td>
                                <!-- Actions -->
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <button @click="startEdit(category)" class="mr-3 text-gray-400 hover:text-blue-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button @click="destroyCategory(category)" class="text-gray-400 hover:text-red-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No categories found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
             <div v-if="categories.links && categories.links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-b-xl">
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link 
                        v-if="categories.prev_page_url" 
                        :href="categories.prev_page_url" 
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Previous
                    </Link>
                    <Link 
                        v-if="categories.next_page_url" 
                        :href="categories.next_page_url" 
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Next
                    </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ categories.from }}</span> to <span class="font-medium">{{ categories.to }}</span> of <span class="font-medium">{{ categories.total }}</span> categories
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                             <template v-for="(link, index) in categories.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus:outline-offset-0"
                                    :class="link.active ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0'"
                                    v-html="link.label"
                                />
                                <span 
                                    v-else 
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
                                    v-html="link.label"
                                ></span>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" max-width="lg" @close="closeCreateModal">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Add New Category</h2>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form class="space-y-6" @submit.prevent="submitCreate">
                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900">Thumbnail</label>
                        <div class="mt-2 flex items-center gap-x-3">
                             <div class="h-12 w-12 overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="file" @change="(e) => handleImageUpload(e, createForm)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                        <InputError class="mt-2" :message="createForm.errors.image" />
                    </div>

                    <div>
                        <label for="create-name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                        <div class="mt-2">
                            <Input
                                id="create-name"
                                v-model="createForm.name"
                                type="text"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                placeholder="e.g. Electronics"
                            />
                            <InputError class="mt-2" :message="createForm.errors.name" />
                        </div>
                    </div>

                    <div>
                        <label for="create-description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                        <div class="mt-2">
                            <Input
                                id="create-description"
                                v-model="createForm.description"
                                type="text"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                placeholder="e.g. Major category"
                            />
                            <InputError class="mt-2" :message="createForm.errors.description" />
                        </div>
                    </div>

                    <div>
                        <label for="create-slug" class="block text-sm font-medium leading-6 text-gray-900">Slug</label>
                        <div class="mt-2">
                            <Input
                                id="create-slug"
                                v-model="createForm.slug"
                                type="text"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                placeholder="e.g. electronics"
                                @input="createSlugTouched = true"
                            />
                            <InputError class="mt-2" :message="createForm.errors.slug" />
                        </div>
                    </div>

                    <div>
                        <label for="create-parent" class="block text-sm font-medium leading-6 text-gray-900">Parent Category</label>
                        <div class="mt-2">
                            <select
                                id="create-parent"
                                v-model="createForm.parent_id"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            >
                                <option value="">None (Root Category)</option>
                                <option v-for="category in allCategories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="createForm.errors.parent_id" />
                        </div>
                    </div>

                    <div>
                        <label for="create-status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                        <div class="mt-2">
                            <select
                                id="create-status"
                                v-model="createForm.status"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            >
                                <option :value="true">Active</option>
                                <option :value="false">Inactive</option>
                            </select>
                            <InputError class="mt-2" :message="createForm.errors.status" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="closeCreateModal" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                        <button type="submit" :disabled="createForm.processing" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save Category</button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" max-width="lg" @close="closeEditModal">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Edit Category</h2>
                    <button @click="closeEditModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form class="space-y-6" @submit.prevent="submitUpdate">
                     <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900">Thumbnail</label>
                        <div class="mt-2 flex items-center gap-x-3">
                             <div class="h-12 w-12 overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                <img v-if="updateForm.image_preview" :src="updateForm.image_preview" class="h-full w-full object-cover">
                                <svg v-else class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="file" @change="(e) => handleImageUpload(e, updateForm)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                         <p class="mt-1 text-xs text-gray-500">Leave empty to keep current image.</p>
                        <InputError class="mt-2" :message="updateForm.errors.image" />
                    </div>

                    <div>
                        <label for="update-name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                        <div class="mt-2">
                            <Input
                                id="update-name"
                                v-model="updateForm.name"
                                type="text"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            />
                            <InputError class="mt-2" :message="updateForm.errors.name" />
                        </div>
                    </div>

                    <div>
                        <label for="update-description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                        <div class="mt-2">
                            <Input
                                id="update-description"
                                v-model="updateForm.description"
                                type="text"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                placeholder="e.g. Major category"
                            />
                            <InputError class="mt-2" :message="updateForm.errors.description" />
                        </div>
                    </div>

                    <div>
                        <label for="update-slug" class="block text-sm font-medium leading-6 text-gray-900">Slug</label>
                        <div class="mt-2">
                            <Input
                                id="update-slug"
                                v-model="updateForm.slug"
                                type="text"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            />
                            <InputError class="mt-2" :message="updateForm.errors.slug" />
                        </div>
                    </div>

                    <div>
                        <label for="update-parent" class="block text-sm font-medium leading-6 text-gray-900">Parent Category</label>
                        <div class="mt-2">
                            <select
                                id="update-parent"
                                v-model="updateForm.parent_id"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            >
                                <option value="">None (Root Category)</option>
                                <option v-for="category in availableParentCategories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="updateForm.errors.parent_id" />
                        </div>
                    </div>

                    <div>
                        <label for="update-status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                        <div class="mt-2">
                            <select
                                id="update-status"
                                v-model="updateForm.status"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            >
                                <option :value="true">Active</option>
                                <option :value="false">Inactive</option>
                            </select>
                            <InputError class="mt-2" :message="updateForm.errors.status" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="closeEditModal" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                        <button type="submit" :disabled="updateForm.processing" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Update Category</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>