<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    contacts: Object,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    from: props.filters?.from || '',
    to: props.filters?.to || '',
});

const deleteForm = useForm({});
const selectedContact = ref(null);
const showDeleteModal = ref(false);

const submitFilters = () => {
    filterForm.get(route('admin.contacts.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['contacts', 'filters'],
    });
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'new':
            return 'bg-blue-100 text-blue-800';
        case 'read':
            return 'bg-yellow-100 text-yellow-800';
        case 'replied':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'new': 'New',
        'read': 'Read',
        'replied': 'Replied',
    };
    return labels[status] || status;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const openDeleteModal = (contact) => {
    selectedContact.value = contact;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    deleteForm.delete(route('admin.contacts.destroy', selectedContact.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedContact.value = null;
        },
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Contact Messages" />

        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Admin', href: route('admin.dashboard') },
                            { label: 'Contacts' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">
                        Contact Messages
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        View and manage customer contact form submissions
                    </p>
                </div>
                <div class="flex gap-3">
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Filters -->
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="filterForm.search"
                            @keydown.enter="submitFilters"
                            type="text"
                            class="block w-full rounded-lg border-gray-200 bg-gray-50 py-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                            placeholder="Search by name, email, or subject..."
                        />
                    </div>
                    <select
                        v-model="filterForm.status"
                        @change="submitFilters"
                        class="rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Status</option>
                        <option value="new">New</option>
                        <option value="read">Read</option>
                        <option value="replied">Replied</option>
                    </select>
                    <input
                        v-model="filterForm.from"
                        @change="submitFilters"
                        type="date"
                        class="rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <input
                        v-model="filterForm.to"
                        @change="submitFilters"
                        type="date"
                        class="rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="contact in contacts.data" :key="contact.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ contact.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ contact.email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                    {{ contact.subject }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusBadgeClass(contact.status)]">
                                        {{ getStatusLabel(contact.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(contact.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('admin.contacts.show', contact.id)"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                        >
                                            View
                                        </Link>
                                        <button
                                            @click="openDeleteModal(contact)"
                                            class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-700 shadow-sm hover:bg-red-50"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="contacts.data.length === 0" class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">No contacts</h3>
                    <p class="mt-2 text-sm text-gray-500">No contact messages found.</p>
                </div>

                <!-- Pagination -->
                <div v-if="contacts.data.length > 0" class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-medium">{{ contacts.from }}</span> to <span class="font-medium">{{ contacts.to }}</span> of <span class="font-medium">{{ contacts.total }}</span> results
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-if="contacts.prev_page_url"
                            :href="contacts.prev_page_url"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <button
                            v-else
                            disabled
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed"
                        >
                            Previous
                        </button>

                        <Link
                            v-if="contacts.next_page_url"
                            :href="contacts.next_page_url"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Next
                        </Link>
                        <button
                            v-else
                            disabled
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="rounded-lg bg-white shadow-lg max-w-md w-full mx-4 p-6">
                <h3 class="text-lg font-medium text-gray-900">Delete Contact</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Are you sure you want to delete this contact message? This action cannot be undone.
                </p>
                <div class="mt-6 flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmDelete"
                        :disabled="deleteForm.processing"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-500 disabled:opacity-50"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
