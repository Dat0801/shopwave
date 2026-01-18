<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const filterForm = useForm({
    status: props.filters?.status || '',
    from: props.filters?.from || '',
    to: props.filters?.to || '',
});

const submitFilters = () => {
    filterForm.get(route('admin.orders.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'filters'],
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin - Orders" />
        <template #header>
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Orders
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Track and manage customer orders.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="w-full sm:w-48">
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                Status
                            </label>
                            <select
                                v-model="filterForm.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    All statuses
                                </option>
                                <option value="pending">
                                    Pending
                                </option>
                                <option value="processing">
                                    Processing
                                </option>
                                <option value="paid">
                                    Paid
                                </option>
                                <option value="shipped">
                                    Shipped
                                </option>
                                <option value="completed">
                                    Completed
                                </option>
                                <option value="cancelled">
                                    Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="w-full sm:w-40">
                            <Input
                                v-model="filterForm.from"
                                type="date"
                                label="From"
                            />
                        </div>

                        <div class="w-full sm:w-40">
                            <Input
                                v-model="filterForm.to"
                                type="date"
                                label="To"
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <Button
                                type="button"
                                class="px-4"
                                @click="submitFilters"
                            >
                                Apply
                            </Button>
                            <button
                                type="button"
                                class="text-xs text-gray-500 hover:text-gray-900"
                                @click="
                                    filterForm.status = '';
                                    filterForm.from = '';
                                    filterForm.to = '';
                                    submitFilters();
                                "
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    v-if="orders.data.length"
                    class="space-y-4"
                >
                    <div
                        class="hidden overflow-x-auto rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 md:block"
                    >
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b text-xs uppercase tracking-wide text-gray-500">
                                    <th class="px-3 py-2">
                                        ID
                                    </th>
                                    <th class="px-3 py-2">
                                        Customer
                                    </th>
                                    <th class="px-3 py-2">
                                        Total
                                    </th>
                                    <th class="px-3 py-2">
                                        Status
                                    </th>
                                    <th class="px-3 py-2">
                                        Created
                                    </th>
                                    <th class="px-3 py-2 text-right">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="order in orders.data"
                                    :key="order.id"
                                    class="border-b last:border-b-0"
                                >
                                    <td class="px-3 py-2">
                                        #{{ order.id }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ order.user?.name }}
                                    </td>
                                    <td class="px-3 py-2">
                                        ${{ order.total_price }}
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                            :class="order.status === 'completed' || order.status === 'paid'
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : order.status === 'cancelled'
                                                    ? 'bg-rose-50 text-rose-700'
                                                    : 'bg-gray-100 text-gray-700'"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-500">
                                        {{ new Date(order.created_at).toLocaleString() }}
                                    </td>
                                    <td class="px-3 py-2 text-right text-xs">
                                        <Link
                                            :href="route('admin.orders.show', order.id)"
                                            class="text-indigo-600 hover:text-indigo-800"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3 md:hidden">
                        <div
                            v-for="order in orders.data"
                            :key="order.id"
                            class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        #{{ order.id }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ order.user?.name || 'Guest customer' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">
                                        ${{ order.total_price }}
                                    </p>
                                    <p class="mt-1 text-[11px] text-gray-500">
                                        {{ new Date(order.created_at).toLocaleString() }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                    :class="order.status === 'completed' || order.status === 'paid'
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : order.status === 'cancelled'
                                            ? 'bg-rose-50 text-rose-700'
                                            : 'bg-gray-100 text-gray-700'"
                                >
                                    {{ order.status }}
                                </span>
                                <Link
                                    :href="route('admin.orders.show', order.id)"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    View details
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <p
                    v-else
                    class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm ring-1 ring-gray-100"
                >
                    No orders yet.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
