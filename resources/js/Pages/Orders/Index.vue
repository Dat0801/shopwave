<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
});
</script>

<template>
    <ShopLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Your orders</h1>
            <p class="mt-1 text-sm text-gray-500">
                View your recent orders and their status.
            </p>
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
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-500">
                            {{ order.status }}
                        </p>
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

