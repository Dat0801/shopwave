<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    period: props.filters?.period || '30_days', // Default to 30 days if not present
});

const submitFilters = () => {
    filterForm.get(route('orders.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'filters'],
    });
};

// Debounce search
let timeout;
watch(() => filterForm.search, () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        submitFilters();
    }, 500);
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
</script>

<template>
    <Head title="Order History" />

    <ProfileLayout>
        <template #breadcrumb>
            <span class="text-gray-900 font-medium">Order History</span>
        </template>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Order History</h1>
                <p class="mt-1 text-gray-500">Review and manage your past orders</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0011.378 2H4.5zm2.25 8.5a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 3a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                </svg>
                Export Receipts
            </button>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6 rounded-2xl bg-white p-2 shadow-sm ring-1 ring-gray-100">
            <div class="flex flex-col md:flex-row gap-2">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        v-model="filterForm.search"
                        type="text"
                        placeholder="Search by Order ID or product name..."
                        class="block w-full rounded-xl border-0 bg-transparent py-2.5 pl-10 pr-3 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                    />
                </div>
                <div class="flex items-center gap-2 border-t md:border-t-0 md:border-l border-gray-100 p-1 md:pl-2">
                    <select
                        v-model="filterForm.status"
                        @change="submitFilters"
                        class="rounded-lg border-0 bg-gray-50 py-2 pl-3 pr-8 text-xs font-medium text-gray-700 focus:ring-2 focus:ring-blue-600"
                    >
                        <option value="">All Orders</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select
                        v-model="filterForm.period"
                        @change="submitFilters"
                        class="rounded-lg border-0 bg-gray-50 py-2 pl-3 pr-8 text-xs font-medium text-gray-700 focus:ring-2 focus:ring-blue-600"
                    >
                        <option value="30_days">Last 30 Days</option>
                        <option value="3_months">Past 3 Months</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Order List -->
        <div v-if="orders.data.length" class="space-y-6">
            <article
                v-for="order in orders.data"
                :key="order.id"
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition-shadow hover:shadow-md"
            >
                <!-- Order Header -->
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap gap-x-8 gap-y-2">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Order ID</p>
                                <p class="mt-0.5 text-sm font-bold text-gray-900">#{{ order.id }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Placed On</p>
                                <p class="mt-0.5 text-sm font-medium text-gray-900">
                                    {{ new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Status</p>
                                <div class="mt-0.5">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold"
                                        :class="getStatusColor(order.status)"
                                    >
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ getStatusLabel(order.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total</p>
                            <p class="mt-0.5 text-xl font-bold text-gray-900">${{ order.total_price }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Content -->
                <div class="p-6">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Product Images (First 2 + Counter) -->
                            <div class="flex -space-x-3 overflow-hidden p-1">
                                <template v-for="(item, index) in order.items.slice(0, 2)" :key="item.id">
                                    <div class="relative inline-block h-16 w-16 rounded-lg border-2 border-white bg-gray-100 shadow-sm">
                                        <img 
                                            v-if="item.product?.image"
                                            :src="`/storage/${item.product.image}`" 
                                            :alt="item.product?.name"
                                            class="h-full w-full object-cover rounded-md"
                                        />
                                        <div v-else class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-400">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                                <div v-if="order.items.length > 2" class="relative inline-flex h-16 w-16 items-center justify-center rounded-lg border-2 border-white bg-gray-50 shadow-sm">
                                    <span class="text-xs font-bold text-gray-500">+{{ order.items.length - 2 }}</span>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">
                                    {{ order.items[0]?.product?.name || 'Unknown Product' }}
                                    <span v-if="order.items.length > 1" class="font-normal text-gray-500">
                                        + {{ order.items.length - 1 }} items
                                    </span>
                                </h4>
                                <p class="mt-1 text-xs text-gray-500" v-if="order.status === 'delivered'">
                                    Delivered on {{ new Date(order.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500" v-else-if="order.status === 'in_transit'">
                                    Expected delivery: {{ new Date(Date.now() + 86400000 * 3).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                            >
                                View Details
                            </button>
                            
                            <button 
                                v-if="order.status === 'delivered' || order.status === 'completed'"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                Buy Again
                            </button>
                            <button 
                                v-else-if="order.status === 'in_transit' || order.status === 'shipped'"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                Track Package
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-white rounded-2xl border border-gray-100">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 mb-4">
                <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No orders found</h3>
            <p class="mt-1 text-gray-500">We couldn't find any orders matching your criteria.</p>
            <button 
                @click="filterForm.reset()"
                class="mt-4 text-blue-600 font-medium hover:underline"
            >
                Clear Filters
            </button>
        </div>
    </ProfileLayout>
</template>
