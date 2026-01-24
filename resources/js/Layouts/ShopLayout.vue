<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useFlash } from '@/Composables/useFlash';
import ShopHeader from '@/Components/ShopHeader.vue';

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

        <footer class="bg-white pt-24 pb-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-5">
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-blue-600 text-white">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </div>
                            <span class="font-bold text-xl tracking-tight text-gray-900">ShopWave</span>
                        </div>
                        <p class="text-base text-gray-500 mb-8 max-w-sm leading-relaxed">
                            Redefining lifestyle through sustainable fashion and modern design. Join the wave of conscious consumption.
                        </p>
                        <div class="flex gap-5">
                            <a href="#" class="text-gray-400 hover:text-gray-900 transition-colors">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                            </a>
                             <a href="#" class="text-gray-400 hover:text-gray-900 transition-colors">
                                <span class="sr-only">Twitter</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                            </a>
                             <a href="#" class="text-gray-400 hover:text-gray-900 transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.416 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465 1.067-.047 1.409-.06 3.809-.06h.63zm1.418 1.998l-1.418.002c-2.404 0-2.747.01-3.722.054-.97.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.886-.344 1.857-.044.975-.054 1.318-.054 3.722v1.418c0 2.404.01 2.747.054 3.722.045.97.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.886.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.886.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.886-.3-1.857-.344-1.023-.047-1.351-.054-3.808-.054zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.998a3.137 3.137 0 100 6.274 3.137 3.137 0 000-6.274zM16.949 7.05a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z" clip-rule="evenodd" /></svg>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Shop</h3>
                        <ul class="mt-6 space-y-4">
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">All Products</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Featured Drops</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Men's Apparel</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Women's Apparel</a></li>
                        </ul>
                    </div>
                     <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Support</h3>
                        <ul class="mt-6 space-y-4">
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Help Center</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Shipping & Returns</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Size Guide</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Track Order</a></li>
                        </ul>
                    </div>
                     <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Company</h3>
                        <ul class="mt-6 space-y-4">
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Our Story</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Sustainability</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Careers</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900 transition-colors">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-16 pt-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <p class="text-sm text-gray-400">
                        &copy; {{ new Date().getFullYear() }} ShopWave Inc. All rights reserved.
                    </p>
                     <div class="flex gap-3">
                         <!-- Payment icons placeholder -->
                        <div class="h-8 w-12 bg-gray-100 rounded-md"></div>
                        <div class="h-8 w-12 bg-gray-100 rounded-md"></div>
                        <div class="h-8 w-12 bg-gray-100 rounded-md"></div>
                        <div class="h-8 w-12 bg-gray-100 rounded-md"></div>
                     </div>
                </div>
            </div>
        </footer>
    </div>
</template>