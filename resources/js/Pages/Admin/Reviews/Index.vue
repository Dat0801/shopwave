<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reviews: Object,
});

const toggleForm = useForm({});
const deleteForm = useForm({});

const toggleApproval = (review) => {
    toggleForm.patch(route('admin.reviews.toggle-approval', review.id), {
        preserveScroll: true,
    });
};

const deleteReview = (review) => {
    if (confirm('Are you sure you want to delete this review?')) {
        deleteForm.delete(route('admin.reviews.destroy', review.id), {
            preserveScroll: true,
        });
    }
};

const renderStars = (rating) => {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '★';
        } else {
            stars += '☆';
        }
    }
    return stars;
};
</script>

<template>
    <Head title="Reviews" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Product Reviews
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rating
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Comment
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="reviews.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No reviews found.
                                        </td>
                                    </tr>
                                    <tr v-for="review in reviews.data" :key="review.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                <img v-if="review.product.image_path" :src="review.product.image_path" class="h-10 w-10 rounded-full mr-3 object-cover" alt="">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ review.product.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ review.user.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-500">
                                            {{ renderStars(review.rating) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            {{ review.comment }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="review.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                            >
                                                {{ review.is_approved ? 'Approved' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(review.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                @click="toggleApproval(review)" 
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                {{ review.is_approved ? 'Reject' : 'Approve' }}
                                            </button>
                                            <button 
                                                @click="deleteReview(review)" 
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <!-- Pagination can be added here if needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
