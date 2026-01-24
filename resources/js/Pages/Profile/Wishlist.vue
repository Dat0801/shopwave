<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    wishlistItems: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || 'All Items');

const categories = ['All Items', 'Clothing', 'Accessories', 'Shoes']; // Mock categories for filter tabs

const moveAllForm = useForm({});
const addToCartForm = useForm({
    quantity: 1,
});

const moveAllToCart = () => {
    moveAllForm.post(route('wishlist.move-all'), {
        preserveScroll: true,
    });
};

const addToCart = (product) => {
    addToCartForm.post(route('cart.store', product.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Remove from wishlist if needed
        }
    });
};

const removeFromWishlist = (product) => {
    if (confirm('Remove this item from your wishlist?')) {
        router.delete(route('wishlist.destroy', product.id), {
            preserveScroll: true,
        });
    }
};

const handleSearch = () => {
    router.get(route('wishlist.index'), {
        search: search.value,
        category: category.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const setCategory = (cat) => {
    category.value = cat;
    handleSearch();
};

watch(search, (value) => {
    // Debounce search could be added here
});

</script>

<template>
    <Head title="My Wishlist" />

    <ProfileLayout>
        <template #breadcrumb>
            <span class="text-gray-900 font-medium">Wishlist</span>
        </template>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
                <p class="mt-1 text-gray-500">You have {{ wishlistItems.total }} items saved in your wishlist</p>
            </div>
            <button 
                @click="moveAllToCart"
                :disabled="wishlistItems.data.length === 0"
                class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" />
                </svg>
                Move All to Cart
            </button>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-2">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    v-model="search"
                    @keydown.enter="handleSearch"
                    type="text"
                    placeholder="Search within wishlist..."
                    class="block w-full rounded-xl border-0 bg-transparent py-2.5 pl-10 pr-3 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                />
            </div>
            <div class="flex items-center gap-1 border-t md:border-t-0 md:border-l border-gray-100 p-1 md:pl-2 overflow-x-auto">
                <button
                    v-for="cat in categories"
                    :key="cat"
                    @click="setCategory(cat)"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg whitespace-nowrap transition-colors"
                    :class="category === cat ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                >
                    {{ cat }}
                </button>
            </div>
        </div>

        <!-- Product Grid -->
        <div v-if="wishlistItems.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="item in wishlistItems.data" :key="item.id" class="group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Remove Button -->
                <button 
                    @click="removeFromWishlist(item.product)"
                    class="absolute top-3 right-3 z-10 p-1.5 bg-white/80 backdrop-blur-sm rounded-full text-gray-400 hover:text-red-500 hover:bg-white transition-all shadow-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>

                <!-- Image -->
                <div class="aspect-square bg-gray-100 relative overflow-hidden">
                    <img 
                        v-if="item.product.image_path"
                        :src="item.product.image_path" 
                        :alt="item.product.name" 
                        class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                        <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <!-- Badges -->
                    <div class="mb-2 flex items-center gap-2">
                        <span 
                            v-if="item.product.stock > 10" 
                            class="inline-flex items-center rounded-sm bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                        >
                            IN STOCK
                        </span>
                        <span 
                            v-else-if="item.product.stock > 0" 
                            class="inline-flex items-center rounded-sm bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20"
                        >
                            LOW STOCK
                        </span>
                        <span 
                            v-else
                            class="inline-flex items-center rounded-sm bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20"
                        >
                            OUT OF STOCK
                        </span>
                    </div>

                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                        {{ item.product.category?.name || 'Uncategorized' }}
                    </p>
                    
                    <h3 class="text-base font-bold text-gray-900 mb-1 truncate">
                        {{ item.product.name }}
                    </h3>
                    
                    <p class="text-lg font-bold text-blue-600 mb-4">
                        ${{ Number(item.product.sale_price || item.product.price).toFixed(2) }}
                    </p>

                    <button 
                        @click="addToCart(item.product)"
                        :disabled="item.product.stock <= 0"
                        class="w-full bg-gray-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" />
                        </svg>
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-white rounded-2xl border border-gray-100">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 mb-4">
                <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Your wishlist is empty</h3>
            <p class="mt-1 text-gray-500 max-w-sm mx-auto">Items you save will appear here. Start shopping to find items you love!</p>
            <div class="mt-6">
                <Link :href="route('shop.index')" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Start Shopping
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="wishlistItems.data.length > 0 && wishlistItems.links.length > 3" class="mt-8 flex justify-center">
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <template v-for="link in wishlistItems.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus:outline-offset-0"
                        :class="[
                            link.active ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0',
                            'first:rounded-l-md last:rounded-r-md'
                        ]"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 first:rounded-l-md last:rounded-r-md bg-gray-50"
                        v-html="link.label"
                    ></span>
                </template>
            </nav>
        </div>
    </ProfileLayout>
</template>
