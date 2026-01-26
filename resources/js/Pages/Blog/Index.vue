<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    featuredStory: Object,
    latestStories: Array,
    trendingPosts: Array,
});

const shopTheLook = {
    title: "Structured Linen Blazer",
    collection: "Essential Collection",
    price: 129.00,
    image: "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800&auto=format&fit=crop",
    link: "#"
};

const activeFilter = ref('All Posts');
const filters = ['All Posts', 'Trends', 'Lifestyle', 'Sustainable'];

</script>

<template>
    <ShopLayout>
        <Head title="Blog" />

        <div class="bg-gray-50 min-h-screen pb-20">
            <!-- Hero Section / Featured Story -->
            <div v-if="featuredStory" class="bg-white border-b border-gray-100">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 grid md:grid-cols-2 gap-0">
                        <div class="relative h-[400px] md:h-auto">
                            <img :src="featuredStory.image" alt="Featured Story" class="absolute inset-0 w-full h-full object-cover">
                        </div>
                        <div class="p-8 md:p-12 lg:p-16 flex flex-col justify-center">
                            <span class="text-blue-600 font-bold text-sm tracking-wider uppercase mb-4">{{ featuredStory.category }}</span>
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                                {{ featuredStory.title }}
                            </h1>
                            <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                                {{ featuredStory.description }}
                            </p>
                            <div>
                                <Link :href="route('blog.show', featuredStory.slug)" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                    Read Full Story
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Main Content -->
                    <div class="lg:w-2/3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                            <h2 class="text-2xl font-bold text-gray-900">Latest Stories</h2>
                            
                            <!-- Filters -->
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    v-for="filter in filters" 
                                    :key="filter"
                                    @click="activeFilter = filter"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-all"
                                    :class="activeFilter === filter ? 'bg-black text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                                >
                                    {{ filter }}
                                </button>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="grid gap-8">
                            <article v-for="post in latestStories" :key="post.id" class="bg-white rounded-xl p-6 border border-gray-100 hover:shadow-md transition-shadow flex flex-col sm:flex-row gap-6">
                                <div class="w-full sm:w-48 h-48 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                    <img :src="post.image" :alt="post.title" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                                        <span class="text-blue-600 font-bold uppercase tracking-wider">{{ post.category }}</span>
                                        <span>&bull;</span>
                                        <span>{{ post.read_time }}</span> <!-- Ensure read_time is available or mocked in transform -->
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                                        <Link :href="route('blog.show', post.slug)" class="hover:text-blue-600 transition-colors">
                                            {{ post.title }}
                                        </Link>
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ post.excerpt }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-auto">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                             <img v-if="post.author.avatar" :src="post.author.avatar" alt="" class="w-full h-full object-cover">
                                             <span v-else class="flex w-full h-full items-center justify-center text-xs font-bold text-gray-500">{{ post.author.name.charAt(0) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ post.author.name }}</span>
                                    </div>
                                </div>
                            </article>
                            
                            <div v-if="latestStories.length === 0" class="text-center py-12 text-gray-500">
                                No stories found.
                            </div>
                        </div>
                        
                        <!-- Load More -->
                        <div v-if="latestStories.length > 0" class="mt-12 text-center">
                            <button class="px-8 py-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-900 hover:bg-gray-50 transition-colors">
                                Load More Stories
                            </button>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:w-1/3 space-y-12">
                        <!-- Search -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-6">Search Blog</h3>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    placeholder="Search for style tips..." 
                                    class="w-full pl-4 pr-10 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                >
                                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-blue-600 hover:text-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Trending Now -->
                        <div v-if="trendingPosts.length > 0">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-6">Trending Now</h3>
                            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
                                <div v-for="post in trendingPosts" :key="post.id" class="flex items-center gap-4 group">
                                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                        <img :src="post.image" :alt="post.title" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <span class="text-blue-600 font-bold text-xs mb-1 block">{{ post.rank }}</span>
                                        <h4 class="text-sm font-bold text-gray-900 leading-snug group-hover:text-blue-600 transition-colors">
                                            <Link :href="route('blog.show', post.slug)">{{ post.title }}</Link>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shop The Look -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-6">Shop The Look</h3>
                            <div class="bg-white rounded-xl overflow-hidden border border-gray-100">
                                <div class="aspect-[3/4] relative bg-gray-100">
                                    <img :src="shopTheLook.image" :alt="shopTheLook.title" class="absolute inset-0 w-full h-full object-cover">
                                    <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm rounded-lg p-4">
                                        <span class="text-xs text-gray-500 mb-1 block">{{ shopTheLook.collection }}</span>
                                        <h4 class="font-bold text-gray-900 text-sm mb-2">{{ shopTheLook.title }}</h4>
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-blue-600">${{ shopTheLook.price.toFixed(2) }}</span>
                                            <a :href="shopTheLook.link" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Newsletter -->
                        <div class="bg-blue-600 rounded-xl p-8 text-center text-white">
                            <h3 class="font-bold text-xl mb-2">Join the Club</h3>
                            <p class="text-blue-100 text-sm mb-6">Get exclusive style tips and early access to new collections.</p>
                            <form class="space-y-3">
                                <input type="email" placeholder="Your email address" class="w-full px-4 py-3 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-white/50">
                                <button class="w-full px-4 py-3 bg-black text-white text-sm font-bold rounded-lg hover:bg-gray-900 transition-colors">
                                    Subscribe
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
