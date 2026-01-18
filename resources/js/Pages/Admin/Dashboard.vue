<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    stats: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">
                Admin dashboard
            </h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Orders
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                            {{ stats.orders }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Revenue
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                            ${{ stats.revenue }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Users
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                            {{ stats.users }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

