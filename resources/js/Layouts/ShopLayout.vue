<script setup>
import { usePage } from '@inertiajs/vue3';
import ShopHeader from '@/Components/Shop/Header.vue';
import ShopFooter from '@/Components/Shop/Footer.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex flex-col font-sans">
        <FlashMessage />
        <ShopHeader />

        <main class="flex-1 w-full mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

        <ShopFooter />
    </div>
</template>