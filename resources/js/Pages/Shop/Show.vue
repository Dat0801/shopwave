<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
});

const form = useForm({
    quantity: 1,
});

const price = computed(() => props.product.sale_price || props.product.price);

const addToCart = () => {
    form.post(route('cart.store', props.product.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <ShopLayout>
        <nav class="mb-4 text-xs text-gray-500">
            <Link :href="route('shop.index')" class="hover:underline">
                Products
            </Link>
            <span class="mx-1">/</span>
            <span class="text-gray-700">
                {{ product.name }}
            </span>
        </nav>

        <div class="grid gap-8 md:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <div class="aspect-[4/3] rounded-md bg-gray-100" />
            </div>

            <div class="space-y-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ product.name }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ product.category?.name }}
                    </p>
                </div>

                <div class="flex items-baseline gap-3">
                    <p class="text-2xl font-semibold text-gray-900">
                        ${{ price }}
                    </p>
                    <p
                        v-if="product.sale_price"
                        class="text-sm text-gray-400 line-through"
                    >
                        ${{ product.price }}
                    </p>
                </div>

                <p class="text-sm text-gray-600">
                    {{ product.description || 'No description provided.' }}
                </p>

                <p class="text-xs text-gray-500">
                    In stock: {{ product.stock }}
                </p>

                <form
                    class="mt-4 flex items-center gap-3"
                    @submit.prevent="addToCart"
                >
                    <div class="w-24">
                        <Input
                            v-model="form.quantity"
                            type="number"
                            min="1"
                        />
                    </div>

                    <Button type="submit">
                        Add to cart
                    </Button>
                </form>
            </div>
        </div>
    </ShopLayout>
</template>

