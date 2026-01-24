<script setup>
import { usePage } from '@inertiajs/vue3';
import { useFlash } from '@/Composables/useFlash';
import ShopHeader from '@/Components/Shop/Header.vue';
import ShopFooter from '@/Components/Shop/Footer.vue';

const page = usePage();

const { success, error } = useFlash();
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex flex-col font-sans">
        <ShopHeader />

        <main class="flex-1 w-full mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div
                v-if="success || error"
                class="mb-6"
            >
                <div
                    v-if="success"
                    class="flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm"
                >
                    <span class="mt-0.5 h-2 w-2 rounded-full bg-emerald-500" />
                    <p class="flex-1">
                        {{ success }}
                    </p>
                </div>
                <div
                    v-if="error"
                    class="flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm"
                >
                    <span class="mt-0.5 h-2 w-2 rounded-full bg-rose-500" />
                    <p class="flex-1">
                        {{ error }}
                    </p>
                </div>
            </div>

            <slot />
        </main>

        <ShopFooter />
    </div>
</template>