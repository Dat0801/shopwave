<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Facebook, 
    Twitter, 
    Linkedin, 
    ShoppingCart, 
    Heart,
    ChevronLeft,
    ChevronRight,
    MessageCircle
} from 'lucide-vue-next';

const props = defineProps({
    post: Object,
    related_posts: Array,
    shop_the_look: Array
});

const comment = ref('');

const submitComment = () => {
    // Placeholder for comment submission
    console.log('Submitting comment:', comment.value);
    comment.value = '';
};
</script>

<template>
    <ShopLayout>
        <Head :title="post.title" />

        <div class="bg-white min-h-screen pb-20">
            <!-- Breadcrumb -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 text-sm text-gray-500">
                <Link :href="route('home')" class="hover:text-blue-600">Home</Link>
                <span class="mx-2">></span>
                <Link :href="route('blog.index')" class="hover:text-blue-600">Blog</Link>
                <span class="mx-2">></span>
                <span class="text-gray-900 truncate">{{ post.title }}</span>
            </div>

            <!-- Hero Section -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-12">
                <div class="relative rounded-2xl overflow-hidden h-[400px] md:h-[500px]">
                    <img :src="post.image" :alt="post.title" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 md:p-12 text-white max-w-3xl">
                        <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block">
                            {{ post.category }}
                        </span>
                        <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">
                            {{ post.title }}
                        </h1>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    
                    <!-- Left Sidebar (Social Share) -->
                    <div class="hidden lg:block lg:col-span-1">
                        <div class="sticky top-24 flex flex-col gap-4 items-center">
                            <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition">
                                <Facebook class="w-5 h-5" />
                            </button>
                            <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center hover:bg-blue-400 hover:text-white transition">
                                <Twitter class="w-5 h-5" />
                            </button>
                            <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center hover:bg-blue-700 hover:text-white transition">
                                <Linkedin class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="lg:col-span-8">
                        <!-- Author Header -->
                        <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-8">
                            <div class="flex items-center gap-4">
                                <div v-if="post.author.avatar" class="w-12 h-12 rounded-full overflow-hidden">
                                    <img :src="post.author.avatar" :alt="post.author.name" class="w-full h-full object-cover">
                                </div>
                                <div v-else class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                    {{ post.author.name.charAt(0) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ post.author.name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ post.author.role }} • Published {{ post.published_at }} • {{ post.read_time }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                                    Follow
                                </button>
                                <button class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Blog Content -->
                        <div class="prose prose-lg prose-blue max-w-none text-gray-600" v-html="post.content"></div>

                        <!-- Shop The Look Section -->
                        <div class="mt-16 mb-16">
                            <div class="flex items-center justify-between mb-8">
                                <h2 class="text-2xl font-bold text-gray-900">Shop the Look</h2>
                                <div class="flex gap-2">
                                    <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                        <ChevronLeft class="w-4 h-4" />
                                    </button>
                                    <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                        <ChevronRight class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div v-for="product in shop_the_look" :key="product.id" class="group">
                                    <div class="relative bg-gray-100 rounded-xl overflow-hidden aspect-[4/5] mb-4">
                                        <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        <span class="absolute top-3 left-3 bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded">
                                            {{ product.category }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition">{{ product.name }}</h3>
                                    <p class="text-sm text-gray-500 mb-3">{{ product.category }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900">${{ product.price.toFixed(2) }}</span>
                                        <button class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition">
                                            <ShoppingCart class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keep Reading Section (Full Width) -->
                <div class="border-t border-gray-100 pt-16 mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Keep Reading</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <Link v-for="related in related_posts" :key="related.id" :href="route('blog.show', related.slug)" class="group cursor-pointer">
                            <div class="rounded-xl overflow-hidden h-48 mb-4">
                                <img :src="related.image" :alt="related.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <span class="text-blue-600 text-xs font-bold uppercase tracking-wider mb-2 block">{{ related.category }}</span>
                            <h3 class="font-bold text-gray-900 text-lg group-hover:text-blue-600 transition">{{ related.title }}</h3>
                        </Link>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="mt-16 max-w-4xl mx-auto">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Comments (12)</h2>
                    <div class="bg-gray-50 rounded-xl p-6 mb-8 flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0"></div>
                        <div class="flex-1">
                            <textarea 
                                v-model="comment" 
                                placeholder="Share your thoughts..." 
                                class="w-full border-0 bg-transparent resize-none focus:ring-0 text-gray-900 placeholder-gray-500 min-h-[50px]"
                            ></textarea>
                            <div class="flex justify-end mt-2">
                                <button @click="submitComment" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                    Post Comment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
