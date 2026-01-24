<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
});

const form = useForm({
    status: props.order.status,
});

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'paid', label: 'Paid' },
    { value: 'shipped', label: 'Shipped' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const submit = () => {
    form.patch(route('admin.orders.update', props.order.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin - Order details" />
        <template #header>
            <div
                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Order #{{ order.id }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Order details and status.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ order.user?.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ order.user?.email }}
                            </p>
                        </div>
                        <div class="flex flex-col items-start gap-2 text-right sm:items-end">
                            <p class="text-sm font-semibold text-gray-900">
                                ${{ order.total_price }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ new Date(order.created_at).toLocaleString() }}
                            </p>
                            <span
                                class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium"
                                :class="order.status === 'completed' || order.status === 'paid'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : order.status === 'cancelled'
                                        ? 'bg-rose-50 text-rose-700'
                                        : 'bg-gray-100 text-gray-700'"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="mb-3 text-sm font-semibold text-gray-800">
                        Items
                    </h2>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex items-center justify-between gap-3"
                        >
                            <span>
                                {{ item.product?.name }} × {{ item.quantity }}
                            </span>
                            <span>
                                ${{ (item.price * item.quantity).toFixed(2) }}
                            </span>
                        </li>
                    </ul>
                </section>

                <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="mb-3 text-sm font-semibold text-gray-800">
                        Update status
                    </h2>

                    <form
                        class="flex flex-col gap-4 sm:flex-row sm:items-center"
                        @submit.prevent="submit"
                    >
                        <div class="w-full sm:w-64">
                            <select
                                v-model="form.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="form.errors.status"
                            />
                        </div>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            Save
                        </Button>
                    </form>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>
