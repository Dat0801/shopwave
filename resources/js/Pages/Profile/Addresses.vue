<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    addresses: Array,
});

const user = usePage().props.auth.user;

const deleteAddress = (id) => {
    if (confirm('Are you sure you want to delete this address?')) {
        useForm({}).delete(route('addresses.destroy', id));
    }
};

// Modal state
const showModal = ref(false);
const editingAddress = ref(null);

const form = useForm({
    label: 'Home',
    name: user.name,
    phone: user.phone || '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    postal_code: '',
    country_code: 'US',
    is_default: false,
});

const openModal = (address = null) => {
    editingAddress.value = address;
    if (address) {
        form.label = address.label;
        form.name = address.name;
        form.phone = address.phone;
        form.address_line_1 = address.address_line_1;
        form.address_line_2 = address.address_line_2;
        form.city = address.city;
        form.state = address.state;
        form.postal_code = address.postal_code;
        form.country_code = address.country_code;
        form.is_default = address.is_default;
    } else {
        form.reset();
        form.name = user.name;
        form.phone = user.phone || '';
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingAddress.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingAddress.value) {
        form.put(route('addresses.update', editingAddress.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('addresses.store'), {
            onSuccess: () => closeModal(),
        });
    }
};
</script>

<template>
    <Head title="Addresses" />

    <ProfileLayout>
        <template #breadcrumb>
            <span class="text-gray-900 font-medium">Addresses</span>
        </template>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Addresses</h1>
            <p class="mt-1 text-gray-500">Manage your shipping addresses for a faster checkout experience.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add New Address Card -->
            <button 
                @click="openModal()"
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white p-8 hover:border-blue-500 hover:bg-blue-50 transition-all group min-h-[250px]"
            >
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-gray-900">Add New Address</h3>
                <p class="mt-1 text-sm text-gray-500">Save a new shipping location</p>
            </button>

            <!-- Address Cards -->
            <div 
                v-for="address in addresses" 
                :key="address.id"
                class="relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 flex flex-col justify-between"
            >
                <div>
                    <div class="flex items-start justify-between">
                        <div class="flex gap-2">
                            <span v-if="address.is_default" class="inline-flex items-center rounded-md bg-blue-600 px-2 py-1 text-xs font-medium text-white ring-1 ring-inset ring-blue-700/10">DEFAULT</span>
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase">{{ address.label }}</span>
                        </div>
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <svg v-if="address.label?.toLowerCase() === 'home'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" /></svg>
                            <svg v-else-if="address.label?.toLowerCase() === 'office'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M7.5 5.25a3 3 0 013-3h3a3 3 0 013 3v.25a3 3 0 013 3v9a3 3 0 01-3 3h-9a3 3 0 01-3-3v-9a3 3 0 013-3V5.25zm11.1 6.28a2.25 2.25 0 00-2.1-1.28H14.5c-1.24 0-2.25-1.01-2.25-2.25v-.25h-2.5v.25a2.25 2.25 0 01-2.25 2.25H5.5a2.25 2.25 0 00-2.1 1.28v.02l.6 6.31a.75.75 0 00.75.69h12.5a.75.75 0 00.75-.69l.6-6.31v-.02z" clip-rule="evenodd" /></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                        </div>
                    </div>
                    
                    <h3 class="mt-4 text-lg font-bold text-gray-900">{{ address.name }}</h3>
                    <p class="text-sm text-gray-500">{{ address.phone }}</p>
                    
                    <div class="mt-4 space-y-1 text-sm text-gray-600">
                        <p>{{ address.address_line_1 }}</p>
                        <p v-if="address.address_line_2">{{ address.address_line_2 }}</p>
                        <p>{{ address.city }}, {{ address.state }} {{ address.postal_code }}</p>
                        <p>{{ address.country_code }}</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center border-t border-gray-100 pt-4">
                    <button 
                        @click="openModal(address)"
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
                        @click="deleteAddress(address.id)"
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

        <!-- Footer Support Section -->
        <div class="mt-12 rounded-2xl bg-gray-50 p-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Need help with your orders?</h3>
                    <p class="mt-1 text-gray-500">Our support team is available 24/7 to assist you.</p>
                </div>
                <div class="flex gap-4">
                    <button class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                        Contact Support
                    </button>
                    <button class="rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50 transition-colors">
                        View FAQ
                    </button>
                </div>
            </div>
        </div>

        <!-- Address Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <!-- Modal Panel -->
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">{{ editingAddress ? 'Edit Address' : 'Add New Address' }}</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="label" class="block text-sm font-medium text-gray-700">Label (e.g. Home, Office)</label>
                            <input type="text" id="label" v-model="form.label" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" v-model="form.name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" id="phone" v-model="form.phone" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                            <input type="text" id="address_line_1" v-model="form.address_line_1" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.address_line_1" class="mt-1 text-sm text-red-600">{{ form.errors.address_line_1 }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                            <input type="text" id="address_line_2" v-model="form.address_line_2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" v-model="form.city" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">{{ form.errors.city }}</p>
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State / Province</label>
                            <input type="text" id="state" v-model="form.state" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.state" class="mt-1 text-sm text-red-600">{{ form.errors.state }}</p>
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" id="postal_code" v-model="form.postal_code" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.postal_code" class="mt-1 text-sm text-red-600">{{ form.errors.postal_code }}</p>
                        </div>

                        <div>
                            <label for="country_code" class="block text-sm font-medium text-gray-700">Country Code</label>
                            <input type="text" id="country_code" v-model="form.country_code" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                            <p v-if="form.errors.country_code" class="mt-1 text-sm text-red-600">{{ form.errors.country_code }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="flex items-center">
                                <input id="is_default" type="checkbox" v-model="form.is_default" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <label for="is_default" class="ml-2 block text-sm text-gray-900">Set as default address</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="closeModal" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                            {{ editingAddress ? 'Update Address' : 'Save Address' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </ProfileLayout>
</template>
