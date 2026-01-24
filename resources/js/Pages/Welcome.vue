<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    trendingProducts: Array
});

// Mock Categories
const categories = [
    { name: 'Streetwear', image: 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?auto=format&fit=crop&w=300&q=80', slug: 'streetwear' },
    { name: 'Minimalist', image: 'https://images.unsplash.com/photo-1551488852-18004107e881?auto=format&fit=crop&w=300&q=80', slug: 'minimalist' },
    { name: 'Active', image: 'https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&fit=crop&w=300&q=80', slug: 'active' },
    { name: 'Accessories', image: 'https://images.unsplash.com/photo-1523779917675-b6ed3a42a561?auto=format&fit=crop&w=300&q=80', slug: 'accessories' },
    { name: 'Shoes', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80', slug: 'shoes' },
    { name: 'Jewelry', image: 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=300&q=80', slug: 'jewelry' },
    { name: 'Eyewear', image: 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=300&q=80', slug: 'eyewear' },
];

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
</script>

<template>
    <Head title="Welcome" />
    <ShopLayout>
        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-3xl bg-amber-500 text-white shadow-xl mb-16">
             <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d?q=80&w=2076&auto=format&fit=crop')] bg-cover bg-center mix-blend-overlay opacity-50"></div>
             <div class="absolute inset-0 bg-gradient-to-r from-amber-600/90 to-transparent"></div>
             
             <div class="relative z-10 px-8 py-16 sm:px-16 sm:py-24 lg:py-32 max-w-2xl">
                <span class="inline-block px-3 py-1 mb-6 text-xs font-bold tracking-wider text-white uppercase bg-blue-600 rounded-sm">Summer '24</span>
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl mb-6">
                    The New Wave <br/> is Here.
                </h1>
                <p class="text-lg text-white/90 mb-8 max-w-lg">
                    Discover our limited edition collection and redefine your summer style with modern essentials.
                </p>
                <div class="flex flex-wrap gap-4">
                    <Link :href="route('shop.index')" class="px-8 py-3 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/30">
                        Shop Collection
                    </Link>
                    <Link :href="route('shop.index')" class="px-8 py-3 text-sm font-bold text-white bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-colors border border-white/30">
                        View Lookbook
                    </Link>
                </div>
             </div>
        </div>

        <!-- Browse by Category -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Browse by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-6">
                <Link :href="route('shop.index', { category: cat.slug })" v-for="cat in categories" :key="cat.slug" class="group flex flex-col items-center">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden mb-3 border-2 border-transparent group-hover:border-blue-600 transition-all p-1">
                        <div class="w-full h-full rounded-full overflow-hidden bg-gray-100">
                             <img :src="cat.image" :alt="cat.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ cat.name }}</span>
                </Link>
            </div>
        </div>

        <!-- Trending Now -->
        <div class="mb-16">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Trending Now</h2>
                    <p class="text-gray-500 mt-1">Our most loved pieces this week.</p>
                </div>
                <Link :href="route('shop.index')" class="text-blue-600 font-bold hover:text-blue-700 flex items-center gap-1 text-sm">
                    View All
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </Link>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                 <div v-for="product in trendingProducts" :key="product.id" class="group relative flex flex-col bg-white">
                    <div class="aspect-[4/5] w-full overflow-hidden rounded-xl bg-gray-100 relative mb-4">
                        <img :src="getProductImage(product)" :alt="product.name" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500" />
                        
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            <span v-if="product.sale_price" class="inline-flex items-center rounded-sm bg-red-500 px-2 py-1 text-[10px] font-bold text-white uppercase tracking-wide">
                                -20%
                            </span>
                            <span v-else class="inline-flex items-center rounded-sm bg-white/90 px-2 py-1 text-[10px] font-bold text-gray-900 uppercase tracking-wide">
                                NEW ARRIVAL
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <h3 class="text-base font-bold text-gray-900 mb-1 leading-tight group-hover:text-blue-600 transition-colors">
                            <Link :href="route('shop.show', product.slug)">
                                <span aria-hidden="true" class="absolute inset-0" />
                                {{ product.name }}
                            </Link>
                        </h3>
                        <p class="text-sm text-gray-500 mb-2">
                            {{ product.category?.name || 'Essential Collection' }}
                        </p>
                        
                        <div class="mt-auto flex items-baseline gap-2">
                            <span class="text-base font-bold text-blue-600">${{ product.sale_price || product.price }}</span>
                            <span v-if="product.sale_price" class="text-xs text-gray-400 line-through">${{ product.price }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="relative overflow-hidden rounded-2xl bg-blue-100 p-8 sm:p-12">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                <div class="max-w-xl">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Catch the Wave</h2>
                    <p class="text-gray-600">Join our newsletter for early access to drops and exclusive offers.</p>
                </div>
                <div class="w-full max-w-md">
                    <form class="flex gap-2">
                        <input type="email" placeholder="Your email address" class="w-full px-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-blue-600 placeholder-gray-400" />
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <!-- Decor -->
             <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-blue-200/50 blur-3xl"></div>
             <div class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-blue-200/50 blur-3xl"></div>
        </div>

    </ShopLayout>
</template>
