<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    items: Array,
    total: Number,
});

const form = useForm({});

const submit = () => {
    form.post(route('checkout.store'));
};
</script>

<template>
    <ShopLayout>
        <Head title="Checkout" />
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Checkout</h1>
            <p class="text-sm text-gray-500">
                {{ items.length }} items
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="space-y-4">
                <section class="rounded-lg border bg-white p-4 shadow-sm">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Order summary
                    </h2>

                    <ul class="mt-4 space-y-3 text-sm text-gray-700">
                        <li
                            v-for="item in items"
                            :key="item.product_id"
                            class="flex items-center justify-between"
                        >
                            <div>
                                <p class="font-medium">
                                    {{ item.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Qty {{ item.quantity }}
                                </p>
                            </div>
                            <p>
                                ${{ (item.price * item.quantity).toFixed(2) }}
                            </p>
                        </li>
                    </ul>
                </section>

                <p class="text-xs text-gray-500">
                    This demo does not integrate a real payment gateway. Placing an order will create it in the database and clear your cart.
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

                <form @submit.prevent="submit" class="space-y-3">
                    <Button
                        type="submit"
                        class="w-full justify-center"
                        :disabled="!items.length || form.processing"
                    >
                        Place order
                    </Button>

                    <Link
                        :href="route('cart.index')"
                        class="inline-flex w-full items-center justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Back to cart
                    </Link>
                </form>
            </aside>
        </div>
    </ShopLayout>
</template>
