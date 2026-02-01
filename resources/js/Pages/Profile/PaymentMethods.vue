<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
    paymentMethods: Array,
    stripePublicKey: String,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const stripe = ref(null);
const cardElement = ref(null);
const stripeElements = ref(null);
const stripeError = ref('');
const isLoadingStripe = ref(false);

const form = useForm({
    stripe_payment_method_id: '',
    label: 'Personal',
    is_default: false,
});

const initializeStripe = async () => {
    try {
        isLoadingStripe.value = true;
        stripe.value = await loadStripe(props.stripePublicKey);
        
        if (stripe.value) {
            stripeElements.value = stripe.value.elements();
            
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
            });
        }
    } catch (error) {
        console.error('Stripe initialization error:', error);
        stripeError.value = 'Failed to load payment system.';
    } finally {
        isLoadingStripe.value = false;
    }
};

const openAddModal = async () => {
    isEditing.value = false;
    form.reset();
    showModal.value = true;
    stripeError.value = '';
    
    if (!stripe.value) {
        await initializeStripe();
    }
    
    setTimeout(() => {
        const cardElementContainer = document.getElementById('card-element');
        if (cardElementContainer && cardElement.value) {
            try {
                cardElement.value.unmount();
            } catch (e) {
                // Already unmounted
            }
            
            cardElement.value.mount('#card-element');
            
            cardElement.value.on('change', (event) => {
                stripeError.value = event.error ? event.error.message : '';
            });
        }
    }, 100);
};

const openEditModal = (method) => {
    isEditing.value = true;
    editingId.value = method.id;
    form.label = method.label || 'Personal';
    form.is_default = method.is_default;
    showModal.value = true;
    stripeError.value = '';
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
    stripeError.value = '';
};

const submit = async () => {
    stripeError.value = '';
    
    if (isEditing.value) {
        form.put(route('payment-methods.update', editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        if (!stripe.value || !cardElement.value) {
            stripeError.value = 'Payment system not loaded.';
            return;
        }
        
        try {
            const { paymentMethod, error } = await stripe.value.createPaymentMethod({
                type: 'card',
                card: cardElement.value,
            });
            
            if (error) {
                stripeError.value = error.message;
                return;
            }
            
            if (!paymentMethod) {
                stripeError.value = 'Failed to create payment method.';
                return;
            }
            
            form.stripe_payment_method_id = paymentMethod.id;
            form.post(route('payment-methods.store'), {
                onSuccess: () => closeModal(),
            });
        } catch (error) {
            stripeError.value = 'Payment processing failed: ' + error.message;
        }
    }
};

const deleteMethod = (id) => {
    if (confirm('Are you sure you want to delete this payment method?')) {
        useForm({}).delete(route('payment-methods.destroy', id));
    }
};
</script>

<template>
    <Head title="Payment Methods" />

    <ProfileLayout>
        <template #breadcrumb>
            <span class="text-gray-900 font-medium">Payment Methods</span>
        </template>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Payment Methods</h1>
            <p class="mt-1 text-gray-500">Manage your credit and debit cards for seamless transactions.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add New Card -->
            <button 
                @click="openAddModal"
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white p-8 hover:border-blue-500 hover:bg-blue-50 transition-all group min-h-[250px]"
            >
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-gray-900">Add New Card</h3>
                <p class="mt-1 text-sm text-gray-500">Visa, Mastercard, or Amex</p>
            </button>

            <!-- Payment Method Cards -->
            <div 
                v-for="method in paymentMethods" 
                :key="method.id"
                class="relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 flex flex-col justify-between"
            >
                <div>
                    <div class="flex items-start justify-between">
                        <div class="flex gap-2">
                            <span v-if="method.is_default" class="inline-flex items-center rounded-md bg-blue-600 px-2 py-1 text-xs font-medium text-white ring-1 ring-inset ring-blue-700/10">DEFAULT</span>
                            <span v-if="method.is_personal" class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase">PERSONAL</span>
                            <span v-if="method.is_business" class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase">BUSINESS</span>
                            <span v-if="method.is_secondary" class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase">SECONDARY</span>
                        </div>
                        <div class="h-8 w-auto">
                            <img v-if="method.brand === 'mastercard'" src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-8" />
                            <img v-else-if="method.brand === 'visa'" src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-8" />
                            <img v-else-if="method.brand === 'amex'" src="https://upload.wikimedia.org/wikipedia/commons/3/30/American_Express_logo.svg" alt="Amex" class="h-8" />
                            <span v-else class="text-xl font-bold uppercase text-gray-400">{{ method.brand }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">CARD NUMBER</p>
                        <div class="mt-1 flex items-center gap-2 font-mono text-xl text-gray-900">
                            <span>••••</span>
                            <span>••••</span>
                            <span>••••</span>
                            <span>{{ method.last4 }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">CARD HOLDER</p>
                            <p class="mt-1 text-sm font-bold text-gray-900">{{ method.holder }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">EXPIRES</p>
                            <p class="mt-1 text-sm font-bold text-gray-900">{{ method.expiry }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center border-t border-gray-100 pt-4">
                    <button 
                        @click="openEditModal(method)"
                        class="flex-1 flex items-center justify-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors py-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                        </svg>
                        Edit
                    </button>
                    <div class="h-4 w-px bg-gray-200"></div>
                    <button 
                        @click="deleteMethod(method.id)"
                        class="flex-1 flex items-center justify-center gap-2 text-sm font-medium text-gray-700 hover:text-red-600 transition-colors py-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Secure Section -->
        <div class="mt-12 rounded-2xl bg-blue-50 p-8 border border-blue-100">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Secure your transactions</h3>
                    <p class="mt-1 text-gray-500">ShopWave uses industry-standard encryption to protect your financial information.</p>
                </div>
                <div class="flex gap-4">
                    <button class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                        View Security Policy
                    </button>
                    <button class="rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50 transition-colors">
                        Billing Support
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeModal"></div>

            <!-- Modal Panel -->
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">{{ isEditing ? 'Edit Payment Method' : 'Add New Card' }}</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit">
                    <!-- Show error if any -->
                    <div v-if="stripeError" class="mb-6 rounded-md bg-red-50 border border-red-200 p-4">
                        <p class="text-sm font-medium text-red-800">{{ stripeError }}</p>
                    </div>

                    <!-- Stripe Card Element (only for add, not edit) -->
                    <div v-if="!isEditing" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                        <div v-if="isLoadingStripe" class="flex items-center justify-center py-4 text-gray-500">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading secure payment...
                        </div>
                        <div 
                            id="card-element" 
                            class="block w-full rounded-lg border border-gray-300 shadow-sm py-3 px-3 bg-white"
                        ></div>
                        <div class="mt-2 flex items-center text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                            </svg>
                            Your card information is encrypted and secure
                        </div>
                    </div>

                    <!-- Label -->
                    <div class="mb-6">
                        <label for="label" class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                        <select
                            id="label"
                            v-model="form.label"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="Personal">Personal</option>
                            <option value="Business">Business</option>
                            <option value="Secondary">Secondary</option>
                        </select>
                    </div>

                    <!-- Default Checkbox -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_default" class="h-4 w-4 rounded border-gray-300 text-blue-600" />
                            <span class="ms-2 text-sm text-gray-600">Set as default payment method</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="closeModal" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</button>
                        <button type="submit" :disabled="form.processing || isLoadingStripe" class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-50">
                            {{ isEditing ? 'Save Changes' : 'Add Card' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </ProfileLayout>
</template>
