<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    paymentMethods: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    type: 'visa',
    last4: '',
    holder_name: '',
    expiry_date: '',
    is_default: false,
    label: 'Personal',
});

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    showModal.value = true;
};

const openEditModal = (method) => {
    isEditing.value = true;
    editingId.value = method.id;
    form.type = method.type;
    form.last4 = method.last4;
    form.holder_name = method.holder;
    form.expiry_date = method.expiry;
    form.is_default = method.is_default;
    form.label = method.label || 'Personal';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('payment-methods.update', editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('payment-methods.store'), {
            onSuccess: () => closeModal(),
        });
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
                            <img v-if="method.type === 'mastercard'" src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-8" />
                            <img v-else-if="method.type === 'visa'" src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-8" />
                            <span v-else class="text-xl font-bold uppercase text-gray-400">{{ method.type }}</span>
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
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isEditing ? 'Edit Payment Method' : 'Add New Card' }}
                </h2>
                
                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <!-- Card Type -->
                    <div>
                        <InputLabel for="type" value="Card Type" />
                        <select
                            id="type"
                            v-model="form.type"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="visa">Visa</option>
                            <option value="mastercard">Mastercard</option>
                            <option value="amex">Amex</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>

                    <!-- Card Number (Last 4) -->
                    <div>
                        <InputLabel for="last4" value="Last 4 Digits" />
                        <TextInput
                            id="last4"
                            v-model="form.last4"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. 4242"
                            maxlength="4"
                        />
                        <InputError :message="form.errors.last4" class="mt-2" />
                    </div>

                    <!-- Card Holder -->
                    <div>
                        <InputLabel for="holder_name" value="Card Holder Name" />
                        <TextInput
                            id="holder_name"
                            v-model="form.holder_name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Name on card"
                        />
                        <InputError :message="form.errors.holder_name" class="mt-2" />
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <InputLabel for="expiry_date" value="Expiry Date" />
                        <TextInput
                            id="expiry_date"
                            v-model="form.expiry_date"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="MM/YY"
                        />
                        <InputError :message="form.errors.expiry_date" class="mt-2" />
                    </div>

                    <!-- Label -->
                    <div>
                        <InputLabel for="label" value="Label" />
                        <select
                            id="label"
                            v-model="form.label"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="Personal">Personal</option>
                            <option value="Business">Business</option>
                            <option value="Secondary">Secondary</option>
                        </select>
                        <InputError :message="form.errors.label" class="mt-2" />
                    </div>

                    <!-- Default Checkbox -->
                    <div class="block">
                        <label class="flex items-center">
                            <Checkbox name="is_default" v-model:checked="form.is_default" />
                            <span class="ms-2 text-sm text-gray-600">Set as default payment method</span>
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ isEditing ? 'Save Changes' : 'Add Card' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </ProfileLayout>
</template>
