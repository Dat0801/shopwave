<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    },
    total: {
        type: Number,
        default: 0
    },
    discount: {
        type: Number,
        default: 0
    },
    stripePublicKey: {
        type: String,
        default: ''
    },
    user: {
        type: Object,
        default: null
    },
    defaultAddress: {
        type: Object,
        default: null
    },
    addresses: {
        type: Array,
        default: () => []
    },
    paymentMethods: {
        type: Array,
        default: () => []
    }
});

// Ref for selected address and payment method
const selectedAddressId = ref(null);
const selectedPaymentMethodId = ref(null);

// Split user name into first and last name
const splitName = (fullName) => {
    if (!fullName) return { firstName: '', lastName: '' };
    const parts = fullName.trim().split(' ');
    if (parts.length === 1) {
        return { firstName: parts[0], lastName: '' };
    }
    const lastName = parts.pop();
    const firstName = parts.join(' ');
    return { firstName, lastName };
};

// Get first address or default address
const getInitialAddress = () => {
    if (props.defaultAddress) {
        return props.defaultAddress;
    }
    if (props.addresses && props.addresses.length > 0) {
        return props.addresses[0];
    }
    return null;
};

const initialAddress = getInitialAddress();

// Use default address if available, otherwise use user name
let firstName = '';
let lastName = '';

if (initialAddress) {
    selectedAddressId.value = initialAddress.id;
    const nameParts = splitName(initialAddress.name);
    firstName = nameParts.firstName;
    lastName = nameParts.lastName;
} else if (props.user) {
    const nameParts = splitName(props.user.name);
    firstName = nameParts.firstName;
    lastName = nameParts.lastName;
}

// Set default payment method
if (props.paymentMethods && props.paymentMethods.length > 0) {
    const defaultPaymentMethod = props.paymentMethods.find(pm => pm.is_default);
    selectedPaymentMethodId.value = defaultPaymentMethod ? defaultPaymentMethod.id : props.paymentMethods[0].id;
}

const form = useForm({
    email: props.user?.email || '',
    newsletter: false,
    firstName: firstName,
    lastName: lastName,
    address: initialAddress?.address_line_1 || '',
    city: initialAddress?.city || '',
    postalCode: initialAddress?.postal_code || '',
    paymentMethod: 'credit_card',
    paymentMethodId: null, // For saved payment methods
});

const couponForm = useForm({
    code: ''
});

const stripe = ref(null);
const cardElement = ref(null);
const stripeElements = ref(null);
const stripeError = ref('');
const isLoadingStripe = ref(false);

const subtotal = computed(() => {
    return props.items.reduce((sum, item) => sum + (Number(item.price) * item.quantity), 0);
});
const shipping = 0;
const taxes = computed(() => subtotal.value * 0.08); // 8% tax
const grandTotal = computed(() => Math.max(0, subtotal.value - props.discount + shipping + taxes.value));

import { getImageUrl } from '@/Utils/image';

const getProductImageUrl = (item) => {
    return getImageUrl(item.image_path || item.image, 150, 150);
};

const applyCoupon = () => {
    couponForm.post(route('cart.coupon.apply'), {
        preserveScroll: true,
        onSuccess: () => {
            couponForm.reset();
        }
    });
};

// Watch for address selection change
watch(() => selectedAddressId.value, (newAddressId) => {
    if (newAddressId) {
        const selected = props.addresses.find(addr => addr.id === newAddressId);
        if (selected) {
            const nameParts = splitName(selected.name);
            form.firstName = nameParts.firstName;
            form.lastName = nameParts.lastName;
            form.address = selected.address_line_1;
            form.city = selected.city;
            form.postalCode = selected.postal_code;
        }
    }
});

// Initialize Stripe
onMounted(async () => {
    // Only initialize if credit card selected AND no saved card selected
    if (props.stripePublicKey && form.paymentMethod === 'credit_card' && !selectedPaymentMethodId.value) {
        await initializeStripe();
    }
});

// Watch for payment method changes
watch(() => form.paymentMethod, async (newMethod) => {
    // Only initialize if credit card selected AND no saved card selected
    if (newMethod === 'credit_card' && props.stripePublicKey && !stripe.value && !selectedPaymentMethodId.value) {
        await initializeStripe();
    }
});

// Watch for saved payment method selection changes
watch(() => selectedPaymentMethodId.value, () => {
    // When selecting a saved card, no need for Stripe - card element will be hidden
    // When unselecting, Stripe will be initialized by paymentMethod watcher
});

const initializeStripe = async () => {
    try {
        isLoadingStripe.value = true;
        stripe.value = await loadStripe(props.stripePublicKey);
        
        if (stripe.value) {
            stripeElements.value = stripe.value.elements();
            
            // Create card element
            cardElement.value = stripeElements.value.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#1f2937',
                        fontFamily: 'system-ui, -apple-system, sans-serif',
                        '::placeholder': {
                            color: '#9ca3af',
                        },
                    },
                    invalid: {
                        color: '#ef4444',
                    },
                },
                hidePostalCode: true,
            });
            
            // Mount card element
            setTimeout(() => {
                const cardElementContainer = document.getElementById('card-element');
                if (cardElementContainer && cardElement.value) {
                    cardElement.value.mount('#card-element');
                    
                    // Listen for errors
                    cardElement.value.on('change', (event) => {
                        stripeError.value = event.error ? event.error.message : '';
                    });
                }
            }, 100);
        }
    } catch (error) {
        console.error('Stripe initialization error:', error);
        stripeError.value = 'Failed to load payment system. Please refresh the page.';
    } finally {
        isLoadingStripe.value = false;
    }
};

const submit = async () => {
    stripeError.value = '';
    
    // Validate required fields
    if (!form.firstName || !form.lastName || !form.email || !form.address || !form.city || !form.postalCode) {
        stripeError.value = 'Please fill in all required fields.';
        return;
    }
    
    // For non-credit card payments, submit normally
    if (form.paymentMethod !== 'credit_card') {
        form.post(route('checkout.store'));
        return;
    }
    
    // If using saved card, submit directly without Stripe
    if (selectedPaymentMethodId.value) {
        form.paymentMethodId = selectedPaymentMethodId.value;
        form.post(route('checkout.store'));
        return;
    }
    
    // For new credit card, validate with Stripe
    if (!stripe.value || !cardElement.value) {
        stripeError.value = 'Payment system not loaded. Please refresh the page.';
        return;
    }
    
    form.processing = true;
    
    try {
        // Create payment method with Stripe
        const { error, paymentMethod } = await stripe.value.createPaymentMethod({
            type: 'card',
            card: cardElement.value,
            billing_details: {
                name: `${form.firstName} ${form.lastName}`,
                email: form.email,
                address: {
                    line1: form.address,
                    city: form.city,
                    postal_code: form.postalCode,
                }
            },
        });
        
        if (error) {
            stripeError.value = error.message;
            form.processing = false;
            console.error('Stripe error:', error);
            return;
        }
        
        if (!paymentMethod) {
            stripeError.value = 'Failed to process card. Please check your card details.';
            form.processing = false;
            return;
        }
        
        // Submit form to create order (will redirect to payment processing)
        form.post(route('checkout.store'));
        
    } catch (error) {
        console.error('Payment error:', error);
        stripeError.value = 'Payment processing failed. Please try again.';
        form.processing = false;
    }
};
</script>

<template>
    <ShopLayout>
        <Head title="Checkout" />

            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-gray-500 mb-8">
                <ol class="flex items-center space-x-2">
                    <li><Link :href="route('cart.index')" class="hover:text-gray-900">Cart</Link></li>
                    <li><span class="text-gray-400">›</span></li>
                    <li><span class="font-medium text-gray-900">Checkout</span></li>
                    <li><span class="text-gray-400">›</span></li>
                    <li><span class="text-gray-400">Order Complete</span></li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
                <!-- Left Column: Forms -->
                <div class="lg:col-span-7 space-y-10">
                    
                    <!-- Global Error Alert -->
                    <div v-if="stripeError" class="rounded-md bg-red-50 border border-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ stripeError }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 1. Contact Information -->
                    <section>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3">1</div>
                            <h2 class="text-lg font-bold text-gray-900">Contact Information</h2>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" id="email" v-model="form.email" placeholder="your@email.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div class="flex items-center">
                                    <input id="newsletter" type="checkbox" v-model="form.newsletter" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="newsletter" class="ml-2 block text-sm text-gray-600">Keep me up to date on news and exclusive offers</label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Shipping Address -->
                    <section>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3">2</div>
                            <h2 class="text-lg font-bold text-gray-900">Shipping Address</h2>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                            <!-- Saved Addresses Selection -->
                            <div v-if="addresses.length > 0" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Use Saved Address</label>
                                <div class="space-y-2">
                                    <div v-for="address in addresses" :key="address.id" class="flex items-center p-3 border rounded-lg cursor-pointer" :class="selectedAddressId === address.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" :id="`address-${address.id}`" :value="address.id" v-model="selectedAddressId" class="h-4 w-4 text-blue-600">
                                        <label :for="`address-${address.id}`" class="ml-3 flex-1 cursor-pointer">
                                            <p class="font-medium text-gray-900">{{ address.label }}</p>
                                            <p class="text-sm text-gray-600">{{ address.address_line_1 }}, {{ address.city }}</p>
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-6">
                            </div>

                            <!-- Manual Address Input -->
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" id="first-name" v-model="form.firstName" placeholder="Jane" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div>
                                    <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" id="last-name" v-model="form.lastName" placeholder="Doe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" id="address" v-model="form.address" placeholder="123 Street Name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" id="city" v-model="form.city" placeholder="New York" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                                <div>
                                    <label for="postal-code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                    <input type="text" id="postal-code" v-model="form.postalCode" placeholder="10001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 3. Payment Method -->
                    <section>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3">3</div>
                            <h2 class="text-lg font-bold text-gray-900">Payment Method</h2>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                            <div class="space-y-4">
                                <!-- Saved Payment Methods Options -->
                                <template v-if="paymentMethods.length > 0">
                                    <div v-for="pm in paymentMethods" :key="`pm-${pm.id}`">
                                        <div class="rounded-md border cursor-pointer transition" :class="selectedPaymentMethodId === pm.id ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200 hover:border-gray-300'">
                                            <div class="p-4 flex items-center justify-between" @click="selectedPaymentMethodId = pm.id; form.paymentMethod = 'credit_card'">
                                                <div class="flex items-center flex-1">
                                                    <input type="radio" :id="`payment-${pm.id}`" :value="pm.id" v-model="selectedPaymentMethodId" @change="form.paymentMethod = 'credit_card'" class="h-4 w-4 text-blue-600">
                                                    <label :for="`payment-${pm.id}`" class="ml-3 flex-1 cursor-pointer">
                                                        <p class="font-medium text-gray-900">{{ pm.label }} - {{ pm.brand }} ••••{{ pm.last4 }}</p>
                                                        <p class="text-sm text-gray-600">Expires {{ pm.exp_month }}/{{ pm.exp_year }}</p>
                                                    </label>
                                                </div>
                                                <svg class="h-5 w-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                                    <path d="M2 10h20" stroke="white" stroke-width="2" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative py-4">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-gray-300"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-2 bg-white text-gray-500">Or add a new card</span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Credit Card Option (New Card) -->
                                <div class="rounded-md border cursor-pointer transition" :class="form.paymentMethod === 'credit_card' && !selectedPaymentMethodId ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="p-4 flex items-center justify-between" @click="form.paymentMethod = 'credit_card'; selectedPaymentMethodId = null">
                                        <div class="flex items-center">
                                            <input v-if="!selectedPaymentMethodId" type="radio" id="payment-new-card" name="payment-method" value="credit_card" v-model="form.paymentMethod" @change="selectedPaymentMethodId = null" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <label for="payment-new-card" class="ml-3 block text-sm font-medium text-gray-900 cursor-pointer">Add New Credit Card</label>
                                        </div>
                                        <svg class="h-5 w-6 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                            <rect x="2" y="5" width="20" height="14" rx="2" />
                                            <path d="M2 10h20" stroke="white" stroke-width="2" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Credit Card Details (Stripe Elements) -->
                                    <div v-if="form.paymentMethod === 'credit_card' && !selectedPaymentMethodId" class="px-4 pb-4 pt-2 border-t border-gray-200/50 space-y-4">
                                        <div>
                                            <label for="card-element" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                Card Information
                                            </label>
                                            <div v-if="isLoadingStripe" class="flex items-center justify-center py-4 text-gray-500">
                                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Loading secure payment...
                                            </div>
                                            <div 
                                                id="card-element" 
                                                class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-3 bg-white"
                                            ></div>
                                            <div v-if="stripeError" class="mt-2 text-sm text-red-600">
                                                {{ stripeError }}
                                            </div>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                                </svg>
                                                Your card information is encrypted and secure
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Option -->
                                <div class="rounded-md border cursor-pointer transition" :class="form.paymentMethod === 'paypal' ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="p-4 flex items-center justify-between" @click="form.paymentMethod = 'paypal'; selectedPaymentMethodId = null">
                                        <div class="flex items-center">
                                            <input type="radio" id="payment-paypal" name="payment-method" value="paypal" v-model="form.paymentMethod" @change="selectedPaymentMethodId = null" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <label for="payment-paypal" class="ml-3 block text-sm font-medium text-gray-900 cursor-pointer">PayPal</label>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.946 5.425-3.646 6.794-8.263 6.794H10.3c-.578 0-1.075.418-1.17 1.009l-.506 3.454c-.024.175.006.356.104.496.098.14.258.223.428.223h3.29c.578 0 1.075.418 1.17 1.009l.157 1.074a.641.641 0 0 1-.633.742h-6.064a.641.641 0 0 1-.633-.74l.662-4.563-1.033 6.467a.641.641 0 0 1-.633.74z"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- COD Option -->
                                <div class="rounded-md border cursor-pointer transition" :class="form.paymentMethod === 'cod' ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="p-4 flex items-center justify-between" @click="form.paymentMethod = 'cod'; selectedPaymentMethodId = null">
                                        <div class="flex items-center">
                                            <input type="radio" id="payment-cod" name="payment-method" value="cod" v-model="form.paymentMethod" @change="selectedPaymentMethodId = null" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <label for="payment-cod" class="ml-3 block text-sm font-medium text-gray-900 cursor-pointer">Cash on Delivery</label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="mt-10 lg:mt-0 lg:col-span-5">
                    <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Order Summary</h2>

                        <!-- Items List -->
                        <div class="flow-root">
                            <ul role="list" class="-my-6 divide-y divide-gray-200">
                                <li v-for="item in items" :key="item.product_id" class="flex py-6">
                                    <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                        <img :src="getProductImageUrl(item)" :alt="item.name" class="h-full w-full object-cover object-center">
                                    </div>
                                    <div class="ml-4 flex flex-1 flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>{{ item.name }}</h3>
                                                <p>${{ Number(item.price).toFixed(2) }}</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">Size: {{ item.size }}</p>
                                        </div>
                                        <div class="flex flex-1 items-end justify-between text-sm">
                                            <p class="text-gray-500">Qty: {{ item.quantity }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Discount Code -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    v-model="couponForm.code" 
                                    placeholder="Discount Code" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 bg-gray-50/50"
                                    @keyup.enter="applyCoupon"
                                >
                                <button 
                                    type="button" 
                                    @click="applyCoupon"
                                    :disabled="couponForm.processing"
                                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 disabled:opacity-50"
                                >
                                    Apply
                                </button>
                            </div>
                            <p v-if="couponForm.errors.code" class="mt-2 text-sm text-red-600">
                                {{ couponForm.errors.code }}
                            </p>
                        </div>

                        <!-- Price Breakdown -->
                        <dl class="mt-6 space-y-4 border-t border-gray-200 pt-6 text-sm font-medium text-gray-900">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Subtotal</dt>
                                <dd>${{ subtotal.toFixed(2) }}</dd>
                            </div>
                            <div v-if="discount > 0" class="flex items-center justify-between text-green-600">
                                <dt>Discount</dt>
                                <dd>-${{ discount.toFixed(2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Shipping</dt>
                                <dd class="text-green-600">Free</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Taxes (8%)</dt>
                                <dd>${{ taxes.toFixed(2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <dt class="text-lg font-bold text-gray-900">Total</dt>
                                <dd class="text-lg font-bold text-gray-900">${{ grandTotal.toFixed(2) }}</dd>
                            </div>
                        </dl>

                        <!-- Complete Purchase Button -->
                        <div class="mt-6">
                            <button type="submit" @click="submit" :disabled="form.processing" class="w-full rounded-md border border-transparent bg-blue-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                Complete Purchase
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <p class="mt-4 flex justify-center text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 mr-1">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                                Secure encrypted checkout
                            </p>
                        </div>
                    </div>
                </div>
            </div>

    </ShopLayout>
</template>
