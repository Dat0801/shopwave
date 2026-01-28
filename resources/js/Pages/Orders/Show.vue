<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    CheckIcon, 
    TruckIcon, 
    MapPinIcon, 
    HomeIcon, 
    ArrowDownTrayIcon, 
    ArrowPathIcon,
    CreditCardIcon
} from '@heroicons/vue/24/outline';
import { CheckCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    order: Object,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        month: 'long', 
        day: 'numeric', 
        year: 'numeric' 
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const steps = [
    { id: 1, name: 'Confirmed', status: ['pending', 'processing', 'paid'] },
    { id: 2, name: 'Shipped', status: ['shipped', 'in_transit'] },
    { id: 3, name: 'Out for Delivery', status: ['out_for_delivery'] }, // Assuming this status exists or we map it
    { id: 4, name: 'Delivered', status: ['delivered', 'completed'] },
];

const currentStep = computed(() => {
    const status = props.order.status;
    if (status === 'cancelled') return -1;
    
    if (['delivered', 'completed'].includes(status)) return 4;
    if (['out_for_delivery'].includes(status)) return 3;
    if (['shipped', 'in_transit'].includes(status)) return 2;
    return 1;
});

const isStepComplete = (stepId) => {
    return currentStep.value >= stepId;
};

const isStepCurrent = (stepId) => {
    return currentStep.value === stepId;
};

const estimatedDelivery = computed(() => {
    // Mock estimated delivery: created_at + 3 days
    const date = new Date(props.order.created_at);
    date.setDate(date.getDate() + 3);
    return date.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
});

const buyAgain = () => {
    // Logic to add items to cart or redirect to shop
    router.visit(route('shop.index'));
};

const downloadInvoice = () => {
    window.print();
};

const contactSupport = () => {
    router.visit(route('contact.index'));
};

// Check if billing address is same as shipping
const isBillingSameAsShipping = computed(() => {
    if (!props.order.billing_address) return true;
    // Simple check, can be more robust
    return JSON.stringify(props.order.shipping_address) === JSON.stringify(props.order.billing_address);
});

</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <ProfileLayout>
        <template #breadcrumb>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <Link :href="route('profile.edit')" class="hover:text-gray-900 transition-colors">
                    Account
                </Link>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
                <Link :href="route('orders.index')" class="hover:text-gray-900 transition-colors">
                    Order History
                </Link>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-900 font-medium">Order #SW-{{ order.id }}</span>
            </div>
        </template>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Order #SW-{{ order.id }}</h1>
            <div class="mt-2 flex flex-wrap items-center justify-between gap-4">
                <div class="text-gray-500">
                    Placed on <span class="text-gray-900 font-medium">{{ formatDate(order.created_at) }}</span> 
                    <span class="mx-2">•</span> 
                    Total: <span class="text-gray-900 font-medium">{{ formatCurrency(order.total_price) }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        @click="downloadInvoice"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        Invoice
                    </button>
                    <button 
                        @click="buyAgain"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 shadow-sm transition-colors"
                    >
                        <ArrowPathIcon class="w-4 h-4" />
                        Buy Again
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Status & Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Status Timeline -->
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-lg font-bold text-gray-900">Order Status</h2>
                        <span 
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium uppercase tracking-wide"
                            :class="{
                                'bg-blue-100 text-blue-800': order.status === 'processing' || order.status === 'shipped',
                                'bg-green-100 text-green-800': order.status === 'delivered' || order.status === 'completed',
                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                'bg-red-100 text-red-800': order.status === 'cancelled'
                            }"
                        >
                            {{ order.status }}
                        </span>
                    </div>

                    <div v-if="order.status !== 'cancelled'" class="relative">
                        <!-- Progress Bar Background -->
                        <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 rounded-full z-0"></div>
                        
                        <!-- Active Progress Bar -->
                        <div 
                            class="absolute top-1/2 left-0 h-1 bg-blue-600 -translate-y-1/2 rounded-full z-0 transition-all duration-500"
                            :style="{ width: `${((currentStep - 1) / (steps.length - 1)) * 100}%` }"
                        ></div>

                        <!-- Steps -->
                        <div class="relative z-10 flex justify-between">
                            <div v-for="(step, index) in steps" :key="step.id" class="flex flex-col items-center">
                                <div 
                                    class="w-8 h-8 rounded-full flex items-center justify-center border-2 bg-white transition-colors duration-300"
                                    :class="[
                                        isStepComplete(step.id) ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-400'
                                    ]"
                                >
                                    <CheckIcon v-if="isStepComplete(step.id)" class="w-5 h-5" />
                                    <div v-else class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
                                </div>
                                <span 
                                    class="mt-3 text-xs font-medium"
                                    :class="[
                                        isStepComplete(step.id) ? 'text-blue-600' : 'text-gray-500'
                                    ]"
                                >
                                    {{ step.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-4 bg-red-50 rounded-lg text-red-700">
                        This order has been cancelled.
                    </div>

                    <!-- Estimated Delivery -->
                    <div v-if="order.status !== 'cancelled' && order.status !== 'delivered' && order.status !== 'completed'" class="mt-8 rounded-xl bg-blue-50 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <TruckIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Estimated delivery: <span class="font-bold">{{ estimatedDelivery }}</span></p>
                            </div>
                        </div>
                        <Link :href="route('contact.index')" class="text-sm font-semibold text-blue-600 hover:text-blue-700 whitespace-nowrap">
                            Track Package
                        </Link>
                    </div>
                </div>

                <!-- Items -->
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-gray-900">Items in your order</h2>
                    </div>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="item in order.items" :key="item.id" class="p-6">
                            <div class="flex items-start gap-6">
                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                                    <img 
                                        v-if="item.product?.image_path"
                                        :src="item.product.image_path.startsWith('http') ? item.product.image_path : `/storage/${item.product.image_path}`" 
                                        :alt="item.product?.name"
                                        class="h-full w-full object-cover object-center"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-400">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex flex-1 flex-col sm:flex-row sm:justify-between">
                                    <div class="pr-6">
                                        <h3 class="text-base font-bold text-gray-900">
                                            <Link :href="route('shop.show', item.product?.slug || '#')" class="hover:text-blue-600 transition-colors">
                                                {{ item.product?.name || 'Product Unavailable' }}
                                            </Link>
                                        </h3>
                                        <div class="mt-1 flex flex-col space-y-1 text-sm text-gray-500">
                                            <p v-if="item.product?.category">Category: {{ item.product.category.name }}</p>
                                            <!-- Mock Options -->
                                            <!-- <p>Color: Midnight Blue | Size: 10.5</p> -->
                                        </div>
                                        <p class="mt-4 text-sm text-gray-500">Qty: {{ item.quantity }}</p>
                                    </div>
                                    <div class="mt-4 sm:mt-0 flex flex-col items-start sm:items-end justify-between">
                                        <p class="text-base font-bold text-gray-900">{{ formatCurrency(item.price) }}</p>
                                        <Link :href="route('shop.show', item.product?.slug || '#')" class="mt-4 text-sm font-medium text-blue-600 hover:text-blue-500">
                                            Write a review
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Address & Payment Info -->
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden p-6 space-y-8">
                    <!-- Shipping Address -->
                    <div v-if="order.shipping_address">
                        <div class="flex items-center gap-2 mb-3 text-gray-400 uppercase text-xs font-bold tracking-wider">
                            <TruckIcon class="w-4 h-4" />
                            Shipping Address
                        </div>
                        <div class="text-sm text-gray-600 leading-relaxed">
                            <p class="font-bold text-gray-900 mb-1">
                                {{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}
                            </p>
                            <p>{{ order.shipping_address.address }}</p>
                            <p>{{ order.shipping_address.city }}, {{ order.shipping_address.postal_code }}</p>
                            <p v-if="order.shipping_address.country">{{ order.shipping_address.country }}</p>
                            <p v-if="order.shipping_address.phone" class="mt-2 text-gray-500">Tel: {{ order.shipping_address.phone }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <!-- Billing Address -->
                    <div>
                        <div class="flex items-center gap-2 mb-3 text-gray-400 uppercase text-xs font-bold tracking-wider">
                            <MapPinIcon class="w-4 h-4" />
                            Billing Address
                        </div>
                        <div class="text-sm text-gray-600 leading-relaxed">
                            <p v-if="isBillingSameAsShipping">Same as shipping address</p>
                            <div v-else-if="order.billing_address">
                                <p class="font-bold text-gray-900 mb-1">
                                    {{ order.billing_address.first_name }} {{ order.billing_address.last_name }}
                                </p>
                                <p>{{ order.billing_address.address }}</p>
                                <p>{{ order.billing_address.city }}, {{ order.billing_address.postal_code }}</p>
                            </div>
                            <p v-else>Same as shipping address</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <!-- Payment Method -->
                    <div>
                        <div class="flex items-center gap-2 mb-3 text-gray-400 uppercase text-xs font-bold tracking-wider">
                            <CreditCardIcon class="w-4 h-4" />
                            Payment Method
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Icon based on payment method -->
                            <div class="h-8 w-12 bg-gray-100 rounded flex items-center justify-center text-xs font-bold text-gray-500">
                                {{ order.payment_method === 'cod' ? 'COD' : 'CARD' }}
                            </div>
                            <div class="text-sm">
                                <p class="font-bold text-gray-900">
                                    {{ order.payment_method === 'cod' ? 'Cash on Delivery' : 'Credit Card' }}
                                </p>
                                <p v-if="order.payment_method !== 'cod'" class="text-gray-500">Visa ending in 4242</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500">Order Summary</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">{{ formatCurrency(Number(order.total_price) + Number(order.discount_amount)) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span class="font-medium text-green-600">Free</span>
                        </div>
                         <div v-if="Number(order.discount_amount) > 0" class="flex items-center justify-between text-sm text-green-600">
                            <span>Discount <span v-if="order.coupon_code">({{ order.coupon_code }})</span></span>
                            <span class="font-medium">-{{ formatCurrency(order.discount_amount) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Estimated Tax</span>
                            <span class="font-medium text-gray-900">$0.00</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                            <span class="text-base font-bold text-gray-900">Total</span>
                            <span class="text-xl font-bold text-blue-600">{{ formatCurrency(order.total_price) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="rounded-2xl bg-gray-900 p-6 text-white text-center">
                    <h3 class="text-lg font-bold mb-2">Need help with this order?</h3>
                    <p class="text-gray-400 text-sm mb-6">If you have any issues with your items or haven't received them, please reach out to our support team.</p>
                    <button 
                        @click="contactSupport"
                        class="w-full bg-white text-gray-900 font-bold py-2.5 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        Contact Support
                    </button>
                </div>

            </div>
        </div>
    </ProfileLayout>
</template>
