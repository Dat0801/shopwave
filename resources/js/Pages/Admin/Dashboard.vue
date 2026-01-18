<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps({
    stats: Object,
    charts: Object,
});

const ordersByDay = computed(() => props.charts?.orders_by_day || []);

const averageOrderValue = computed(() => {
    if (!props.stats || !props.stats.orders || !props.stats.revenue) {
        return '0.00';
    }

    if (props.stats.orders === 0) {
        return '0.00';
    }

    return (props.stats.revenue / props.stats.orders).toFixed(2);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin - Dashboard" />
        <template #header>
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Admin dashboard
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Quick overview of orders, revenue, users, and products.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-10 sm:px-6 lg:px-8">
                <section>
                    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
                        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                        Orders & revenue (last 7 days)
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Simple trend chart without external libraries.
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="ordersByDay.length"
                                class="mt-4"
                            >
                                <div class="h-40">
                                    <svg
                                        viewBox="0 0 200 100"
                                        class="h-full w-full text-indigo-500"
                                    >
                                        <defs>
                                            <linearGradient
                                                id="revenue-fill"
                                                x1="0"
                                                y1="0"
                                                x2="0"
                                                y2="1"
                                            >
                                                <stop
                                                    offset="0%"
                                                    stop-color="rgb(129, 140, 248)"
                                                    stop-opacity="0.3"
                                                />
                                                <stop
                                                    offset="100%"
                                                    stop-color="rgb(129, 140, 248)"
                                                    stop-opacity="0"
                                                />
                                            </linearGradient>
                                        </defs>

                                        <template
                                            v-if="Math.max(...ordersByDay.map((p) => p.revenue)) > 0"
                                        >
                                            <path
                                                :d="(() => {
                                                    const maxRevenue = Math.max(
                                                        ...ordersByDay.map((p) => p.revenue),
                                                    );
                                                    const step =
                                                        ordersByDay.length > 1
                                                            ? 200 / (ordersByDay.length - 1)
                                                            : 0;

                                                    const points = ordersByDay.map(
                                                        (point, index) => {
                                                            const x = index * step;
                                                            const y =
                                                                100 -
                                                                (point.revenue / maxRevenue) * 80 -
                                                                10;

                                                            return `${x},${y}`;
                                                        },
                                                    );

                                                    if (!points.length) {
                                                        return '';
                                                    }

                                                    const first = points[0];
                                                    const last = points[points.length - 1];

                                                    return [
                                                        `M ${first}`,
                                                        ...points.slice(1).map((p) => `L ${p}`),
                                                        `L ${last.split(',')[0]},100`,
                                                        'L 0,100',
                                                        'Z',
                                                    ].join(' ');
                                                })()"
                                                fill="url(#revenue-fill)"
                                                stroke="none"
                                            />
                                        </template>

                                        <template
                                            v-if="Math.max(...ordersByDay.map((p) => p.orders)) > 0"
                                        >
                                            <polyline
                                                :points="(() => {
                                                    const maxOrders = Math.max(
                                                        ...ordersByDay.map((p) => p.orders),
                                                    );
                                                    const step =
                                                        ordersByDay.length > 1
                                                            ? 200 / (ordersByDay.length - 1)
                                                            : 0;

                                                    return ordersByDay
                                                        .map((point, index) => {
                                                            const x = index * step;
                                                            const y =
                                                                100 -
                                                                (point.orders / maxOrders) * 80 -
                                                                10;

                                                            return `${x},${y}`;
                                                        })
                                                        .join(' ');
                                                })()"
                                                fill="none"
                                                stroke="rgb(37, 99, 235)"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </template>
                                    </svg>
                                </div>

                                <div class="mt-4 flex items-center justify-between text-[11px]">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full bg-indigo-500" />
                                        <span class="text-gray-600">Orders</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full bg-indigo-300" />
                                        <span class="text-gray-600">Revenue</span>
                                    </div>
                                </div>

                                <div class="mt-2 flex justify-between text-[10px] text-gray-400">
                                    <span
                                        v-for="point in ordersByDay"
                                        :key="point.date"
                                        class="flex-1 text-center"
                                    >
                                        {{ new Date(point.date).toLocaleDateString(undefined, {
                                            month: 'short',
                                            day: 'numeric',
                                        }) }}
                                    </span>
                                </div>
                            </div>

                            <p
                                v-else
                                class="mt-4 text-xs text-gray-400"
                            >
                                No orders in the last 7 days to display.
                            </p>
                        </div>

                        <div class="space-y-3 text-xs text-gray-500">
                            <p>
                                The chart shows how many orders you received and how much revenue
                                you generated each day over the last week.
                            </p>
                            <p>
                                Use it alongside the summary cards below to quickly spot trends,
                                busy days, or slow periods.
                            </p>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Orders
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        {{ stats.orders }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M5 12L10 17L19 8"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Revenue
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        ${{ stats.revenue }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M12 5V19M8 9L12 5L16 9M8 15L12 19L16 15"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Users
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        {{ stats.users }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-sky-600"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M5 20C5 16.6863 7.68629 14 11 14H13C16.3137 14 19 16.6863 19 20"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="3"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Avg. order value
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        ${{ averageOrderValue }}
                                    </p>
                                </div>
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M4 12H20M4 8H14M4 16H12"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Products
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        {{ stats.products_total }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Total products in the catalog
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Active products
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        {{ stats.products_active }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Visible and available for customers
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Out of stock
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        {{ stats.products_out_of_stock }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Products with zero inventory
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                    >
                                        Total stock
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold text-gray-900"
                                    >
                                        {{ stats.products_total_stock }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Units of inventory across all products
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
