<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    coupon: Object,
});

const form = useForm({
    code: props.coupon?.code || '',
    name: props.coupon?.name || '',
    description: props.coupon?.description || '',
    type: props.coupon?.type || 'fixed',
    value: props.coupon?.value || '',
    min_order_amount: props.coupon?.min_order_amount || '',
    starts_at: props.coupon?.starts_at ? props.coupon.starts_at.substring(0, 10) : '',
    expires_at: props.coupon?.expires_at ? props.coupon.expires_at.substring(0, 10) : '',
    usage_limit: props.coupon?.usage_limit || '',
    limit_usage_per_user: props.coupon ? Boolean(props.coupon.limit_usage_per_user) : false,
    is_active: props.coupon !== undefined ? Boolean(props.coupon.is_active) : true,
});

// Helper for publishing status
const publishStatus = ref('active');

if (props.coupon) {
    if (!props.coupon.is_active) publishStatus.value = 'draft';
    else if (props.coupon.starts_at && new Date(props.coupon.starts_at) > new Date()) publishStatus.value = 'scheduled';
    else publishStatus.value = 'active';
}

watch(publishStatus, (val) => {
    if (val === 'draft') form.is_active = false;
    else form.is_active = true;
});

const generateCode = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < 10; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    form.code = result;
};

const submit = () => {
    if (props.coupon) {
        form.put(route('admin.coupons.update', props.coupon.id));
    } else {
        form.post(route('admin.coupons.store'));
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};
</script>

<template>
    <AdminLayout>
        <Head :title="props.coupon ? 'Admin - Edit Coupon' : 'Admin - Create New Coupon'" />

        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Dashboard', href: route('admin.dashboard') },
                            { label: 'Coupons', href: route('admin.coupons.index') },
                            { label: props.coupon ? 'Edit Coupon' : 'Create New' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ props.coupon ? 'Edit Coupon' : 'Create New Coupon' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">Configure your discount rules and usage limits for the promotion.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.coupons.index')" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">
                        Discard Draft
                    </Link>
                    <button 
                        @click="submit" 
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50"
                    >
                        {{ props.coupon ? 'Update Coupon' : 'Create Coupon' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Information -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">General Information</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="code" value="Coupon Code" />
                            <div class="relative mt-1">
                                <TextInput
                                    id="code"
                                    type="text"
                                    class="block w-full pr-20 uppercase"
                                    v-model="form.code"
                                    placeholder="E.G. SUMMER2024"
                                    required
                                />
                                <button 
                                    type="button"
                                    @click="generateCode"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-semibold text-blue-600 hover:text-blue-500"
                                >
                                    GENERATE
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.code" />
                        </div>

                        <div>
                            <InputLabel for="name" value="Internal Title" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                placeholder="Marketing Summer Blast"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Explain the promotion for the marketing team..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                    </div>
                </div>

                <!-- Discount Rules -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Discount Rules</h2>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <InputLabel value="Discount Type" class="mb-2" />
                            <div class="grid grid-cols-2 gap-4">
                                <button 
                                    type="button"
                                    @click="form.type = 'percent'"
                                    class="relative flex items-center justify-center gap-2 rounded-lg border p-4 text-sm font-semibold transition-all"
                                    :class="form.type === 'percent' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-200 hover:border-gray-300 text-gray-600'"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                    Percentage
                                    <div v-if="form.type === 'percent'" class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-blue-600 ring-2 ring-white"></div>
                                </button>
                                <button 
                                    type="button"
                                    @click="form.type = 'fixed'"
                                    class="relative flex items-center justify-center gap-2 rounded-lg border p-4 text-sm font-semibold transition-all"
                                    :class="form.type === 'fixed' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-200 hover:border-gray-300 text-gray-600'"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Fixed Amount
                                    <div v-if="form.type === 'fixed'" class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-blue-600 ring-2 ring-white"></div>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="value" value="Discount Value" />
                                <div class="relative mt-1">
                                    <TextInput
                                        id="value"
                                        type="number"
                                        step="0.01"
                                        class="block w-full pr-8"
                                        v-model="form.value"
                                        placeholder="0.00"
                                        required
                                    />
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 sm:text-sm">
                                        {{ form.type === 'percent' ? '%' : '$' }}
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.value" />
                            </div>

                            <div>
                                <InputLabel for="min_order_amount" value="Minimum Purchase" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <TextInput
                                        id="min_order_amount"
                                        type="number"
                                        step="0.01"
                                        class="block w-full pl-7"
                                        v-model="form.min_order_amount"
                                        placeholder="0.00"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.min_order_amount" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Limits & Scheduling -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Limits & Scheduling</h2>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="starts_at" value="Start Date" />
                                <TextInput
                                    id="starts_at"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.starts_at"
                                />
                                <InputError class="mt-2" :message="form.errors.starts_at" />
                            </div>

                            <div>
                                <InputLabel for="expires_at" value="Expiry Date" />
                                <TextInput
                                    id="expires_at"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.expires_at"
                                />
                                <InputError class="mt-2" :message="form.errors.expires_at" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                            <div>
                                <InputLabel for="limit_usage_per_user" value="Limit usage per user" />
                                <p class="text-sm text-gray-500">Restrict how many times a single customer can use this coupon.</p>
                            </div>
                            <div class="flex items-center">
                                <button 
                                    type="button" 
                                    @click="form.limit_usage_per_user = !form.limit_usage_per_user"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                                    :class="form.limit_usage_per_user ? 'bg-blue-600' : 'bg-gray-200'"
                                >
                                    <span 
                                        aria-hidden="true" 
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="form.limit_usage_per_user ? 'translate-x-5' : 'translate-x-0'"
                                    ></span>
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <InputLabel for="usage_limit" value="Total usage limit" />
                                    <p class="text-sm text-gray-500">Maximum number of times this coupon can be used globally.</p>
                                </div>
                                <div class="w-32">
                                    <TextInput
                                        id="usage_limit"
                                        type="number"
                                        class="block w-full"
                                        v-model="form.usage_limit"
                                        placeholder="100"
                                    />
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.usage_limit" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Preview Card -->
                <div class="rounded-xl bg-blue-600 p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span class="text-xs font-bold tracking-wider uppercase opacity-80">Store Coupon</span>
                        </div>
                        <svg class="h-6 w-6 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>

                    <div class="mb-8">
                        <div class="text-4xl font-bold">
                            {{ form.type === 'percent' ? (form.value || 0) + '%' : formatCurrency(form.value) }} OFF
                        </div>
                        <div class="mt-1 text-sm opacity-90">
                            Valid on orders above {{ formatCurrency(form.min_order_amount) }}
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -left-6 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full bg-gray-100"></div>
                        <div class="absolute -right-6 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full bg-gray-100"></div>
                        <div class="flex items-center justify-between rounded-lg bg-white/20 p-3 backdrop-blur-sm border border-white/30 border-dashed">
                            <span class="font-mono text-lg font-bold tracking-wider">{{ form.code || 'CODE' }}</span>
                            <button class="opacity-80 hover:opacity-100">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Publishing -->
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-4">Publishing</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-6 items-center">
                                <input id="status-active" name="status" type="radio" value="active" v-model="publishStatus" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="status-active" class="font-medium text-gray-900">Active</label>
                                <p class="text-gray-500">Enable immediately upon creation</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-6 items-center">
                                <input id="status-scheduled" name="status" type="radio" value="scheduled" v-model="publishStatus" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="status-scheduled" class="font-medium text-gray-900">Scheduled</label>
                                <p class="text-gray-500">Wait until the specified start date</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-6 items-center">
                                <input id="status-draft" name="status" type="radio" value="draft" v-model="publishStatus" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="status-draft" class="font-medium text-gray-900">Draft</label>
                                <p class="text-gray-500">Save for later editing</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <div v-if="publishStatus === 'active'" class="flex items-center gap-2 text-sm text-amber-600 bg-amber-50 p-3 rounded-lg">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Verify all rules before saving.
                        </div>
                        <button
                            @click="submit"
                            :disabled="form.processing"
                            class="mt-4 w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50"
                        >
                            Save Coupon
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
