<script setup>
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    charts: Object,
    topCategories: Array,
    recentOrders: Array,
    topProducts: Array,
});

const topCategories = computed(() => props.topCategories || []);
const recentOrders = computed(() => props.recentOrders || []);
const topProducts = computed(() => props.topProducts || []);
const monthlyRevenue = computed(() => props.charts?.monthly_revenue || []);
const dailyOrders = computed(() => props.charts?.daily_orders || []);

const maxRevenue = computed(() => {
    if (monthlyRevenue.value.length === 0) return 1;
    return Math.max(...monthlyRevenue.value.map(item => item.value));
});

const maxDailyOrders = computed(() => {
    if (dailyOrders.value.length === 0) return 1;
    return Math.max(...dailyOrders.value.map(item => item.value));
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-US').format(value);
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Dashboard" />
        
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Home', href: route('shop.index') },
                            { label: 'Admin', href: route('admin.dashboard') },
                            { label: 'Dashboard' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">
                        Dashboard
                    </h1>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Last updated: just now</span>
                    <button class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Report
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Total Sales -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Sales</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatCurrency(stats?.revenue || 0) }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm" v-if="stats?.growth?.revenue !== undefined">
                        <span class="flex items-center font-medium" :class="stats.growth.revenue >= 0 ? 'text-green-600' : 'text-red-600'">
                             <svg v-if="stats.growth.revenue >= 0" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <svg v-else class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            {{ Math.abs(stats.growth.revenue) }}%
                        </span>
                        <span class="ml-2 text-gray-500">vs last month</span>
                    </div>
                </div>

                <!-- New Orders -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Orders</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatNumber(stats?.orders || 0) }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm" v-if="stats?.growth?.orders !== undefined">
                        <span class="flex items-center font-medium" :class="stats.growth.orders >= 0 ? 'text-green-600' : 'text-red-600'">
                             <svg v-if="stats.growth.orders >= 0" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <svg v-else class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            {{ Math.abs(stats.growth.orders) }}%
                        </span>
                        <span class="ml-2 text-gray-500">vs last month</span>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatNumber(stats?.users || 0) }}</p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm" v-if="stats?.growth?.users !== undefined">
                         <span class="flex items-center font-medium" :class="stats.growth.users >= 0 ? 'text-green-600' : 'text-red-600'">
                             <svg v-if="stats.growth.users >= 0" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <svg v-else class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            {{ Math.abs(stats.growth.users) }}%
                        </span>
                        <span class="ml-2 text-gray-500">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Monthly Revenue Chart -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Monthly Revenue</h3>
                            <p class="text-sm text-gray-500">Revenue performance for last 6 months</p>
                        </div>
                        <button class="rounded-md bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100">
                            Last 6 Months
                        </button>
                    </div>
                    <div class="mt-8 flex h-64 items-end justify-between gap-4 px-4">
                         <!-- Simple CSS Bar Chart -->
                         <div v-for="item in monthlyRevenue" :key="item.month" class="group flex w-full flex-col items-center gap-2">
                             <div class="relative w-full max-w-[60px] rounded-t-sm transition-all duration-300 hover:opacity-80" 
                                  :class="item.month === 'APR' || item.month === 'FEB' ? 'bg-blue-200' : 'bg-blue-600'"
                                  :style="{ height: `${(item.value / maxRevenue) * 100}%` }">
                                  <!-- Tooltip -->
                                  <div class="absolute -top-10 left-1/2 hidden -translate-x-1/2 transform rounded bg-gray-900 px-2 py-1 text-xs text-white group-hover:block whitespace-nowrap z-10">
                                      {{ formatCurrency(item.value) }}
                                  </div>
                             </div>
                             <span class="text-xs font-medium text-gray-500">{{ item.month }}</span>
                         </div>
                    </div>
                </div>

                <!-- Top Categories -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Top Categories</h3>
                    <div class="mt-6 space-y-6">
                        <div v-for="category in topCategories" :key="category.name">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ category.name }}</span>
                                <span class="font-bold text-gray-900">{{ category.percentage }}%</span>
                            </div>
                            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full" :class="category.color" :style="{ width: `${category.percentage}%` }"></div>
                            </div>
                        </div>
                        <div v-if="topCategories.length === 0" class="text-center text-sm text-gray-500 py-4">
                            No sales data yet.
                        </div>
                    </div>
                    <button class="mt-8 w-full rounded-lg border border-gray-200 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        View Detailed Report
                    </button>
                </div>
            </div>

            <!-- New Row: Daily Orders & Top Products -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Daily Orders Chart -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daily Orders</h3>
                            <p class="text-sm text-gray-500">Orders trend for last 30 days</p>
                        </div>
                    </div>
                    <div class="mt-8 flex h-64 items-end justify-between gap-1">
                         <div v-for="(item, index) in dailyOrders" :key="item.date" class="group flex w-full flex-col items-center gap-1">
                             <div class="relative w-full rounded-t-sm bg-indigo-500 transition-all duration-300 hover:opacity-80" 
                                  :class="item.value === 0 ? 'h-0.5 bg-gray-100' : ''"
                                  :style="{ height: item.value > 0 ? `${(item.value / maxDailyOrders) * 100}%` : '4px' }">
                                  <!-- Tooltip -->
                                  <div class="absolute -top-10 left-1/2 hidden -translate-x-1/2 transform rounded bg-gray-900 px-2 py-1 text-xs text-white group-hover:block whitespace-nowrap z-10 shadow-lg">
                                      {{ item.value }} orders <span class="text-gray-400">|</span> {{ item.date }}
                                  </div>
                             </div>
                             <!-- Show date only for every 5th item or first/last to avoid clutter -->
                             <span v-if="index % 5 === 0 || index === dailyOrders.length - 1" class="text-[10px] text-gray-400">{{ item.date }}</span>
                         </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Top Products</h3>
                    <div class="mt-6 space-y-5">
                        <div v-for="product in topProducts" :key="product.id" class="flex items-center gap-4">
                            <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 border border-gray-200">
                                <img v-if="product.image" :src="product.image" :alt="product.name" class="h-full w-full object-cover" />
                                <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900" :title="product.name">{{ product.name }}</p>
                                <p class="text-xs text-gray-500">{{ formatCurrency(product.price) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">{{ product.sold }}</p>
                                <p class="text-xs text-gray-500">sold</p>
                            </div>
                        </div>
                        <div v-if="topProducts.length === 0" class="text-center text-sm text-gray-500 py-4">
                            No sales data yet.
                        </div>
                    </div>
                    <Link :href="route('admin.products.index')" class="mt-6 block w-full rounded-lg border border-gray-200 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                        View All Products
                    </Link>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                    <Link :href="route('admin.orders.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500">View All</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 font-medium uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 font-medium uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 font-medium uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 font-medium uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 font-medium uppercase tracking-wider text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="order in recentOrders" :key="order.id" class="group hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ order.id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img :src="order.avatar" alt="" class="h-8 w-8 rounded-full" />
                                        <span class="font-medium text-gray-700">{{ order.customer }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ order.amount }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="order.statusColor">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ order.date }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
