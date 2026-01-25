<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { getImageUrl } from '@/Utils/image';
import ProductCard from '@/Components/Shop/ProductCard.vue';

const props = defineProps({
    trendingProducts: Array,
    banners: Array,
    categories: Array,
});

// Banner Logic
const currentBannerIndex = ref(0);
let bannerTimeout = null;

const nextBanner = () => {
    if (props.banners && props.banners.length > 0) {
        currentBannerIndex.value = (currentBannerIndex.value + 1) % props.banners.length;
        scheduleNext();
    }
};

const scheduleNext = () => {
    stopRotation();
    if (props.banners && props.banners.length > 1) {
        const currentBanner = props.banners[currentBannerIndex.value];
        const duration = currentBanner.duration || 5000;
        bannerTimeout = setTimeout(nextBanner, duration);
    }
};

const startRotation = () => {
    scheduleNext();
};

const stopRotation = () => {
    if (bannerTimeout) clearTimeout(bannerTimeout);
};

onMounted(() => {
    startRotation();
});

onUnmounted(() => {
    stopRotation();
});

const getBannerImage = (path) => {
     return getImageUrl(path, 1920, 600);
};

const getCategoryImage = (path) => {
    return getImageUrl(path, 300, 300);
};


</script>

<template>
    <Head title="Welcome" />
    <ShopLayout>
        <!-- Hero Section -->
        <div v-if="banners && banners.length > 0" class="relative overflow-hidden rounded-3xl bg-gray-900 text-white shadow-xl mb-16 h-[500px]">
            <div 
                v-for="(banner, index) in banners" 
                :key="banner.id"
                class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                :class="{ 'opacity-100 z-10': index === currentBannerIndex, 'opacity-0 z-0': index !== currentBannerIndex }"
            >
                <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-60" :style="`background-image: url('${getBannerImage(banner.image_path)}')`"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 to-transparent"></div>
                
                <div class="relative z-20 px-8 py-16 sm:px-16 sm:py-24 lg:py-32 max-w-2xl h-full flex flex-col justify-center">
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl mb-6 leading-tight">
                        {{ banner.title }}
                    </h1>
                    <p v-if="banner.description" class="text-lg text-white/90 mb-8 max-w-lg">
                        {{ banner.description }}
                    </p>
                    <div v-if="banner.link" class="flex flex-wrap gap-4">
                        <a :href="banner.link" class="px-8 py-3 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/30">
                            View More
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Indicators -->
            <div v-if="banners.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2">
                <button 
                    v-for="(_, index) in banners" 
                    :key="index"
                    @click="currentBannerIndex = index; stopRotation(); startRotation();"
                    class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                    :class="index === currentBannerIndex ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/80'"
                ></button>
            </div>
        </div>

        <div v-else class="relative overflow-hidden rounded-3xl bg-amber-500 text-white shadow-xl mb-16">
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
                             <img :src="getCategoryImage(cat.image_path)" :alt="cat.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
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
                 <ProductCard 
                    v-for="product in trendingProducts" 
                    :key="product.id" 
                    :product="product" 
                />
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