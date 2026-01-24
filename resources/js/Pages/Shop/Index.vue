<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

// State for filters
const selectedCategory = ref(props.filters.category || null);
const priceRange = ref([0, 350]); // Mock range
const selectedSizes = ref([]);
const selectedColors = ref([]);

// Mock Data for UI (since backend doesn't support these yet)
const sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL'];
const colors = [
    { name: 'Black', class: 'bg-black' },
    { name: 'White', class: 'bg-white border border-gray-200' },
    { name: 'Blue', class: 'bg-blue-500' },
    { name: 'Red', class: 'bg-red-500' },
    { name: 'Green', class: 'bg-green-500' },
    { name: 'Yellow', class: 'bg-yellow-400' },
];

// Helper to get product image
const getProductImage = (product) => {
    if (product.image_path) {
        return '/storage/' + product.image_path;
    }
    const fallbacks = [
        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=500&q=80',
        'https://images.unsplash.com/photo-1556906781-9a412961d28c?auto=format&fit=crop&w=500&q=80',
        'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80',
        'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=500&q=80',
    ];
    return fallbacks[product.id % fallbacks.length];
};

// Filter Logic
const updateFilters = () => {
    router.get(route('shop.index'), {
        category: selectedCategory.value,
        search: props.filters.search,
        // price: priceRange.value, // TODO: Implement backend
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const toggleCategory = (slug) => {
    if (selectedCategory.value === slug) {
        selectedCategory.value = null;
    } else {
        selectedCategory.value = slug;
    }
    updateFilters();
};

const clearFilters = () => {
    selectedCategory.value = null;
    updateFilters();
};

// Stars helper
const getStars = (id) => {
    // Deterministic random stars based on ID
    const rating = 3 + (id % 3); // 3 to 5
    return rating;
};
const getReviewCount = (id) => (id * 13) % 200 + 10;

</script>

<template>
    <ShopLayout>
        <Head title="Shop" />

        <div class="bg-gray-50/50 min-h-screen pb-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
                
                <!-- Breadcrumb -->
                <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('shop.index')" class="hover:text-blue-600 transition-colors">
                                Home
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 font-medium text-gray-900 md:ml-2">Fashion</span>
                            </div>
                        </li>
                         <li v-if="selectedCategory">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 font-medium text-gray-900 md:ml-2 capitalize">{{ props.categories.find(c => c.slug === selectedCategory)?.name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                     <!-- Mobile Filter Toggle (Visible only on mobile) -->
                     <div class="sm:hidden mb-4">
                        <button class="flex items-center gap-2 text-gray-700 font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            Filters
                        </button>
                     </div>

                    <div class="text-gray-500">
                        Showing <span class="font-medium text-gray-900">{{ products.from }}-{{ products.to }}</span> of <span class="font-medium text-gray-900">{{ products.total }}</span> results
                    </div>
                    
                    <div class="flex items-center gap-4 mt-4 sm:mt-0">
                        <div class="relative inline-block text-left">
                            <button type="button" class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900" id="menu-button" aria-expanded="false" aria-haspopup="true">
                                Sort by: Newest Arrivals
                                <svg class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
                    <!-- Filters Sidebar -->
                    <div class="hidden lg:block space-y-10 bg-white p-6 rounded-lg shadow-sm">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Filters</h3>
                            <button @click="clearFilters" class="text-sm text-blue-600 hover:text-blue-500 font-medium">Clear All</button>
                        </div>

                        <!-- Categories -->
                        <div>
                            <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-4 text-sm uppercase tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path fill-rule="evenodd" d="M2 3.75A.75.75 0 0 1 2.75 3h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 3.75Zm0 4.167a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Zm0 4.166a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Zm0 4.167a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                                </svg>
                                Category
                            </h4>
                            <div class="space-y-3">
                                <div v-for="category in categories" :key="category.id" class="flex items-center">
                                    <input
                                        :id="`category-${category.id}`"
                                        :value="category.slug"
                                        :checked="selectedCategory === category.slug"
                                        @change="toggleCategory(category.slug)"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <label :for="`category-${category.id}`" class="ml-3 text-sm text-gray-600 hover:text-gray-900 cursor-pointer">
                                        {{ category.name }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-4 text-sm uppercase tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path d="M10 2a.75.75 0 0 1 .75.75v1.5h1.5a.75.75 0 0 1 0 1.5h-1.5v2h1.5a.75.75 0 0 1 0 1.5h-1.5v2h1.5a.75.75 0 0 1 0 1.5h-1.5v1.5a.75.75 0 0 1-1.5 0v-1.5h-2v1.5a.75.75 0 0 1-1.5 0v-1.5h-1.5a.75.75 0 0 1 0-1.5h1.5v-2h-1.5a.75.75 0 0 1 0-1.5h1.5v-2h-1.5a.75.75 0 0 1 0-1.5h1.5v-1.5A.75.75 0 0 1 10 2Zm-2 4v2h2V6H8Zm0 4v2h2v-2H8Z" />
                                </svg>
                                Price Range
                            </h4>
                            <div class="space-y-4">
                                <input 
                                    type="range" 
                                    min="0" 
                                    max="500" 
                                    step="10" 
                                    v-model="priceRange[1]" 
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                >
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>${{ priceRange[0] }}</span>
                                    <span>${{ priceRange[1] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div>
                            <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-4 text-sm uppercase tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                                </svg>
                                Size
                            </h4>
                            <div class="grid grid-cols-4 gap-2">
                                <button 
                                    v-for="size in sizes" 
                                    :key="size"
                                    class="flex items-center justify-center py-2 text-sm font-medium border rounded-md hover:border-blue-600 hover:text-blue-600 transition-colors"
                                    :class="selectedSizes.includes(size) ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-700'"
                                >
                                    {{ size }}
                                </button>
                            </div>
                        </div>

                        <!-- Color -->
                        <div>
                            <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-4 text-sm uppercase tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16ZM6.5 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM13.5 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM8 13.5A1.5 1.5 0 1 1 5 13.5 1.5 1.5 0 0 1 8 13.5Zm7 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" clip-rule="evenodd" />
                                </svg>
                                Color
                            </h4>
                            <div class="flex flex-wrap gap-3">
                                <button 
                                    v-for="color in colors" 
                                    :key="color.name"
                                    class="h-8 w-8 rounded-full shadow-sm ring-2 ring-offset-2 transition-all hover:scale-110"
                                    :class="[color.class, selectedColors.includes(color.name) ? 'ring-blue-600' : 'ring-transparent']"
                                    :title="color.name"
                                ></button>
                            </div>
                        </div>

                        <button class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-blue-700 transition-colors">
                            Apply Filters
                        </button>
                    </div>

                    <!-- Product Grid -->
                    <div class="lg:col-span-3">
                         <div v-if="products.data.length > 0" class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
                            <div v-for="product in products.data" :key="product.id" class="group relative flex flex-col bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                                <!-- Image & Badges -->
                                <div class="aspect-[4/5] w-full overflow-hidden rounded-xl bg-gray-100 relative mb-4">
                                    <img :src="getProductImage(product)" :alt="product.name" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500" />
                                    
                                    <!-- Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-2">
                                        <span v-if="product.sale_price" class="inline-flex items-center rounded-md bg-red-500 px-2 py-1 text-xs font-bold text-white uppercase tracking-wide shadow-sm">
                                            Sale
                                        </span>
                                        <span v-else-if="product.id % 2 === 0" class="inline-flex items-center rounded-md bg-blue-500 px-2 py-1 text-xs font-bold text-white uppercase tracking-wide shadow-sm">
                                            New
                                        </span>
                                    </div>

                                    <!-- Wishlist -->
                                    <button class="absolute top-3 right-3 p-2 rounded-full bg-white/90 text-gray-400 hover:text-red-500 shadow-sm hover:bg-white transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 flex flex-col">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                                        {{ product.category?.name || 'Collection' }}
                                    </p>
                                    <h3 class="text-base font-bold text-gray-900 mb-1 leading-tight group-hover:text-blue-600 transition-colors">
                                        <Link :href="route('shop.show', product.slug)">
                                            <span aria-hidden="true" class="absolute inset-0" />
                                            {{ product.name }}
                                        </Link>
                                    </h3>
                                    
                                    <!-- Rating -->
                                    <div class="flex items-center mb-3">
                                        <div class="flex items-center text-yellow-400">
                                            <svg v-for="i in 5" :key="i" class="h-4 w-4 flex-shrink-0" :class="i <= getStars(product.id) ? 'fill-current' : 'text-gray-200 fill-current'" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="ml-2 text-xs text-gray-400">({{ getReviewCount(product.id) }})</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="mt-auto flex items-center justify-between">
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-lg font-bold text-blue-600">${{ product.sale_price || product.price }}</span>
                                            <span v-if="product.sale_price" class="text-sm text-gray-400 line-through">${{ product.price }}</span>
                                        </div>
                                        <button class="rounded-full p-2 bg-gray-50 text-gray-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                             <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                            <div class="mt-6">
                                <button @click="clearFilters" type="button" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    Clear all filters
                                </button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="products.links.length > 3" class="mt-12 flex justify-center">
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <template v-for="(link, index) in products.links" :key="index">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold transition-colors focus:z-20 focus:outline-offset-0"
                                        :class="[
                                            link.active 
                                                ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' 
                                                : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === products.links.length - 1 ? 'rounded-r-md' : ''
                                        ]"
                                        v-html="link.label"
                                    />
                                    <span 
                                        v-else 
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 opacity-50 cursor-not-allowed"
                                        :class="[
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === products.links.length - 1 ? 'rounded-r-md' : ''
                                        ]"
                                        v-html="link.label"
                                    ></span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
