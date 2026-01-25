<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    order: Object,
});

const getStatusColor = (status) => {
    switch (status) {
        case 'delivered':
        case 'completed':
            return 'bg-emerald-100 text-emerald-700';
        case 'processing':
        case 'in_transit':
        case 'shipped':
            return 'bg-blue-50 text-blue-700';
        case 'cancelled':
            return 'bg-gray-100 text-gray-600';
        default:
            return 'bg-amber-50 text-amber-700';
    }
};

const getStatusLabel = (status) => {
    if (status === 'in_transit') return 'In Transit';
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const cancelOrder = () => {
    if (confirm('Are you sure you want to cancel this order?')) {
        router.patch(route('orders.cancel', props.order.id));
    }
};
</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <ProfileLayout>
        <template #breadcrumb>
            <div class="flex items-center gap-2">
                <Link :href="route('orders.index')" class="text-gray-500 hover:text-gray-900 transition-colors">
                    Order History
                </Link>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-900 font-medium">Order #{{ order.id }}</span>
            </div>
        </template>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Order #{{ order.id }}</h1>
                <p class="mt-1 text-gray-500">
                    Placed on {{ new Date(order.created_at).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                 <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"
                    :class="getStatusColor(order.status)"
                >
                    <span class="mr-1.5 h-2 w-2 rounded-full bg-current"></span>
                    {{ getStatusLabel(order.status) }}
                </span>
                <button 
                    v-if="order.status === 'pending'"
                    @click="cancelOrder"
                    class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors"
                >
                    Cancel Order
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Items -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-gray-900">Items</h2>
                    </div>
                    <ul role="list" class="divide-y divide-gray-100">
                        <li v-for="item in order.items" :key="item.id" class="p-6 flex items-center gap-6">
                            <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
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
                            <div class="flex flex-1 flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                        <h3>
                                            <Link :href="route('shop.show', item.product?.slug || '#')" class="hover:text-blue-600 transition-colors">
                                                {{ item.product?.name || 'Product Unavailable' }}
                                            </Link>
                                        </h3>
                                        <p class="ml-4">${{ item.price }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500" v-if="item.product?.category">
                                        {{ item.product.category.name }}
                                    </p>
                                </div>
                                <div class="flex flex-1 items-end justify-between text-sm">
                                    <p class="text-gray-500">Qty {{ item.quantity }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Order Summary & Address -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-gray-900">Order Summary</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">${{ (Number(order.total_price) + Number(order.discount_amount)).toFixed(2) }}</span>
                        </div>
                         <div v-if="Number(order.discount_amount) > 0" class="flex items-center justify-between text-sm text-green-600">
                            <span>Discount <span v-if="order.coupon_code">({{ order.coupon_code }})</span></span>
                            <span class="font-medium">-${{ order.discount_amount }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span class="font-medium text-gray-900">Free</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Tax</span>
                            <span class="font-medium text-gray-900">$0.00</span>
                        </div>
                        <div class="border-t border-gray-100 pt-4 flex items-center justify-between text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span>${{ order.total_price }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div v-if="order.shipping_address" class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-gray-900">Shipping Address</h2>
                    </div>
                    <div class="p-6 text-sm text-gray-600">
                        <p class="font-bold text-gray-900 mb-1">
                            {{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}
                        </p>
                        <p>{{ order.shipping_address.address }}</p>
                        <p>{{ order.shipping_address.city }}, {{ order.shipping_address.postal_code }}</p>
                        <p v-if="order.shipping_address.country" class="mt-1">{{ order.shipping_address.country }}</p>
                        <p v-if="order.shipping_address.phone" class="mt-2 flex items-center gap-2">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
                                <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z" clip-rule="evenodd" />
                            </svg>
                            {{ order.shipping_address.phone }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ProfileLayout>
</template>