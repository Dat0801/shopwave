<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import CategoryFilter from '@/Components/Shop/CategoryFilter.vue';
import ProductCard from '@/Components/Shop/ProductCard.vue';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

// State for filters
const selectedCategory = ref(props.filters.category || null);
const selectedSort = ref(props.filters.sort || 'newest');
const minPrice = ref(props.filters.min_price || '');
const maxPrice = ref(props.filters.max_price || '');
const selectedSizes = ref(props.filters.size ? (Array.isArray(props.filters.size) ? props.filters.size : [props.filters.size]) : []);

// Mock Data for UI (since backend doesn't support these yet)
const sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '40', '41', '42', '43', '44'];
const colors = [
    { name: 'Black', class: 'bg-black' },
    { name: 'White', class: 'bg-white border border-gray-200' },
    { name: 'Blue', class: 'bg-blue-500' },
    { name: 'Red', class: 'bg-red-500' },
    { name: 'Green', class: 'bg-green-500' },
    { name: 'Yellow', class: 'bg-yellow-400' },
];



// Filter Logic
const updateFilters = () => {
    router.get(route('shop.index'), {
        category: selectedCategory.value,
        search: props.filters.search,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        size: selectedSizes.value,
        color: selectedColors.value,
        sort: selectedSort.value,
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
    minPrice.value = '';
    maxPrice.value = '';
    selectedSizes.value = [];
    updateFilters();
};



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
                            <CategoryFilter 
                                :categories="categories"
                                :selected-category="selectedCategory"
                                @toggle="toggleCategory"
                            />
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
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-500">Min</label>
                                        <input 
                                            type="number" 
                                            v-model="minPrice" 
                                            placeholder="0"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Max</label>
                                        <input 
                                            type="number" 
                                            v-model="maxPrice" 
                                            placeholder="1000"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Size Filter -->
                        <div>
                            <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-4 text-sm uppercase tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v14.5a.75.75 0 0 1-1.5 0V2.75A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                                  <path fill-rule="evenodd" d="M2.75 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H3.5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                                </svg>
                                Size
                            </h4>
                            <div class="grid grid-cols-3 gap-2">
                                <label v-for="size in sizes" :key="size" class="flex items-center justify-center p-2 border rounded-md cursor-pointer hover:border-blue-500 transition-colors" :class="selectedSizes.includes(size) ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-gray-200'">
                                    <input type="checkbox" :value="size" v-model="selectedSizes" class="sr-only">
                                    <span class="text-sm font-medium">{{ size }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Color Filter -->
                        <div>
                            <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-4 text-sm uppercase tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm0-1.5a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13Z" clip-rule="evenodd" />
                                </svg>
                                Color
                            </h4>
                            <div class="flex flex-wrap gap-3">
                                <label v-for="color in colors" :key="color.name" class="relative cursor-pointer group">
                                    <input type="checkbox" :value="color.name" v-model="selectedColors" class="sr-only">
                                    <div class="w-8 h-8 rounded-full shadow-sm transition-transform group-hover:scale-110" :class="[color.class, selectedColors.includes(color.name) ? 'ring-2 ring-offset-2 ring-blue-500' : '']"></div>
                                    <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs text-gray-600 opacity-0 group-hover:opacity-100 whitespace-nowrap bg-white px-1 rounded shadow-sm">{{ color.name }}</span>
                                </label>
                            </div>
                        </div>

                        <button @click="updateFilters" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-blue-700 transition-colors">
                            Apply Filters
                        </button>
                    </div>

                    <!-- Product Grid -->
                    <div class="lg:col-span-3">
                         <div v-if="products.data.length > 0" class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
                            <ProductCard 
                                v-for="product in products.data" 
                                :key="product.id" 
                                :product="product" 
                            />
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
