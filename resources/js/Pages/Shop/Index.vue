<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const form = useForm({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
});

const hasFilters = computed(() => !!form.search || !!form.category);

const submitFilters = () => {
    form.get(route('shop.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'filters'],
    });
};
</script>

<template>
    <ShopLayout>
        <Head title="Shop - Products" />
        <section
            class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm"
        >
            <div
                class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8"
            >
                <div>
                    <p
                        class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700"
                    >
                        Browse and filter your catalog in one place
                    </p>
                    <h1 class="mt-3 text-2xl font-semibold text-gray-900 sm:text-3xl">
                        Products
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 sm:max-w-md">
                        Use search and category filters to quickly find items and add them
                        to your cart.
                    </p>
                </div>

                <form
                    @submit.prevent="submitFilters"
                    class="flex w-full flex-wrap items-center gap-3 sm:w-auto sm:justify-end"
                >
                    <div class="w-full min-[420px]:w-40">
                        <Input
                            v-model="form.search"
                            type="search"
                            placeholder="Search products"
                        />
                    </div>

                    <select
                        v-model="form.category"
                        class="block w-full min-[420px]:w-40 rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All categories</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.slug"
                        >
                            {{ category.name }}
                        </option>
                    </select>

                    <Button type="submit">
                        Filter
                    </Button>

                    <button
                        v-if="hasFilters"
                        type="button"
                        class="text-sm text-gray-500 hover:text-gray-900"
                        @click="
                            form.search = '';
                            form.category = '';
                            submitFilters();
                        "
                    >
                        Clear
                    </button>
                </form>
            </div>
        </section>

        <div
            v-if="products.data.length"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <article
                v-for="product in products.data"
                :key="product.id"
                class="flex flex-col overflow-hidden rounded-lg border bg-white shadow-sm"
            >
                <div class="aspect-[4/3] bg-gray-100" />

                <div class="flex flex-1 flex-col gap-2 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="text-sm font-semibold text-gray-900">
                            <Link
                                :href="route('shop.show', product.slug)"
                                class="hover:underline"
                            >
                                {{ product.name }}
                            </Link>
                        </h2>
                        <p class="text-sm font-semibold text-gray-900">
                            <span v-if="product.sale_price">
                                ${{ product.sale_price }}
                                <span class="ml-1 text-xs text-gray-400 line-through">
                                    ${{ product.price }}
                                </span>
                            </span>
                            <span v-else>
                                ${{ product.price }}
                            </span>
                        </p>
                    </div>

                    <p class="text-xs text-gray-500">
                        {{ product.category?.name }}
                    </p>

                    <p class="mt-auto text-xs text-gray-500">
                        In stock: {{ product.stock }}
                    </p>

                    <div class="mt-3">
                        <Link
                            :href="route('shop.show', product.slug)"
                            class="inline-flex w-full items-center justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            View details
                        </Link>
                    </div>
                </div>
            </article>
        </div>
        <p
            v-else
            class="text-sm text-gray-500"
        >
            No products found.
        </p>

        <div
            v-if="products.links.length > 3"
            class="mt-8 flex justify-center gap-2"
        >
            <Link
                v-for="link in products.links"
                :key="link.url ?? link.label"
                :href="link.url || ''"
                preserve-state
                preserve-scroll
                v-html="link.label"
                :class="[
                    'min-w-8 inline-flex items-center justify-center rounded-md border px-3 py-1 text-xs',
                    {
                        'bg-indigo-600 text-white border-indigo-600': link.active,
                        'text-gray-600 hover:bg-gray-50 border-gray-300': !link.active && link.url,
                        'text-gray-400 border-transparent': !link.url,
                    },
                ]"
            />
        </div>
    </ShopLayout>
</template>
