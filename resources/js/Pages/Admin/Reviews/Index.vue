<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    CheckCircleIcon, 
    XCircleIcon, 
    ClockIcon, 
    TrashIcon,
    ArrowPathIcon,
    FunnelIcon,
    ArrowDownTrayIcon,
    ChatBubbleLeftRightIcon,
    Cog6ToothIcon,
    ArrowUturnLeftIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';
import { StarIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    reviews: Object,
    stats: Object,
    filters: Object,
});

const currentStatus = ref(props.filters?.status || 'all');

const tabs = [
    { name: 'All Reviews', value: 'all' },
    { name: 'Pending', value: 'pending', count: props.stats.pending },
    { name: 'Approved', value: 'approved' },
    { name: 'Rejected', value: 'rejected' },
];

const updateStatus = (review, status) => {
    router.patch(route('admin.reviews.update-status', review.id), {
        status: status,
    }, {
        preserveScroll: true,
    });
};

const deleteReview = (review) => {
    if (confirm('Are you sure you want to delete this review?')) {
        router.delete(route('admin.reviews.destroy', review.id), {
            preserveScroll: true,
        });
    }
};

const filterByStatus = (status) => {
    currentStatus.value = status;
    router.get(route('admin.reviews.index'), {
        status: status === 'all' ? null : status,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const getInitials = (name) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

const getStatusColor = (status) => {
    switch (status) {
        case 'approved': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-orange-100 text-orange-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Review Moderation" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Review Moderation</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage and respond to customer feedback across all product categories.</p>
                </div>
                <div class="flex gap-3">
                    <button class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <ArrowDownTrayIcon class="h-4 w-4" />
                        Export CSV
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                        <Cog6ToothIcon class="h-4 w-4" />
                        Moderation Rules
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Reviews</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total.toLocaleString() }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-2 text-blue-600">
                            <ChatBubbleLeftRightIcon class="h-6 w-6" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="font-medium text-green-600">+12%</span>
                        <span class="ml-2 text-gray-500">vs last month</span>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 relative overflow-hidden">
                    <div class="absolute top-2 right-2 h-2 w-2 rounded-full bg-orange-500"></div>
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pending Moderation</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.pending }}</p>
                        </div>
                        <div class="rounded-lg bg-orange-50 p-2 text-orange-600">
                            <ClockIcon class="h-6 w-6" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="font-medium text-orange-600">+5%</span>
                        <span class="ml-2 text-gray-500">new since yesterday</span>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Average Rating</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.avg_rating }}</p>
                        </div>
                        <div class="rounded-lg bg-yellow-50 p-2 text-yellow-600">
                            <StarIcon class="h-6 w-6" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="font-medium text-red-600">-0.2%</span>
                        <span class="ml-2 text-gray-500">site-wide average</span>
                    </div>
                </div>
            </div>

            <!-- Filters & List -->
            <div class="bg-white shadow-sm ring-1 ring-gray-100 rounded-xl overflow-hidden">
                <!-- Toolbar -->
                <div class="border-b border-gray-200 px-4 py-4 sm:flex sm:items-center sm:justify-between sm:px-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <template v-for="tab in tabs" :key="tab.value">
                            <button
                                @click="filterByStatus(tab.value)"
                                :class="[
                                    currentStatus === tab.value
                                        ? 'bg-blue-600 text-white shadow-sm'
                                        : 'bg-white text-gray-700 hover:bg-gray-50 ring-1 ring-inset ring-gray-300',
                                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition'
                                ]"
                            >
                                <span v-if="tab.value === 'pending' && tab.count > 0" class="flex items-center gap-2">
                                    <ClockIcon class="h-4 w-4" :class="currentStatus === tab.value ? 'text-white' : 'text-orange-500'" />
                                    {{ tab.name }}
                                    <span 
                                        :class="currentStatus === tab.value ? 'bg-blue-500 text-white' : 'bg-orange-100 text-orange-700'"
                                        class="ml-1 rounded-full px-2 py-0.5 text-xs font-bold"
                                    >
                                        {{ tab.count }}
                                    </span>
                                </span>
                                <span v-else-if="tab.value === 'approved'" class="flex items-center gap-2">
                                    <CheckCircleIcon class="h-4 w-4" :class="currentStatus === tab.value ? 'text-white' : 'text-green-500'" />
                                    {{ tab.name }}
                                </span>
                                <span v-else-if="tab.value === 'rejected'" class="flex items-center gap-2">
                                    <XCircleIcon class="h-4 w-4" :class="currentStatus === tab.value ? 'text-white' : 'text-red-500'" />
                                    {{ tab.name }}
                                </span>
                                <span v-else>
                                    {{ tab.name }}
                                </span>
                            </button>
                        </template>
                        
                        <div class="h-6 w-px bg-gray-300 mx-2 hidden sm:block"></div>
                        
                        <button class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                            <FunnelIcon class="h-4 w-4" />
                            More Filters
                        </button>
                    </div>

                    <div class="mt-4 sm:mt-0">
                        <button class="group inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                            Sorted by: <span class="font-bold text-blue-600">Newest</span>
                            <ArrowDownTrayIcon class="h-4 w-4 text-gray-400 group-hover:text-gray-600 transform rotate-180" />
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rating</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Review</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="reviews.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No reviews found matching your criteria.
                                </td>
                            </tr>
                            <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-gray-50 transition-colors">
                                <!-- Customer -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div 
                                            class="h-9 w-9 flex-shrink-0 rounded-full flex items-center justify-center text-sm font-bold shadow-sm ring-1 ring-gray-200"
                                            :class="['bg-blue-100 text-blue-700', 'bg-purple-100 text-purple-700', 'bg-pink-100 text-pink-700'][review.id % 3]"
                                        >
                                            {{ getInitials(review.user.name) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ review.user.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Product -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a :href="route('shop.show', review.product.slug)" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                        {{ review.product.name }}
                                    </a>
                                </td>

                                <!-- Rating -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex text-yellow-400">
                                        <StarIcon v-for="i in 5" :key="i" class="h-4 w-4" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200'" />
                                    </div>
                                </td>

                                <!-- Review -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 line-clamp-2 max-w-md">
                                        {{ review.comment }}
                                    </div>
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(review.created_at) }}
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="getStatusColor(review.status)"
                                    >
                                        {{ review.status }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Actions based on status -->
                                        <button 
                                            v-if="review.status === 'pending' || review.status === 'rejected'"
                                            @click="updateStatus(review, 'approved')" 
                                            class="p-1 text-green-600 hover:text-green-900 hover:bg-green-50 rounded"
                                            title="Approve"
                                        >
                                            <CheckCircleIcon class="h-5 w-5" />
                                        </button>

                                        <button 
                                            v-if="review.status === 'pending' || review.status === 'approved'"
                                            @click="updateStatus(review, 'rejected')" 
                                            class="p-1 text-orange-600 hover:text-orange-900 hover:bg-orange-50 rounded"
                                            title="Reject"
                                        >
                                            <XCircleIcon class="h-5 w-5" />
                                        </button>

                                        <button 
                                            @click="deleteReview(review)" 
                                            class="p-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded"
                                            title="Delete"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="reviews.data.length > 0" class="border-t border-gray-200 px-4 py-3 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ reviews.from }}</span> to <span class="font-medium">{{ reviews.to }}</span> of <span class="font-medium">{{ reviews.total }}</span> reviews
                        </div>
                        <div class="flex gap-2">
                            <Link 
                                v-for="link in reviews.links" 
                                :key="link.label"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'relative inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset focus:z-20 focus:outline-offset-0',
                                    link.active 
                                        ? 'bg-blue-600 text-white ring-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' 
                                        : 'bg-white text-gray-900 ring-gray-300 hover:bg-gray-50',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Tips -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500 text-white">
                            <span class="font-bold">i</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Pro Tip: Bulk Moderation</h3>
                        <p class="mt-1 text-sm text-gray-600">You can select multiple reviews using the checkboxes (visible on hover) to perform bulk approvals or deletions. This can save up to 40% of moderation time.</p>
                    </div>
                </div>

                <div class="rounded-xl border border-orange-100 bg-orange-50 p-4 flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-white">
                            <span class="font-bold">✨</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Smart Auto-Reply Active</h3>
                        <p class="mt-1 text-sm text-gray-600">ShopWave AI is currently suggesting draft replies for 5-star reviews without comments. Review and approve them in the "Pending" tab.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
