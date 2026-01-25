<script setup>
import { Link } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    product: Object,
});

const getProductImage = (product) => {
    return getImageUrl(product.image_path, 500, 600);
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
    <div class="group relative flex flex-col bg-white rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
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
</template>
