<script setup>
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    charts: Object,
});

// Mock data for visual consistency with the design request
const topCategories = [
    { name: 'Electronics', percentage: 42, color: 'bg-blue-600' },
    { name: 'Fashion & Apparel', percentage: 28, color: 'bg-blue-400' },
    { name: 'Home Decor', percentage: 18, color: 'bg-purple-400' },
    { name: 'Beauty', percentage: 12, color: 'bg-green-400' },
];

const recentOrders = [
    { id: '#ORD-7742', customer: 'Sarah Jenkins', avatar: 'https://ui-avatars.com/api/?name=Sarah+Jenkins&background=random', amount: '$249.00', status: 'Shipped', statusColor: 'bg-green-100 text-green-700', date: 'Oct 24, 2023' },
    { id: '#ORD-7741', customer: 'Michael Chen', avatar: 'https://ui-avatars.com/api/?name=Michael+Chen&background=random', amount: '$1,290.50', status: 'Processing', statusColor: 'bg-blue-100 text-blue-700', date: 'Oct 24, 2023' },
    { id: '#ORD-7740', customer: 'Elena Rodriguez', avatar: 'https://ui-avatars.com/api/?name=Elena+Rodriguez&background=random', amount: '$84.20', status: 'Pending', statusColor: 'bg-orange-100 text-orange-700', date: 'Oct 23, 2023' },
    { id: '#ORD-7739', customer: 'Tom Wilson', avatar: 'https://ui-avatars.com/api/?name=Tom+Wilson&background=random', amount: '$412.00', status: 'Cancelled', statusColor: 'bg-red-100 text-red-700', date: 'Oct 22, 2023' },
];

// Mock monthly data for the bar chart
const monthlyRevenue = [
    { month: 'JAN', value: 45, light: false },
    { month: 'FEB', value: 35, light: false },
    { month: 'MAR', value: 65, light: false },
    { month: 'APR', value: 30, light: true }, // Highlighted differently in design? No, looks like height.
    { month: 'MAY', value: 75, light: false },
    { month: 'JUN', value: 55, light: false },
];
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Dashboard" />
        
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol role="list" class="flex items-center space-x-2">
                            <li>
                                <div class="flex items-center">
                                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-700">Home</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                    </svg>
                                    <a href="#" class="ml-2 text-sm font-medium text-gray-900 hover:text-gray-700">Dashboard Overview</a>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900">Dashboard Overview</h1>
                </div>
                <div>
                    <button type="button" class="inline-flex items-center gap-x-2 rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Data
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
                            <p class="mt-2 text-3xl font-bold text-gray-900">${{ stats?.revenue || '45,231.89' }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                             <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="flex items-center font-medium text-green-600">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            +12%
                        </span>
                        <span class="ml-2 text-gray-500">vs last month</span>
                    </div>
                </div>

                <!-- New Orders -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">New Orders</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats?.orders || '1,205' }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="flex items-center font-medium text-green-600">
                             <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            +5.2%
                        </span>
                        <span class="ml-2 text-gray-500">vs last month</span>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Active Users</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats?.users || '8,432' }}</p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                         <span class="flex items-center font-medium text-green-600">
                             <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            +8%
                        </span>
                        <span class="ml-2 text-gray-500">vs last week</span>
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
                            <p class="text-sm text-gray-500">Revenue performance for H1 2024</p>
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
                                  :style="{ height: `${item.value * 3}px` }">
                                  <!-- Tooltip -->
                                  <div class="absolute -top-10 left-1/2 hidden -translate-x-1/2 transform rounded bg-gray-900 px-2 py-1 text-xs text-white group-hover:block">
                                      ${{ item.value }}k
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
                    </div>
                    <button class="mt-8 w-full rounded-lg border border-gray-200 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        View Detailed Report
                    </button>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">View All</a>
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
