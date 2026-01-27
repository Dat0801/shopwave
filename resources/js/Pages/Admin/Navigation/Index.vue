<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import draggable from 'vuedraggable';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { 
    Move, Edit2, Trash2, Plus, Eye, EyeOff, 
    Save, GripVertical, CornerDownRight, Link as LinkIcon
} from 'lucide-vue-next';

const props = defineProps({
    items: Array,
    currentLocation: String,
    categories: Array,
    pages: Array,
});

const locations = [
    { id: 'header', label: 'Main Header Menu' },
    { id: 'footer', label: 'Footer Navigation' },
    { id: 'mobile', label: 'Mobile Sidebar' },
];

const menuItems = ref(props.items);

// Update local state when props change
watch(() => props.items, (newItems) => {
    menuItems.value = newItems;
}, { deep: true });

const switchLocation = (loc) => {
    router.get(route('admin.navigations.index', { location: loc }));
};

const saveOrder = () => {
    router.post(route('admin.navigations.reorder'), { 
        items: menuItems.value 
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: show toast via global flash handler
        }
    });
};

// Modal & Form
const showModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    name: '',
    type: 'custom',
    url: '',
    related_id: null,
    parent_id: null,
    location: props.currentLocation,
    is_active: true,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.location = props.currentLocation;
    showModal.value = true;
};

const editItem = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.type = item.type;
    form.url = item.url;
    form.related_id = item.related_id;
    form.parent_id = item.parent_id;
    form.location = item.location;
    form.is_active = !!item.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingItem.value = null;
};

const submitForm = () => {
    if (editingItem.value) {
        form.put(route('admin.navigations.update', editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.navigations.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (!confirm('Are you sure you want to delete this menu item?')) return;
    router.delete(route('admin.navigations.destroy', item.id));
};

// Computed
const parentOptions = computed(() => {
    // Only allow top-level items as parents (max nesting 2)
    // Exclude self if editing
    return menuItems.value.filter(item => {
        if (editingItem.value && item.id === editingItem.value.id) return false;
        return true;
    });
});

const totalItems = computed(() => {
    let count = menuItems.value.length;
    menuItems.value.forEach(item => {
        if (item.children) count += item.children.length;
    });
    return count;
});
</script>

<template>
  <AdminLayout>
    <Head title="Navigation Management" />
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Navigation Management</h1>
        <p class="mt-1 text-sm text-gray-500">Organize your storefront menus using a hierarchical tree.</p>
    </div>

    <!-- Tabs & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <button 
                v-for="loc in locations" 
                :key="loc.id"
                @click="switchLocation(loc.id)"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-md transition-all',
                    currentLocation === loc.id 
                        ? 'bg-white text-gray-900 shadow-sm' 
                        : 'text-gray-500 hover:text-gray-700'
                ]"
            >
                {{ loc.label }}
            </button>
        </div>
        <div class="flex gap-2">
            <PrimaryButton @click="saveOrder">
                <Save class="w-4 h-4 mr-2" />
                Save Changes
            </PrimaryButton>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Visibility Toggle (Visual Only) -->
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <Eye class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900">Menu Visibility</h3>
                    <p class="text-xs text-gray-500">Disable this to hide the menu from storefront.</p>
                </div>
            </div>
             <div class="relative inline-flex h-6 w-11 items-center rounded-full bg-blue-600 cursor-pointer">
                <span class="translate-x-6 inline-block h-4 w-4 transform rounded-full bg-white transition"/>
             </div>
        </div>

        <!-- Header Row -->
        <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            <div class="col-span-6">Menu Item</div>
            <div class="col-span-3">Destination</div>
            <div class="col-span-1">Status</div>
            <div class="col-span-2 text-right">Actions</div>
        </div>

        <!-- Draggable List -->
        <div class="divide-y divide-gray-100">
             <draggable 
                v-model="menuItems" 
                group="menu" 
                item-key="id"
                handle=".drag-handle"
                :animation="200"
            >
                <template #item="{ element }">
                    <div class="bg-white">
                        <!-- Parent Item -->
                        <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-gray-50 group">
                            <div class="col-span-6 flex items-center gap-3">
                                <button class="drag-handle text-gray-400 hover:text-gray-600 cursor-grab active:cursor-grabbing">
                                    <GripVertical class="w-5 h-5" />
                                </button>
                                <span class="font-medium text-gray-900">{{ element.name }}</span>
                            </div>
                            <div class="col-span-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ element.type }}
                                </span>
                            </div>
                            <div class="col-span-1">
                                <span 
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        element.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ element.is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </div>
                            <div class="col-span-2 flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editItem(element)" class="text-gray-400 hover:text-blue-600 p-1">
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button @click="deleteItem(element)" class="text-gray-400 hover:text-red-600 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Nested Items (Level 2) -->
                        <div v-if="element.children && element.children.length > 0" class="pl-12 pr-6 bg-gray-50/50">
                             <draggable 
                                v-model="element.children" 
                                group="menu-child" 
                                item-key="id"
                                handle=".drag-handle-child"
                                :animation="200"
                            >
                                <template #item="{ element: child }">
                                     <div class="grid grid-cols-12 gap-4 py-3 items-center border-t border-gray-100/50 hover:bg-gray-100 group">
                                        <div class="col-span-6 flex items-center gap-3">
                                            <CornerDownRight class="w-4 h-4 text-gray-300" />
                                            <button class="drag-handle-child text-gray-400 hover:text-gray-600 cursor-grab">
                                                <GripVertical class="w-4 h-4" />
                                            </button>
                                            <span class="text-sm text-gray-700">{{ child.name }}</span>
                                        </div>
                                         <div class="col-span-3">
                                            <span class="text-xs text-gray-500">{{ child.type }}</span>
                                        </div>
                                        <div class="col-span-1">
                                            <span :class="child.is_active ? 'text-green-600' : 'text-gray-400'" class="text-xs font-medium">
                                                {{ child.is_active ? 'Active' : 'Hidden' }}
                                            </span>
                                        </div>
                                        <div class="col-span-2 flex justify-end gap-2 opacity-0 group-hover:opacity-100">
                                            <button @click="editItem(child)" class="text-gray-400 hover:text-blue-600 p-1">
                                                <Edit2 class="w-3 h-3" />
                                            </button>
                                            <button @click="deleteItem(child)" class="text-gray-400 hover:text-red-600 p-1">
                                                <Trash2 class="w-3 h-3" />
                                            </button>
                                        </div>
                                     </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </template>
            </draggable>
            
            <!-- Empty State -->
            <div v-if="menuItems.length === 0" class="p-12 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <LinkIcon class="w-6 h-6" />
                </div>
                <h3 class="text-lg font-medium text-gray-900">No menu items</h3>
                <p class="text-gray-500 mt-1">Get started by adding a new item to this menu.</p>
            </div>
        </div>
        
        <!-- Add Button -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <button 
                @click="openCreateModal"
                class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center gap-2 font-medium"
            >
                <Plus class="w-5 h-5" />
                Add New Menu Item
            </button>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2 mb-4">
                <div class="w-1 h-5 bg-blue-600 rounded-full"></div>
                Menu Configuration
            </h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <dt class="text-gray-500">Total Items</dt>
                    <dd class="font-medium text-gray-900">{{ totalItems }} Items</dd>
                </div>
                 <div class="flex justify-between border-b border-gray-100 pb-2">
                    <dt class="text-gray-500">Max Nesting Level</dt>
                    <dd class="font-medium text-gray-900">2 Levels</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Current Menu</dt>
                    <dd class="font-medium text-gray-900">{{ locations.find(l => l.id === currentLocation)?.label }}</dd>
                </div>
            </dl>
        </div>
        <!-- Quick Links Helper -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
             <h3 class="font-semibold text-gray-900 flex items-center gap-2 mb-4">
                <LinkIcon class="w-4 h-4 text-blue-600" />
                Quick Link Reference
            </h3>
            <div class="flex flex-wrap gap-2">
                <span class="px-2.5 py-1 bg-gray-100 text-xs rounded-md text-gray-600 font-medium border border-gray-200">Blog Posts</span>
                <span class="px-2.5 py-1 bg-gray-100 text-xs rounded-md text-gray-600 font-medium border border-gray-200">Categories</span>
                <span class="px-2.5 py-1 bg-gray-100 text-xs rounded-md text-gray-600 font-medium border border-gray-200">Promotions</span>
                <span class="px-2.5 py-1 bg-gray-100 text-xs rounded-md text-gray-600 font-medium border border-gray-200">New Arrivals</span>
            </div>
            <p class="text-xs text-gray-500 mt-4">Use these references when creating custom URL destinations.</p>
        </div>
    </div>

    <!-- Modal -->
    <Modal :show="showModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                {{ editingItem ? 'Edit Menu Item' : 'Add New Menu Item' }}
            </h2>
            
            <form @submit.prevent="submitForm">
                <div class="space-y-5">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" placeholder="e.g. Home" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Parent -->
                    <div>
                        <InputLabel for="parent_id" value="Parent Item (Optional)" />
                        <select 
                            id="parent_id" 
                            v-model="form.parent_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option :value="null">None (Top Level)</option>
                            <option 
                                v-for="item in parentOptions" 
                                :key="item.id" 
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select a parent to nest this item (max 2 levels).</p>
                    </div>

                    <!-- Type -->
                    <div>
                        <InputLabel for="type" value="Destination Type" />
                        <div class="grid grid-cols-2 gap-2 mt-1">
                             <button 
                                type="button"
                                v-for="t in ['custom', 'route', 'category', 'page']"
                                :key="t"
                                @click="form.type = t"
                                :class="[
                                    'px-3 py-2 text-sm font-medium rounded-md border text-center capitalize transition-colors',
                                    form.type === t 
                                        ? 'bg-blue-50 border-blue-200 text-blue-700' 
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'
                                ]"
                            >
                                {{ t }}
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Fields based on Type -->
                    <div v-if="form.type === 'custom'" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                         <InputLabel for="url" value="Custom URL" />
                        <TextInput id="url" v-model="form.url" placeholder="https://" class="mt-1 block w-full" />
                    </div>

                    <div v-if="form.type === 'route'" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                         <InputLabel for="url" value="Route Name" />
                        <TextInput id="url" v-model="form.url" placeholder="e.g. shop.index" class="mt-1 block w-full" />
                    </div>

                    <div v-if="form.type === 'category'" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <InputLabel for="related_id" value="Select Category" />
                        <select 
                            id="related_id" 
                            v-model="form.related_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>

                    <div v-if="form.type === 'page'" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <InputLabel for="related_id" value="Select Page" />
                         <select 
                            id="related_id" 
                            v-model="form.related_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option v-for="page in pages" :key="page.id" :value="page.id">{{ page.name }}</option>
                        </select>
                    </div>

                    <!-- Active -->
                    <div class="flex items-center pt-2">
                         <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-900">Active Status</span>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ editingItem ? 'Update Item' : 'Create Item' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
  </AdminLayout>
</template>
