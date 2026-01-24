<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    orders: Object,
    filters: Object,
});

// Mock stats for the UI
const stats = [
    { 
        title: 'Total Orders Today', 
        value: '145', 
        trend: '+12%', 
        trendUp: true, 
        icon: 'document',
        iconBg: 'bg-blue-50',
        iconColor: 'text-blue-600'
    },
    { 
        title: 'Pending Shipments', 
        value: '28', 
        trend: 'Requires action', 
        trendWarning: true, 
        icon: 'truck',
        iconBg: 'bg-orange-50',
        iconColor: 'text-orange-600'
    },
    { 
        title: 'Revenue Today', 
        value: '$12,450.00', 
        trend: '+8%', 
        trendUp: true, 
        icon: 'cash',
        iconBg: 'bg-emerald-50',
        iconColor: 'text-emerald-600'
    },
];

const filterForm = useForm({
    status: props.filters?.status || '',
    from: props.filters?.from || '',
    to: props.filters?.to || '',
    search: props.filters?.search || '',
});

const submitFilters = () => {
    filterForm.get(route('admin.orders.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'filters'],
    });
};

const getStatusColor = (status) => {
    switch (status) {
        case 'shipped':
            return 'bg-blue-100 text-blue-700';
        case 'pending':
        case 'processing':
            return 'bg-orange-100 text-orange-800';
        case 'completed':
        case 'paid':
        case 'delivered':
            return 'bg-emerald-100 text-emerald-700';
        case 'cancelled':
            return 'bg-rose-100 text-rose-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
};

const getStatusLabel = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Order Management" />
        
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Order Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Monitor and process your platform's customer orders.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export CSV
                    </button>
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Order
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(stat, index) in stats" :key="index" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ stat.title }}</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stat.value }}</p>
                        </div>
                        <div :class="['rounded-lg p-3', stat.iconBg]">
                            <svg v-if="stat.icon === 'document'" :class="['h-6 w-6', stat.iconColor]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <svg v-else-if="stat.icon === 'truck'" :class="['h-6 w-6', stat.iconColor]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <svg v-else-if="stat.icon === 'cash'" :class="['h-6 w-6', stat.iconColor]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span v-if="stat.trendUp" class="flex items-center font-medium text-green-600">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            {{ stat.trend }}
                        </span>
                        <span v-else-if="stat.trendWarning" class="flex items-center font-medium text-orange-600">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            {{ stat.trend }}
                        </span>
                        <span class="ml-2 text-gray-500">from yesterday</span>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Filter Orders</h2>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4 items-end">
                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">
                            Status
                        </label>
                        <select
                            v-model="filterForm.status"
                            class="block w-full rounded-lg border-gray-300 bg-gray-50 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="paid">Paid</option>
                            <option value="shipped">Shipped</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">
                            Date Range
                        </label>
                        <div class="flex gap-2">
                            <div class="relative w-full">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input
                                    v-model="filterForm.from"
                                    type="date"
                                    class="block w-full rounded-lg border-gray-300 bg-gray-50 py-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                    placeholder="From"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Search -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">
                            Quick Search
                        </label>
                        <div class="relative">
                            <input
                                v-model="filterForm.search"
                                type="text"
                                class="block w-full rounded-lg border-gray-300 bg-gray-50 py-2.5 px-4 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                                placeholder="Customer or ID..."
                            />
                        </div>
                    </div>

                    <!-- Apply Button -->
                    <div>
                        <button
                            @click="submitFilters"
                            class="w-full rounded-lg bg-blue-100 px-4 py-2.5 text-sm font-semibold text-blue-600 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-sm">
                        <thead class="bg-white border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-blue-600">
                                    Order ID
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-blue-600">
                                    Customer
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-blue-600">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-blue-600">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-blue-600">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-blue-600 text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="order in orders.data" :key="order.id" class="group hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <Link :href="route('admin.orders.show', order.id)" class="font-bold text-blue-600 hover:text-blue-800">
                                        #ORD-{{ order.id }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 flex-shrink-0 overflow-hidden rounded-full bg-gray-100">
                                            <img 
                                                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(order.user?.name || 'Guest')}&background=random&color=fff`" 
                                                alt="" 
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ order.user?.name || 'Guest Customer' }}</div>
                                            <div class="text-xs text-gray-500">{{ order.user?.email || 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ new Date(order.created_at).toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    ${{ order.total_price }}
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold"
                                        :class="getStatusColor(order.status)"
                                    >
                                        {{ getStatusLabel(order.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-gray-400 hover:text-blue-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No orders found matching your filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="orders.links && orders.data.length > 0" class="flex items-center justify-between border-t border-gray-100 bg-white px-6 py-4">
                    <div class="text-xs text-gray-500">
                        Showing <span class="font-medium">{{ orders.from }}</span> to <span class="font-medium">{{ orders.to }}</span> of <span class="font-medium">{{ orders.total }}</span> orders
                    </div>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, k) in orders.links"
                            :key="k"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="flex h-8 min-w-[2rem] items-center justify-center rounded px-2 text-xs font-medium transition-colors"
                            :class="{
                                'bg-blue-600 text-white': link.active,
                                'text-gray-600 hover:bg-gray-100': !link.active && link.url,
                                'cursor-not-allowed text-gray-300': !link.url
                            }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
