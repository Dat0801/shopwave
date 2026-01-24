<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    from: props.filters?.from || '',
    to: props.filters?.to || '',
});

const submitFilters = () => {
    filterForm.get(route('admin.customers.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['customers', 'filters'],
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Customer Management" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                        <span>Admin</span>
                        <span>/</span>
                        <span>Customer Management</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Customer Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage and monitor your e-commerce customer base
                    </p>
                </div>
                <div class="flex gap-3">
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export
                    </button>
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Add Customer
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="filterForm.search"
                            @keydown.enter="submitFilters"
                            type="text"
                            class="block w-full rounded-lg border-gray-200 bg-gray-50 py-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                            placeholder="Search by name, email, or customer ID..."
                        />
                    </div>
                    <div class="flex gap-4">
                        <select
                            v-model="filterForm.status"
                            class="rounded-lg border-gray-200 bg-gray-50 py-2.5 pl-3 pr-10 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        >
                            <option value="">Status: All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        
                        <select
                            class="rounded-lg border-gray-200 bg-gray-50 py-2.5 pl-3 pr-10 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        >
                            <option value="">Date Joined: Any time</option>
                        </select>

                        <button class="p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-sm">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-4">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-gray-500">
                                    Customer
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-gray-500">
                                    Email Address
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-gray-500">
                                    Total Orders
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-gray-500">
                                    Total Spent
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-gray-500">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-gray-500 text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="customer in customers.data" :key="customer.id" class="group hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full bg-gray-100">
                                            <img 
                                                :src="customer.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(customer.name)}&background=random&color=fff`" 
                                                alt="" 
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ customer.name }}</div>
                                            <div class="text-xs text-gray-500">Joined {{ formatDate(customer.created_at) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ customer.email }}
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-medium">
                                    {{ customer.orders_count }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    {{ formatCurrency(customer.total_spent) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        Active
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
                            <tr v-if="customers.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No customers found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-gray-100 px-6 py-4 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ customers.from || 0 }}</span> to <span class="font-medium">{{ customers.to || 0 }}</span> of <span class="font-medium">{{ customers.total }}</span> customers
                    </p>
                    <div class="flex gap-2">
                        <template v-for="(link, key) in customers.links" :key="key">
                            <div
                                v-if="link.url === null"
                                class="relative inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-300"
                                v-html="link.label"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                :class="[
                                    'relative inline-flex items-center rounded-lg border px-4 py-2 text-sm font-medium focus:z-20',
                                    link.active
                                        ? 'z-10 border-blue-600 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                                        : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:border-gray-300'
                                ]"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
