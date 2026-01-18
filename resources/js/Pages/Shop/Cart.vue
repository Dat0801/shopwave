<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    items: Array,
    total: Number,
});

const updateForm = useForm({});

const updateQuantity = (item, quantity) => {
    updateForm.transform(() => ({
        quantity,
    })).patch(route('cart.update', item.product_id), {
        preserveScroll: true,
    });
};

const removeItem = (item) => {
    updateForm.delete(route('cart.destroy', item.product_id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <ShopLayout>
        <Head title="Your cart" />
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Cart</h1>
            <p class="text-sm text-gray-500">
                {{ items.length }} items
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="space-y-4">
                <div
                    v-if="items.length"
                    class="space-y-4"
                >
                    <div
                        v-for="item in items"
                        :key="item.product_id"
                        class="flex items-start gap-4 rounded-lg border bg-white p-4 shadow-sm"
                    >
                        <div class="h-20 w-20 rounded-md bg-gray-100" />

                        <div class="flex flex-1 items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ item.name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Price: ${{ item.price }}
                                </p>
                                <button
                                    type="button"
                                    class="mt-2 text-xs text-red-600 hover:text-red-700"
                                    @click="removeItem(item)"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-20">
                                        <Input
                                            :model-value="item.quantity"
                                            type="number"
                                            min="1"
                                            @update:model-value="(value) => updateQuantity(item, Number(value))"
                                        />
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">
                                    ${{ (item.price * item.quantity).toFixed(2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <p
                    v-else
                    class="text-sm text-gray-500"
                >
                    Your cart is empty.
                </p>
            </div>

            <aside class="space-y-4 rounded-lg border bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-700">
                        Total
                    </p>
                    <p class="text-lg font-semibold text-gray-900">
                        ${{ total.toFixed(2) }}
                    </p>
                </div>

                <Link
                    :href="route('checkout.index')"
                    class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                    :class="{ 'pointer-events-none opacity-50': !items.length }"
                >
                    Proceed to checkout
                </Link>
            </aside>
        </div>
    </ShopLayout>
</template>
