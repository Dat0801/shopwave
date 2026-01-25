<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    coupons: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'All');
const type = ref(props.filters.type || 'All');

const deleteCoupon = (coupon) => {
    if (confirm('Are you sure you want to delete this coupon?')) {
        router.delete(route('admin.coupons.destroy', coupon.id));
    }
};

const formatDate = (date) => {
    if (!date) return 'No expiry';
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const getStatusBadge = (coupon) => {
    if (!coupon.is_active) return { label: 'Draft', class: 'bg-gray-100 text-gray-700' };
    const now = new Date();
    const start = coupon.starts_at ? new Date(coupon.starts_at) : null;
    const end = coupon.expires_at ? new Date(coupon.expires_at) : null;

    if (end && end < now) return { label: 'Expired', class: 'bg-red-50 text-red-700' };
    if (start && start > now) return { label: 'Scheduled', class: 'bg-blue-50 text-blue-700' };
    return { label: 'Active', class: 'bg-green-50 text-green-700' };
};

const getDurationLabel = (coupon) => {
    const now = new Date();
    const start = coupon.starts_at ? new Date(coupon.starts_at) : null;
    const end = coupon.expires_at ? new Date(coupon.expires_at) : null;

    let text = 'Ongoing';
    let subtext = 'NO EXPIRY';

    if (start && end) {
        text = `${formatDate(start)} - ${formatDate(end)}`;
        if (now < start) subtext = 'STARTS SOON';
        else if (now > end) subtext = 'EXPIRED';
        else subtext = 'ACTIVE PERIOD';
    } else if (start) {
        text = `From ${formatDate(start)}`;
        if (now < start) subtext = 'STARTS SOON';
        else subtext = 'ONGOING';
    } else if (end) {
        text = `Until ${formatDate(end)}`;
        if (now > end) subtext = 'EXPIRED';
        else subtext = 'ENDS SOON';
    }

    return { text, subtext };
};

const getUsagePercentage = (coupon) => {
    if (!coupon.usage_limit) return 0;
    return Math.min(100, (coupon.used_count / coupon.usage_limit) * 100);
};

// Search and filter watchers
watch([search, status, type], debounce(() => {
    router.get(route('admin.coupons.index'), {
        search: search.value,
        status: status.value,
        type: type.value,
    }, { preserveState: true, replace: true });
}, 300));

const paginationLinks = computed(() => {
    return props.coupons.links;
});
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Coupons" />

        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Coupon Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">Manage and track your promotional offers and discount codes.</p>
                </div>
                <Link :href="route('admin.coupons.create')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Coupon
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500">Active Coupons</p>
                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">+5%</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.active_coupons }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500">Total Redemptions</p>
                        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">-2%</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_redemptions.toLocaleString() }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500">Revenue Saved</p>
                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">+12%</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatCurrency(stats.revenue_saved) }}</p>
                </div>
            </div>

            <!-- Table Section -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <!-- Filters -->
                <div class="flex flex-col gap-4 border-b border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative max-w-sm flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            v-model="search"
                            class="block w-full rounded-lg border-0 bg-gray-50 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            placeholder="Search by coupon code..."
                        >
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Status:</label>
                            <select v-model="status" class="rounded-lg border-0 bg-gray-50 py-1.5 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                                <option>All</option>
                                <option>Active</option>
                                <option>Scheduled</option>
                                <option>Expired</option>
                                <option>Draft</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Type:</label>
                            <select v-model="type" class="rounded-lg border-0 bg-gray-50 py-1.5 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                                <option>All</option>
                                <option value="fixed">Fixed Amount</option>
                                <option value="percent">Percentage</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Coupon Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Value</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Duration</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Usage</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-gray-50 transition-colors">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            {{ coupon.code }}
                                        </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ coupon.type === 'fixed' ? 'Fixed Amount' : 'Percentage' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">
                                    {{ coupon.type === 'fixed' ? formatCurrency(coupon.value) + ' Off' : parseInt(coupon.value) + '% Off' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ getDurationLabel(coupon).text }}</span>
                                        <span class="text-xs text-gray-500 uppercase">{{ getDurationLabel(coupon).subtext }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-medium text-gray-900 w-8">{{ coupon.used_count }}</span>
                                        <div class="h-1.5 w-24 rounded-full bg-gray-100 overflow-hidden">
                                            <div class="h-full bg-blue-600 rounded-full" :style="{ width: getUsagePercentage(coupon) + '%' }"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">/ {{ coupon.usage_limit || '∞' }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="getStatusBadge(coupon).class">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full" :class="getStatusBadge(coupon).class.replace('bg-', 'bg-current ').split(' ')[0] === 'bg-green-50' ? 'bg-green-600' : (getStatusBadge(coupon).class.includes('red') ? 'bg-red-600' : (getStatusBadge(coupon).class.includes('blue') ? 'bg-blue-600' : 'bg-gray-600'))"></span>
                                        {{ getStatusBadge(coupon).label }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('admin.coupons.edit', coupon.id)" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </Link>
                                        <button @click="deleteCoupon(coupon)" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-red-600">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="coupons.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No coupons found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6" v-if="coupons.meta && coupons.meta.links">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ coupons.meta.from }}</span>
                                to
                                <span class="font-medium">{{ coupons.meta.to }}</span>
                                of
                                <span class="font-medium">{{ coupons.meta.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link
                                    v-for="(link, index) in coupons.meta.links"
                                    :key="index"
                                    :href="link.url || '#'"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0"
                                    :class="[
                                        link.active ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                        !link.url ? 'text-gray-400 cursor-not-allowed' : '',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index === coupons.meta.links.length - 1 ? 'rounded-r-md' : ''
                                    ]"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
