<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    items: Array,
    total: Number,
});

const updateForm = useForm({});
const promoCode = ref('');

const updateQuantity = (item, quantity) => {
    if (quantity < 1) return;
    updateForm.transform(() => ({
        quantity,
    })).patch(route('cart.update', item.product_id), {
        preserveScroll: true,
    });
};

const removeItem = (item) => {
    updateForm.delete(route('cart.destroy', item.product_id), {
        preserveScroll: true,
    });
};

const subtotal = computed(() => {
    return props.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const tax = computed(() => subtotal.value * 0.05); // Mock 5% tax
const shipping = 0; // Free shipping
const grandTotal = computed(() => subtotal.value + tax.value + shipping);

import { getImageUrl } from '@/Utils/image';
const getProductImageUrl = (item) => {
    return getImageUrl(item.image_path, 200, 200);
};
</script>

<template>
    <ShopLayout>
        <Head title="Your Shopping Cart" />

        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-gray-500 mb-6">
            <Link :href="route('shop.index')" class="hover:text-gray-900">Home</Link>
            <span class="mx-2">›</span>
            <span class="text-gray-900 font-medium">Shopping Cart</span>
        </nav>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Your Shopping Cart</h1>
            <p class="mt-1 text-gray-500">
                You have {{ items.length }} items in your cart
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Left Column: Cart Items -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-4 border-b border-gray-100 bg-gray-50/50 px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <div class="col-span-6">Product</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-2 text-center">Quantity</div>
                        <div class="col-span-2 text-right">Total</div>
                    </div>

                    <!-- Cart Items -->
                    <div v-if="items.length" class="divide-y divide-gray-100">
                        <div
                            v-for="item in items"
                            :key="item.product_id"
                            class="grid grid-cols-12 gap-4 p-6 items-center"
                        >
                            <!-- Product Info -->
                            <div class="col-span-6 flex items-center gap-4">
                                <div class="h-20 w-20 flex-shrink-0 rounded-lg bg-gray-100 overflow-hidden border border-gray-200">
                                    <!-- Placeholder or Real Image -->
                                    <img 
                                        v-if="getProductImageUrl(item)" 
                                        :src="getProductImageUrl(item)" 
                                        :alt="item.name" 
                                        class="h-full w-full object-cover"
                                    />
                                    <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">
                                        {{ item.name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        <span v-if="item.size">Size: {{ item.size }}</span>
                                        <span v-if="item.size && item.color"> | </span>
                                        <span v-if="item.color">Color: {{ item.color }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-span-2 text-center text-sm text-gray-600">
                                ${{ Number(item.price).toFixed(2) }}
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-2 flex justify-center">
                                <div class="flex items-center rounded-md border border-gray-300 bg-white">
                                    <button 
                                        @click="updateQuantity(item, item.quantity - 1)"
                                        class="px-2 py-1 text-gray-600 hover:bg-gray-50 disabled:opacity-50"
                                        :disabled="item.quantity <= 1"
                                    >
                                        −
                                    </button>
                                    <input 
                                        type="text" 
                                        readonly 
                                        :value="item.quantity"
                                        class="w-8 text-center border-none p-0 text-sm text-gray-900 focus:ring-0"
                                    />
                                    <button 
                                        @click="updateQuantity(item, item.quantity + 1)"
                                        class="px-2 py-1 text-gray-600 hover:bg-gray-50"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Total & Action -->
                            <div class="col-span-2 flex items-center justify-end gap-4">
                                <span class="text-sm font-bold text-gray-900">
                                    ${{ (item.price * item.quantity).toFixed(2) }}
                                </span>
                                <button
                                    type="button"
                                    class="text-gray-400 hover:text-red-500 transition-colors"
                                    @click="removeItem(item)"
                                    title="Remove item"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-8 text-center text-gray-500">
                        Your cart is empty.
                    </div>
                </div>

                <!-- Bottom Actions -->
                <div class="flex items-center justify-between pt-4">
                    <Link :href="route('shop.index')" class="flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Continue Shopping
                    </Link>
                    
                    <button class="flex items-center text-gray-500 hover:text-gray-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Clear Cart
                    </button>
                </div>
            </div>

            <!-- Right Column: Summary -->
            <div class="space-y-6">
                <!-- Promo Code -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Have a promo code?</h3>
                    <div class="flex gap-2">
                        <input 
                            v-model="promoCode"
                            type="text" 
                            placeholder="Enter code" 
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        />
                        <button class="bg-blue-50 text-blue-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-100 transition-colors">
                            Apply
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h3>
                    
                    <div class="space-y-3 text-sm border-b border-gray-100 pb-4 mb-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">${{ subtotal.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Estimated Shipping</span>
                            <span class="font-medium text-emerald-600">Free</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span class="font-medium text-gray-900">${{ tax.toFixed(2) }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-end mb-6">
                        <span class="text-base font-bold text-gray-900">Total</span>
                        <div class="text-right">
                            <span class="block text-2xl font-bold text-blue-600">${{ grandTotal.toFixed(2) }}</span>
                            <span class="text-xs text-gray-400 uppercase">Including VAT</span>
                        </div>
                    </div>

                    <Link
                        :href="route('checkout.index')"
                        class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-sm mb-4"
                        :class="{ 'opacity-50 pointer-events-none': !items.length }"
                    >
                        Proceed to Checkout →
                    </Link>

                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-2">Secure Payments via</p>
                        <div class="flex justify-center gap-3 text-gray-400">
                            <!-- Placeholder Icons -->
                            <svg class="h-6 w-8" viewBox="0 0 32 20" fill="currentColor">
                                <rect width="32" height="20" rx="2" fill="#E5E7EB"/>
                                <path d="M6 10h20" stroke="white" stroke-width="2"/>
                            </svg>
                            <svg class="h-6 w-8" viewBox="0 0 32 20" fill="currentColor">
                                <rect width="32" height="20" rx="2" fill="#E5E7EB"/>
                                <circle cx="10" cy="10" r="4" fill="white" fill-opacity="0.5"/>
                                <circle cx="22" cy="10" r="4" fill="white" fill-opacity="0.5"/>
                            </svg>
                             <svg class="h-6 w-8" viewBox="0 0 32 20" fill="currentColor">
                                <rect width="32" height="20" rx="2" fill="#E5E7EB"/>
                                <path d="M16 4v12M10 10h12" stroke="white" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Guarantee -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex gap-3">
                    <div class="flex-shrink-0 text-blue-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">30-Day Guarantee</h4>
                        <p class="text-xs text-blue-700 mt-1">Not satisfied? Return your items within 30 days for a full refund.</p>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
