<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { getImageUrl } from '@/Utils/image';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ProductCard from '@/Components/Shop/ProductCard.vue';

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    reviewStats: Object,
    availableOptions: Object,
    isInWishlist: Boolean,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const showReviewForm = ref(false);
const isInWishlist = ref(props.isInWishlist);

const form = useForm({
    quantity: 1,
    color: null,
    size: null,
});

const wishlistForm = useForm({});

const price = computed(() => props.product.sale_price || props.product.price);
const discountPercentage = computed(() => {
    if (!props.product.sale_price) return 0;
    return Math.round(((props.product.price - props.product.sale_price) / props.product.price) * 100);
});

// Get colors and sizes from product variants
const colors = computed(() => {
    if (!props.availableOptions?.colors?.length) {
        return [{ name: 'Default', value: 'default', class: 'bg-gray-400' }];
    }
    
    const colorMap = {
        'Black': 'bg-gray-900',
        'White': 'bg-white border-gray-300',
        'Red': 'bg-red-600',
        'Blue': 'bg-blue-600',
        'Green': 'bg-green-600',
        'Yellow': 'bg-yellow-400',
        'Grey': 'bg-gray-400',
        'Gray': 'bg-gray-400',
        'Indigo': 'bg-blue-700',
        'Light Blue': 'bg-blue-200',
    };
    
    return props.availableOptions.colors.map(color => ({
        name: color,
        value: color.toLowerCase().replace(/\s+/g, '-'),
        class: colorMap[color] || 'bg-gray-400'
    }));
});

const sizes = computed(() => {
    if (!props.availableOptions?.sizes?.length) {
        return ['M'];
    }
    return props.availableOptions.sizes;
});

const features = computed(() => {
    // Extract features from description or use defaults
    return [
        '100% Cotton Premium Heavyweight Denim',
        'Adjustable button waist tabs',
        'Two chest pockets and two side welt pockets',
        'Pre-washed for a soft, broken-in feel',
        'Ethically sourced and manufactured',
    ];
});

const reviews = computed(() => props.product.reviews || []);

const totalReviews = computed(() => props.reviewStats?.total || 0);

const averageRating = computed(() => props.reviewStats?.average || 0);

const averageRatingRounded = computed(() => Math.round(averageRating.value));

const ratingCounts = computed(() => props.reviewStats?.distribution || { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 });

const ratingPercentages = computed(() => {
    if (totalReviews.value === 0) return { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
    const percentages = {};
    for (let i = 1; i <= 5; i++) {
        percentages[i] = Math.round((ratingCounts.value[i] / totalReviews.value) * 100);
    }
    return percentages;
});

const reviewForm = useForm({
    rating: 5,
    comment: '',
});

// State
const selectedColor = ref(colors[0]);
const selectedSize = ref('M');
const activeTab = ref('description');
const currentImage = ref(0);

const getProductImageUrl = (path) => {
    return getImageUrl(path, 600, 600);
};

// Mock Images (using placeholder if no real images)
const images = computed(() => [
    props.product.image_path,
    null,
    null,
    null
]);

const addToCart = () => {
    form.transform((data) => ({
        ...data,
        color: selectedColor.value?.value,
        size: selectedSize.value,
    })).post(route('cart.store', props.product.id), {
        preserveScroll: true,
    });
};

const toggleWishlist = () => {
    if (!user.value) {
        window.location.href = route('login');
        return;
    }

    if (isInWishlist.value) {
        wishlistForm.delete(route('wishlist.destroy', props.product.id), {
            preserveScroll: true,
            onSuccess: () => {
                isInWishlist.value = false;
            },
        });
    } else {
        wishlistForm.post(route('wishlist.store', props.product.id), {
            preserveScroll: true,
            onSuccess: () => {
                isInWishlist.value = true;
            },
        });
    }
};

const submitReview = () => {
    reviewForm.post(route('products.reviews.store', props.product.id), {
        preserveScroll: true,
        onSuccess: () => {
            reviewForm.reset('comment');
            reviewForm.rating = 5;
            showReviewForm.value = false;
        },
    });
};

const formatReviewDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};
</script>

<template>
    <ShopLayout>
        <Head :title="`${product.name} - Product`" />
        
        <!-- Breadcrumbs -->
        <nav class="mb-8 text-sm text-gray-500">
            <Link :href="route('shop.index')" class="hover:text-gray-900 transition-colors">
                Home
            </Link>
            <span class="mx-2">/</span>
            <Link :href="route('shop.index')" class="hover:text-gray-900 transition-colors">
                {{ product.category?.name || 'Men\'s Fashion' }}
            </Link>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">
                {{ product.name }}
            </span>
        </nav>

        <div class="grid gap-12 lg:grid-cols-2">
            <!-- Left Column: Images -->
            <div class="space-y-4">
                <div class="aspect-[4/5] w-full overflow-hidden rounded-lg bg-gray-100 relative group">
                    <div v-if="!images[currentImage]" class="absolute inset-0 flex items-center justify-center text-gray-400">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <!-- Assuming real images would be <img> tags -->
                    <img v-else :src="images[currentImage]" :alt="product.name" class="h-full w-full object-cover object-center" />
                </div>
                
                <div class="grid grid-cols-4 gap-4">
                    <button 
                        v-for="(img, index) in images" 
                        :key="index"
                        @click="currentImage = index"
                        class="aspect-square overflow-hidden rounded-lg bg-gray-100 ring-2 ring-offset-2 transition-all"
                        :class="currentImage === index ? 'ring-blue-600' : 'ring-transparent hover:ring-gray-300'"
                    >
                         <div v-if="!img" class="h-full w-full flex items-center justify-center text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <img v-else :src="img" class="h-full w-full object-cover object-center" />
                    </button>
                </div>
            </div>

            <!-- Right Column: Product Info -->
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-blue-600">
                    Heritage Collection
                </p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    {{ product.name }}
                </h1>

                <!-- Reviews -->
                <div class="mt-4 flex items-center">
                    <div class="flex items-center text-blue-500">
                        <svg v-for="i in 5" :key="i" class="h-5 w-5 flex-shrink-0" :class="i <= averageRatingRounded ? 'fill-current' : 'text-gray-300 fill-current'" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-500">{{ totalReviews > 0 ? averageRating.toFixed(1) : 'No ratings' }}</span>
                    <span class="ml-2 text-sm text-gray-400">|</span>
                    <a href="#reviews" class="ml-2 text-sm text-gray-500 hover:text-blue-600 underline decoration-gray-300 underline-offset-2">{{ totalReviews }} Reviews</a>
                </div>

                <!-- Price -->
                <div class="mt-6">
                    <div class="flex items-center gap-4">
                        <p class="text-3xl font-bold text-gray-900">${{ price }}</p>
                        <p v-if="product.sale_price" class="text-xl text-gray-400 line-through">${{ product.price }}</p>
                        <span v-if="product.sale_price" class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                            {{ discountPercentage }}% OFF
                        </span>
                    </div>
                    <p class="mt-2 flex items-center text-sm text-green-600">
                        <svg class="mr-1.5 h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                    </p>
                </div>

                <div class="mt-8 border-t border-gray-100 pt-8">
                    <!-- Color Selection -->
                    <div v-if="colors.length > 1 || (colors.length === 1 && colors[0].value !== 'default')">
                        <h3 class="text-sm font-medium text-gray-900 uppercase tracking-wide">Color: <span class="text-gray-500 normal-case">{{ selectedColor?.name || 'Select' }}</span></h3>
                        <div class="mt-3 flex items-center space-x-3">
                            <button
                                v-for="color in colors"
                                :key="color.value"
                                @click="selectedColor = color"
                                :class="[
                                    color.class,
                                    selectedColor?.value === color.value ? 'ring-2 ring-offset-2 ring-blue-600' : 'ring-1 ring-transparent hover:ring-gray-300',
                                    'h-8 w-8 rounded-full border border-black/10 focus:outline-none'
                                ]"
                                :aria-label="color.name"
                            />
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div v-if="sizes.length > 0" :class="colors.length > 1 ? 'mt-6' : ''">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900 uppercase tracking-wide">Select Size</h3>
                            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">Size Guide</a>
                        </div>
                        <div class="mt-3 grid grid-cols-4 gap-3 sm:grid-cols-6">
                            <button
                                v-for="size in sizes"
                                :key="size"
                                @click="selectedSize = size"
                                :class="[
                                    selectedSize === size
                                        ? 'border-blue-600 bg-blue-50 text-blue-600'
                                        : 'border-gray-200 bg-white text-gray-900 hover:bg-gray-50',
                                    'flex items-center justify-center rounded-md border py-3 text-sm font-medium sm:flex-1 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2'
                                ]"
                            >
                                {{ size }}
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex flex-col gap-3">
                        <button
                            type="button"
                            @click="addToCart"
                            class="flex w-full items-center justify-center rounded-lg bg-blue-600 px-8 py-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to Bag
                        </button>
                        <button
                            type="button"
                            @click="toggleWishlist"
                            :class="[
                                isInWishlist 
                                    ? 'border-red-300 bg-red-50 text-red-600 hover:bg-red-100' 
                                    : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                                'flex w-full items-center justify-center rounded-lg border px-8 py-4 text-base font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors'
                            ]"
                        >
                            <svg 
                                :class="['mr-2 h-5 w-5', isInWishlist ? 'text-red-600' : 'text-gray-400']" 
                                :fill="isInWishlist ? 'currentColor' : 'none'"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                            {{ isInWishlist ? 'Remove from Wishlist' : 'Save to Wishlist' }}
                        </button>
                    </div>

                    <!-- Service Info -->
                    <div class="mt-8 space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Free Express Delivery</p>
                                <p class="text-sm text-gray-500">Free delivery on orders over $150</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">30-Day Returns</p>
                                <p class="text-sm text-gray-500">Easy, hassle-free returns</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mt-16 lg:mt-20">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        v-for="tab in ['Description', 'Specifications', 'Shipping & Sustainability']"
                        :key="tab"
                        @click="activeTab = tab.toLowerCase().split(' ')[0]"
                        :class="[
                            activeTab === tab.toLowerCase().split(' ')[0]
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                        ]"
                    >
                        {{ tab }}
                    </button>
                </nav>
            </div>

            <div class="mt-8 lg:grid lg:grid-cols-2 lg:gap-12">
                <div v-if="activeTab === 'description'">
                    <h3 class="text-lg font-medium text-gray-900">The Ultimate Wardrobe Staple</h3>
                    <div class="mt-4 space-y-4 text-base text-gray-600">
                        <p>
                            {{ product.description || 'Crafted from our signature premium denim, the ShopWave Classic Trucker Jacket is designed to be worn for decades. It features a tailored yet comfortable fit, reinforced stitching, and authentic metal hardware.' }}
                        </p>
                        <p>
                            Whether you're layering it over a hoodie for a casual weekend look or pairing it with chinos for a night out, this jacket offers unparalleled versatility and style.
                        </p>
                    </div>
                </div>

                <div v-if="activeTab === 'description'" class="mt-8 lg:mt-0">
                    <div class="rounded-2xl bg-gray-50 p-6 sm:p-8">
                        <div class="flex items-center gap-3">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Features</h3>
                        </div>
                        <ul class="mt-4 space-y-3">
                            <li v-for="feature in features" :key="feature" class="flex items-start text-sm text-gray-600">
                                <span class="mr-2 mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-400"></span>
                                {{ feature }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Reviews -->
        <div id="reviews" class="mt-16 lg:mt-20">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Customer Reviews</h2>
            
            <div class="mt-6 flex flex-col gap-10 md:flex-row md:gap-16">
                <!-- Summary -->
                <div class="w-full md:w-1/3">
                    <div class="flex items-center gap-4">
                        <p class="text-5xl font-bold text-gray-900">{{ totalReviews > 0 ? averageRating.toFixed(1) : '0.0' }}</p>
                        <div>
                            <div class="flex text-blue-500">
                                <svg v-for="i in 5" :key="i" class="h-5 w-5 flex-shrink-0" :class="i <= averageRatingRounded ? 'fill-current' : 'text-gray-300 fill-current'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ totalReviews > 0 ? `Based on ${totalReviews} review${totalReviews > 1 ? 's' : ''}` : 'No reviews yet' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div v-for="i in 5" :key="i" class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-600 w-3">{{ 6-i }}</span>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full bg-blue-500" :style="{ width: `${ratingPercentages[6 - i] || 0}%` }"></div>
                            </div>
                            <span class="text-sm text-gray-400 w-8 text-right">{{ ratingPercentages[6 - i] || 0 }}%</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button 
                            v-if="user"
                            @click="showReviewForm = true"
                            class="w-full flex items-center justify-center rounded-lg border border-gray-300 bg-white px-8 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Write a Review
                        </button>
                        <Link
                            v-else
                            :href="route('login')"
                            class="w-full flex items-center justify-center rounded-lg border border-gray-300 bg-white px-8 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Log in to Review
                        </Link>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="w-full md:w-2/3 space-y-8">
                    <div v-if="reviews.length === 0" class="rounded-2xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                        No reviews yet. Be the first to share your experience.
                    </div>
                    <div v-for="review in reviews" :key="review.id" class="border-b border-gray-100 pb-8 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-gray-900">{{ review.user?.name || 'Anonymous' }}</h4>
                            <span class="text-sm text-gray-500">{{ formatReviewDate(review.created_at) }}</span>
                        </div>
                        <div class="mt-1 flex items-center">
                            <div class="flex text-blue-500">
                                <svg v-for="i in 5" :key="i" class="h-4 w-4 flex-shrink-0" :class="i <= review.rating ? 'fill-current' : 'text-gray-300 fill-current'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-600 leading-relaxed">{{ review.comment }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-24">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">You May Also Like</h2>
            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                <ProductCard 
                    v-for="related in relatedProducts" 
                    :key="related.id" 
                    :product="related" 
                />
            </div>
        </div>

        <!-- Review Modal -->
        <Modal :show="showReviewForm" @close="showReviewForm = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Write a Review for {{ product.name }}
                </h2>
                
                <form @submit.prevent="submitReview" class="mt-6">
                    <!-- Rating -->
                    <div>
                        <InputLabel value="Rating" />
                        <div class="mt-2 flex items-center gap-2">
                            <button 
                                v-for="star in 5" 
                                :key="star"
                                type="button"
                                @click="reviewForm.rating = star"
                                class="focus:outline-none transition-colors"
                            >
                                <svg 
                                    class="h-8 w-8" 
                                    :class="star <= reviewForm.rating ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current'" 
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                            <span class="ml-2 text-sm text-gray-500 font-medium">{{ reviewForm.rating }} out of 5</span>
                        </div>
                        <p v-if="reviewForm.errors.rating" class="mt-2 text-sm text-red-600">{{ reviewForm.errors.rating }}</p>
                    </div>

                    <!-- Comment -->
                    <div class="mt-6">
                        <InputLabel for="comment" value="Review" />
                        <TextArea
                            id="comment"
                            v-model="reviewForm.comment"
                            rows="4"
                            class="mt-1 block w-full"
                            placeholder="Share your thoughts about this product..."
                            required
                        />
                        <p v-if="reviewForm.errors.comment" class="mt-2 text-sm text-red-600">{{ reviewForm.errors.comment }}</p>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="showReviewForm = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton 
                            :class="{ 'opacity-25': reviewForm.processing }" 
                            :disabled="reviewForm.processing"
                        >
                            Submit Review
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </ShopLayout>
</template>
