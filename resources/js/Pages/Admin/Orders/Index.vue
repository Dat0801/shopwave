<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">
                Orders
            </h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="orders.data.length"
                    class="overflow-x-auto rounded-lg bg-white shadow-sm"
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
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-700">
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
                <p
                    v-else
                    class="text-sm text-gray-500"
                >
                    No orders yet.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

