<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

defineProps({
    orderId: Number,
    message: String,
});
</script>

<template>
    <ShopLayout>
        <Head title="Payment Error" />

        <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6 text-center">
                <ExclamationTriangleIcon class="w-16 h-16 text-yellow-500 mx-auto mb-4" />
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Something Went Wrong</h2>
                <p class="text-gray-600 mb-6">{{ message }}</p>
                <div class="space-y-3">
                    <Link
                        :href="route('orders.show', orderId)"
                        class="block bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700"
                    >
                        Back to Order
                    </Link>
                    <Link
                        :href="route('checkout.index')"
                        class="block bg-gray-200 text-gray-900 font-semibold py-2 rounded-lg hover:bg-gray-300"
                    >
                        Try Checkout Again
                    </Link>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
