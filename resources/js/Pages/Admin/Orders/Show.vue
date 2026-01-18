<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
});

const form = useForm({
    status: props.order.status,
});

const submit = () => {
    form.patch(route('admin.orders.update', props.order.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">
                Order #{{ order.id }}
            </h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ order.user?.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ order.user?.email }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                ${{ order.total_price }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ new Date(order.created_at).toLocaleString() }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-800">
                        Items
                    </h2>
                    <ul class="space-y-2 text-sm text-gray-700">
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
                </section>

                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-800">
                        Update status
                    </h2>

                    <form
                        class="flex items-center gap-3"
                        @submit.prevent="submit"
                    >
                        <div class="w-48">
                            <Input
                                v-model="form.status"
                                placeholder="Status"
                            />
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
    </AuthenticatedLayout>
</template>

