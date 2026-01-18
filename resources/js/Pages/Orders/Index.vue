<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
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
    filterForm.get(route('orders.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'filters'],
    });
};
</script>

<template>
    <ShopLayout>
        <Head title="Your orders" />
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Your orders</h1>
            <p class="mt-1 text-sm text-gray-500">
                View your recent orders and their status.
            </p>
        </div>

        <div class="mb-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <div class="flex flex-wrap items-end gap-3">
                <div class="w-full min-[420px]:w-40">
                    <label class="mb-1 block text-xs font-medium text-gray-500">
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

                <div class="w-full min-[420px]:w-40">
                    <Input
                        v-model="filterForm.from"
                        type="date"
                        label="From"
                    />
                </div>

                <div class="w-full min-[420px]:w-40">
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
            <article
                v-for="order in orders.data"
                :key="order.id"
                class="rounded-lg border bg-white p-4 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            Order #{{ order.id }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ new Date(order.created_at).toLocaleString() }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">
                            ${{ order.total_price }}
                        </p>
                        <span
                            class="mt-1 inline-flex items-center justify-end gap-2 text-xs"
                        >
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
                        </span>
                    </div>
                </div>

                <ul class="mt-3 text-xs text-gray-600">
                    <li
                        v-for="item in order.items"
                        :key="item.id"
                        class="flex items-center justify-between"
                    >
                        <span>
                            {{ item.product?.name }} × {{ item.quantity }}
                        </span>
                        <span>
                            ${{ (item.price * item.quantity).toFixed(2) }}
                        </span>
                    </li>
                </ul>

                <div
                    v-if="order.status === 'pending'"
                    class="mt-3 text-right"
                >
                    <Link
                        :href="route('orders.cancel', order.id)"
                        method="patch"
                        as="button"
                        class="text-xs font-medium text-red-600 hover:text-red-700"
                    >
                        Cancel order
                    </Link>
                </div>
            </article>
        </div>
        <p
            v-else
            class="text-sm text-gray-500"
        >
            You do not have any orders yet.
        </p>
    </ShopLayout>
</template>
