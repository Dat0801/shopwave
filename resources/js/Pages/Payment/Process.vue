<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { CheckCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    order: Object,
    payment: Object,
    clientSecret: String,
    stripePublicKey: String,
    amount: Number,
    currency: String,
    csrfToken: String,
});

const stripe = ref(null);
const elements = ref(null);
const cardElement = ref(null);
const processing = ref(false);
const paymentStatus = ref('processing'); // processing, success, error
const errorMessage = ref('');

onMounted(async () => {
    if (!props.stripePublicKey) {
        errorMessage.value = 'Payment system not configured. Please contact support.';
        paymentStatus.value = 'error';
        return;
    }

    stripe.value = await loadStripe(props.stripePublicKey);

    if (!stripe.value) {
        errorMessage.value = 'Failed to load payment system. Please refresh the page.';
        paymentStatus.value = 'error';
        return;
    }

    elements.value = stripe.value.elements();
    cardElement.value = elements.value.create('card');
    cardElement.value.mount('#card-element');
});

const handleSubmit = async () => {
    processing.value = true;
    errorMessage.value = '';

    if (!stripe.value || !cardElement.value) {
        errorMessage.value = 'Payment system not loaded. Please refresh the page.';
        paymentStatus.value = 'error';
        processing.value = false;
        return;
    }

    if (!props.clientSecret) {
        errorMessage.value = 'Payment session missing. Please restart checkout.';
        paymentStatus.value = 'error';
        processing.value = false;
        return;
    }

    const billingName = props.order?.user?.name
        ?? [props.order?.shipping_address?.first_name, props.order?.shipping_address?.last_name]
            .filter(Boolean)
            .join(' ');

    const billingEmail = props.order?.user?.email ?? null;

    try {
        const { error, paymentIntent } = await stripe.value.confirmCardPayment(
            props.clientSecret,
            {
                payment_method: {
                    card: cardElement.value,
                    billing_details: {
                        name: billingName || undefined,
                        email: billingEmail || undefined,
                    },
                },
            }
        );

        if (error) {
            errorMessage.value = error.message;
            paymentStatus.value = 'error';
        } else if (paymentIntent.status === 'succeeded') {
            paymentStatus.value = 'success';
            
            // Confirm payment on server
            if (!props.csrfToken) {
                throw new Error('Security token missing. Please refresh the page.');
            }

            await fetch(route('payments.confirm'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': props.csrfToken,
                },
                body: JSON.stringify({
                    payment_id: props.payment.id,
                }),
            });
        }
    } catch (error) {
        errorMessage.value = error instanceof Error
            ? error.message
            : 'Payment processing failed. Please try again.';
        paymentStatus.value = 'error';
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <ShopLayout>
        <Head title="Payment Processing" />

        <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow">
                <!-- Success State -->
                <div v-if="paymentStatus === 'success'" class="p-6 text-center">
                    <CheckCircleIcon class="w-16 h-16 text-green-500 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
                    <p class="text-gray-600 mb-6">
                        Your order #{{ order.id }} has been confirmed. You'll receive a confirmation email shortly.
                    </p>
                    <Link
                        :href="route('orders.show', order.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        View Order Details
                    </Link>
                </div>

                <!-- Error State -->
                <div v-else-if="paymentStatus === 'error'" class="p-6 text-center">
                    <ExclamationTriangleIcon class="w-16 h-16 text-red-500 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h2>
                    <p class="text-gray-600 mb-4">{{ errorMessage }}</p>
                    <button
                        @click="paymentStatus = 'processing'; errorMessage = ''"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Try Again
                    </button>
                </div>

                <!-- Processing State -->
                <div v-else class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Complete Payment</h2>

                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">Order #{{ order.id }}</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ (amount / 100).toFixed(2) }} {{ currency.toUpperCase() }}
                        </p>
                    </div>

                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Card Details
                            </label>
                            <div id="card-element" class="border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500"></div>
                        </div>

                        <button
                            type="submit"
                            :disabled="processing"
                            class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ processing ? 'Processing...' : 'Pay Now' }}
                        </button>

                        <Link
                            :href="route('orders.index')"
                            class="block text-center text-sm text-gray-600 hover:text-gray-900 mt-4"
                        >
                            Back to Orders
                        </Link>
                    </form>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
