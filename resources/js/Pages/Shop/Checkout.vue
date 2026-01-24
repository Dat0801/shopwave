<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [
            {
                product_id: 1,
                name: 'Essential White Tee',
                size: 'Medium',
                price: 45.00,
                quantity: 1,
                image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
            },
            {
                product_id: 2,
                name: 'Midnight Denim',
                size: '32/32',
                price: 120.00,
                quantity: 1,
                image: 'https://images.unsplash.com/photo-1542272454374-d41e47575f0f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
            }
        ]
    },
    total: {
        type: Number,
        default: 165.00
    },
});

const form = useForm({
    email: '',
    newsletter: false,
    firstName: 'Jane',
    lastName: 'Doe',
    address: '',
    city: 'New York',
    postalCode: '10001',
    paymentMethod: 'credit_card',
    cardNumber: '',
    cardExpiry: '',
    cardCvc: '',
});

const paymentMethod = ref('credit_card');
const discountCode = ref('');

const subtotal = computed(() => {
    return props.items.reduce((sum, item) => sum + (Number(item.price) * item.quantity), 0);
});
const shipping = 0;
const taxes = 13.20;
const grandTotal = computed(() => subtotal.value + shipping + taxes);

const submit = () => {
    form.post(route('checkout.store'));
};
</script>

<template>
    <ShopLayout>
        <Head title="Checkout" />

            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-gray-500 mb-8">
                <ol class="flex items-center space-x-2">
                    <li><Link :href="route('cart.index')" class="hover:text-gray-900">Cart</Link></li>
                    <li><span class="text-gray-400">›</span></li>
                    <li><span class="font-medium text-gray-900">Checkout</span></li>
                    <li><span class="text-gray-400">›</span></li>
                    <li><span class="text-gray-400">Order Complete</span></li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
                <!-- Left Column: Forms -->
                <div class="lg:col-span-7 space-y-10">
                    
                    <!-- 1. Contact Information -->
                    <section>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3">1</div>
                            <h2 class="text-lg font-bold text-gray-900">Contact Information</h2>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" id="email" v-model="form.email" placeholder="your@email.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div class="flex items-center">
                                    <input id="newsletter" type="checkbox" v-model="form.newsletter" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="newsletter" class="ml-2 block text-sm text-gray-600">Keep me up to date on news and exclusive offers</label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Shipping Address -->
                    <section>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3">2</div>
                            <h2 class="text-lg font-bold text-gray-900">Shipping Address</h2>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" id="first-name" v-model="form.firstName" placeholder="Jane" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div>
                                    <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" id="last-name" v-model="form.lastName" placeholder="Doe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" id="address" v-model="form.address" placeholder="123 Street Name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" id="city" v-model="form.city" placeholder="New York" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div>
                                    <label for="postal-code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                    <input type="text" id="postal-code" v-model="form.postalCode" placeholder="10001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 3. Payment Method -->
                    <section>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3">3</div>
                            <h2 class="text-lg font-bold text-gray-900">Payment Method</h2>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                            <div class="space-y-4">
                                <!-- Credit Card Option -->
                                <div class="rounded-md border" :class="paymentMethod === 'credit_card' ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200'">
                                    <div class="p-4 flex items-center justify-between cursor-pointer" @click="paymentMethod = 'credit_card'">
                                        <div class="flex items-center">
                                            <input type="radio" name="payment-method" value="credit_card" v-model="paymentMethod" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <label class="ml-3 block text-sm font-medium text-gray-900 cursor-pointer">Credit Card</label>
                                        </div>
                                        <svg class="h-5 w-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                            <rect x="2" y="5" width="20" height="14" rx="2" />
                                            <path d="M2 10h20" stroke="white" stroke-width="2" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Credit Card Details (Expandable) -->
                                    <div v-if="paymentMethod === 'credit_card'" class="px-4 pb-4 pt-2 border-t border-gray-200/50 space-y-4">
                                        <div>
                                            <label for="card-number" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Card Number</label>
                                            <input type="text" id="card-number" v-model="form.cardNumber" placeholder="0000 0000 0000 0000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="card-expiry" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Expiration (MM/YY)</label>
                                                <input type="text" id="card-expiry" v-model="form.cardExpiry" placeholder="12/26" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                                            </div>
                                            <div>
                                                <label for="card-cvc" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">CVV</label>
                                                <input type="text" id="card-cvc" v-model="form.cardCvc" placeholder="123" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Option -->
                                <div class="rounded-md border" :class="paymentMethod === 'paypal' ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200'">
                                    <div class="p-4 flex items-center justify-between cursor-pointer" @click="paymentMethod = 'paypal'">
                                        <div class="flex items-center">
                                            <input type="radio" name="payment-method" value="paypal" v-model="paymentMethod" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <label class="ml-3 block text-sm font-medium text-gray-900 cursor-pointer">PayPal</label>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.946 5.425-3.646 6.794-8.263 6.794H10.3c-.578 0-1.075.418-1.17 1.009l-.506 3.454c-.024.175.006.356.104.496.098.14.258.223.428.223h3.29c.578 0 1.075.418 1.17 1.009l.157 1.074a.641.641 0 0 1-.633.742h-6.064a.641.641 0 0 1-.633-.74l.662-4.563-1.033 6.467a.641.641 0 0 1-.633.74z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="mt-10 lg:mt-0 lg:col-span-5">
                    <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Order Summary</h2>

                        <!-- Items List -->
                        <div class="flow-root">
                            <ul role="list" class="-my-6 divide-y divide-gray-200">
                                <li v-for="item in items" :key="item.product_id" class="flex py-6">
                                    <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                        <img :src="getProductImageUrl(item)" :alt="item.name" class="h-full w-full object-cover object-center">
                                    </div>
                                    <div class="ml-4 flex flex-1 flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>{{ item.name }}</h3>
                                                <p>${{ Number(item.price).toFixed(2) }}</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">Size: {{ item.size }}</p>
                                        </div>
                                        <div class="flex flex-1 items-end justify-between text-sm">
                                            <p class="text-gray-500">Qty: {{ item.quantity }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Discount Code -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex gap-2">
                                <input type="text" v-model="discountCode" placeholder="Discount Code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                <button type="button" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">Apply</button>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <dl class="mt-6 space-y-4 border-t border-gray-200 pt-6 text-sm font-medium text-gray-900">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Subtotal</dt>
                                <dd>${{ subtotal.toFixed(2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Shipping</dt>
                                <dd class="text-green-600">Free</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Taxes</dt>
                                <dd>${{ taxes.toFixed(2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <dt class="text-lg font-bold text-gray-900">Total</dt>
                                <dd class="text-lg font-bold text-gray-900">${{ grandTotal.toFixed(2) }}</dd>
                            </div>
                        </dl>

                        <!-- Complete Purchase Button -->
                        <div class="mt-6">
                            <button type="submit" @click="submit" :disabled="form.processing" class="w-full rounded-md border border-transparent bg-blue-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                Complete Purchase
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <p class="mt-4 flex justify-center text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 mr-1">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                                Secure encrypted checkout
                            </p>
                        </div>
                    </div>
                </div>
            </div>

    </ShopLayout>
</template>
