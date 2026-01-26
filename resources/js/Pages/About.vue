<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    page: Object,
});
</script>

<template>
    <ShopLayout>
        <Head :title="page.meta?.seo_title || page.title">
            <meta name="description" :content="page.meta?.seo_description" />
        </Head>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ page.meta?.story_headline || page.title }}</h1>
                <div class="prose prose-lg mx-auto text-gray-500 whitespace-pre-line" v-html="page.content"></div>
            </div>

            <!-- Values -->
            <div v-if="page.meta?.values?.length" class="py-12 border-t border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-10">Our Values</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="(val, index) in page.meta.values" :key="index" class="text-center p-6 bg-gray-50 rounded-2xl">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <!-- Simple Icon Mapping -->
                            <svg v-if="val.icon === 'heart'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <svg v-else-if="val.icon === 'leaf'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">{{ val.title }}</h3>
                        <p class="text-gray-500 text-sm">{{ val.description }}</p>
                    </div>
                </div>
            </div>

            <!-- Team -->
            <div v-if="page.meta?.team?.length" class="py-12 border-t border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-10">Meet the Team</h2>
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <div v-for="(member, index) in page.meta.team" :key="index" class="text-center group">
                        <div class="relative w-40 h-40 mx-auto mb-4 rounded-full overflow-hidden bg-gray-100 border-4 border-white shadow-lg">
                            <img :src="member.image" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                        </div>
                        <h3 class="font-bold text-gray-900">{{ member.name }}</h3>
                        <p class="text-blue-600 text-sm">{{ member.role }}</p>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
