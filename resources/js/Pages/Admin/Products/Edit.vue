<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    product: Object,
    categories: Array,
});

const form = useForm({
    _method: 'PUT',
    name: props.product.name,
    slug: props.product.slug,
    description: props.product.description || '',
    category_id: props.product.category_id,
    price: props.product.price,
    sale_price: props.product.sale_price,
    sku: '', // Product table doesn't have sku, but keeping for UI consistency or future use
    stock: props.product.stock,
    weight: '', // Product table doesn't have weight
    image: null,
    status: props.product.status ? true : false,
    options: [],
    variants: [],
});

onMounted(() => {
    // Reconstruct options and variants
    if (props.product.variants && props.product.variants.length > 0) {
        // Populate variants
        form.variants = props.product.variants.map(v => ({
            name: v.name,
            sku: v.sku,
            price: v.price,
            stock: v.stock,
            active: !!v.active,
            options: v.options
        }));

        // Reconstruct options for the generator
        const optionMap = {};
        props.product.variants.forEach(variant => {
            if (variant.options) {
                Object.entries(variant.options).forEach(([key, value]) => {
                    if (!optionMap[key]) optionMap[key] = new Set();
                    optionMap[key].add(value);
                });
            }
        });
        
        form.options = Object.entries(optionMap).map(([name, values]) => ({
            name,
            values: Array.from(values)
        }));
    }
});

const submit = () => {
    form.post(route('admin.products.update', props.product.id));
};

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
    }
};

// Product Variants Logic
const addOption = () => {
    form.options.push({ name: '', values: [] });
};

const removeOption = (index) => {
    form.options.splice(index, 1);
    generateVariants();
};

const addOptionValue = (index, event) => {
    const value = event.target.value.trim();
    if (value && !form.options[index].values.includes(value)) {
        form.options[index].values.push(value);
        event.target.value = '';
        generateVariants();
    }
};

const removeOptionValue = (optionIndex, valueIndex) => {
    form.options[optionIndex].values.splice(valueIndex, 1);
    generateVariants();
};

const generateVariants = () => {
    const validOptions = form.options.filter(opt => opt.values.length > 0);
    
    if (validOptions.length === 0) {
        form.variants = [];
        return;
    }

    const cartesian = (arrays) => {
        return arrays.reduce((acc, curr) => {
            return acc.flatMap(x => curr.map(y => [...x, y]));
        }, [[]]);
    };

    const combinations = cartesian(validOptions.map(opt => opt.values));

    form.variants = combinations.map(combination => {
        const name = combination.join(' / ');
        const existing = form.variants.find(v => v.name === name);
        
        const optionsObj = {};
        validOptions.forEach((opt, idx) => {
            optionsObj[opt.name] = combination[idx];
        });
        
        return existing || {
            name: name,
            sku: form.sku ? `${form.sku}-${combination.map(s => s.substring(0, 2).toUpperCase()).join('-')}` : '',
            price: form.price || '',
            stock: form.stock || 0,
            active: true,
            options: optionsObj
        };
    });
};

import { getImageUrl } from '@/Utils/image';

const getProductImageUrl = (path) => {
    return getImageUrl(path, 300, 300);
};
</script>

<template>
    <AdminLayout>
        <Head title="Edit Product" />
        
        <template #header>
             <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Admin', href: route('admin.dashboard') },
                            { label: 'Products', href: route('admin.products.index') },
                            { label: 'Edit Product' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">Edit Product: {{ product.name }}</h1>
                </div>
                <div>
                     <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        Preview
                    </button>
                </div>
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
                                <InputLabel for="name" value="Product Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    placeholder="e.g. Wireless Noise Cancelling Headphones"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />
                                <div class="mt-1 rounded-md border border-gray-300 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                                    <!-- Simple Toolbar Mockup -->
                                    <div class="flex gap-2 border-b border-gray-200 bg-gray-50 px-3 py-2">
                                        <button type="button" class="p-1 text-gray-500 hover:text-gray-700 font-bold">B</button>
                                        <button type="button" class="p-1 text-gray-500 hover:text-gray-700 italic">I</button>
                                        <button type="button" class="p-1 text-gray-500 hover:text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                        </button>
                                        <button type="button" class="p-1 text-gray-500 hover:text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </button>
                                    </div>
                                    <textarea
                                        id="description"
                                        class="block w-full border-0 bg-transparent p-3 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                        rows="6"
                                        v-model="form.description"
                                        placeholder="Describe your product benefits and features..."
                                    ></textarea>
                                </div>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="category" value="Category" />
                                <select
                                    id="category"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="" disabled>Select a category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category_id" />
                            </div>
                        </div>
                    </div>

                    <!-- Product Variants -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Product Variants</h2>
                        
                        <div class="space-y-6">
                            <!-- Options List -->
                            <div v-for="(option, index) in form.options" :key="index" class="rounded-lg border border-gray-200 p-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <InputLabel value="Option Name" class="mb-1" />
                                        <TextInput
                                            v-model="option.name"
                                            placeholder="e.g. Color"
                                            class="block w-full"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Option Values" class="mb-1" />
                                        <div class="flex flex-wrap gap-2 rounded-md border border-gray-300 bg-white p-2 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                                            <span v-for="(value, vIndex) in option.values" :key="vIndex" class="inline-flex items-center rounded bg-blue-100 px-2 py-1 text-sm font-medium text-blue-800">
                                                {{ value }}
                                                <button type="button" @click="removeOptionValue(index, vIndex)" class="ml-1 text-blue-600 hover:text-blue-800">
                                                    &times;
                                                </button>
                                            </span>
                                            <input
                                                type="text"
                                                class="min-w-[100px] flex-1 border-none bg-transparent p-0 text-sm focus:ring-0"
                                                placeholder="Type & Enter"
                                                @keydown.enter.prevent="addOptionValue(index, $event)"
                                                @blur="addOptionValue(index, $event)"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button type="button" @click="removeOption(index)" class="text-sm text-red-600 hover:text-red-800">
                                        Remove Option
                                    </button>
                                </div>
                            </div>

                            <!-- Add Option Button -->
                            <button type="button" @click="addOption" class="flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add another option
                            </button>

                            <!-- Generated Variants Table -->
                            <div v-if="form.variants.length > 0" class="mt-8">
                                <h3 class="mb-4 text-sm font-medium text-gray-900">Generated Variants</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Active</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Variant</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">SKU</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Price ($)</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            <tr v-for="(variant, index) in form.variants" :key="index">
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" v-model="variant.active" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ variant.name }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <TextInput v-model="variant.sku" class="w-full text-xs" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <TextInput v-model="variant.price" type="number" step="0.01" class="w-full text-xs" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <TextInput v-model="variant.stock" type="number" class="w-full text-xs" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Pricing</h2>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="price" value="Base Price ($)" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    placeholder="0.00"
                                />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <InputLabel for="sale_price" value="Sale Price ($)" />
                                <TextInput
                                    id="sale_price"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.sale_price"
                                    placeholder="0.00"
                                />
                                <InputError class="mt-2" :message="form.errors.sale_price" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="space-y-8">
                    
                    <!-- Product Media -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Product Media</h2>
                        
                        <div class="mb-4 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center hover:bg-gray-100 transition-colors cursor-pointer relative">
                             <input type="file" @change="handleImageChange" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                            <div class="flex flex-col items-center">
                                <div class="mb-3 rounded-full bg-blue-100 p-3 text-blue-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900">Click to upload or drag & drop</span>
                                <span class="mt-1 text-xs text-gray-500">PNG, JPG or WebP (max. 5MB)</span>
                            </div>
                        </div>
                        
                         <!-- Preview Thumbnails -->
                         <div v-if="product.image_path || form.image" class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Current Image</p>
                            <div class="aspect-square w-full rounded-lg bg-gray-100 overflow-hidden">
                                <img 
                                    v-if="form.image"
                                    :src="URL.createObjectURL(form.image)" 
                                    class="h-full w-full object-cover" 
                                />
                                <img 
                                    v-else-if="product.image_path"
                                    :src="getProductImageUrl(product.image_path)" 
                                    class="h-full w-full object-cover" 
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Inventory & Shipping -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Inventory & Shipping</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="sku" value="SKU" />
                                <TextInput
                                    id="sku"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.sku"
                                    placeholder="SW-HDP-001"
                                />
                                <InputError class="mt-2" :message="form.errors.sku" />
                            </div>

                            <div>
                                <InputLabel for="stock" value="Stock Quantity" />
                                <TextInput
                                    id="stock"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.stock"
                                    placeholder="0"
                                />
                                <InputError class="mt-2" :message="form.errors.stock" />
                            </div>

                            <div>
                                <InputLabel for="weight" value="Weight (kg)" />
                                <TextInput
                                    id="weight"
                                    type="number"
                                    step="0.1"
                                    class="mt-1 block w-full"
                                    v-model="form.weight"
                                    placeholder="0.5"
                                />
                                <InputError class="mt-2" :message="form.errors.weight" />
                            </div>
                            
                            <div>
                                <InputLabel for="status" value="Status" />
                                <div class="mt-2 flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="form.status = !form.status"
                                        :class="form.status ? 'bg-green-600' : 'bg-gray-200'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                                    >
                                        <span class="sr-only">Use setting</span>
                                        <span 
                                            :class="form.status ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        ></span>
                                    </button>
                                    <span class="text-sm text-gray-900">{{ form.status ? 'Active' : 'Draft' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Bar -->
            <div class="mt-8 flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                 <Link :href="route('admin.products.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Discard Changes
                </Link>
                <div class="flex gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Update Product
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
