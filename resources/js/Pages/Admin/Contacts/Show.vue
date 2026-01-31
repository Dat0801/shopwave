<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    contact: Object,
});

const statusForm = useForm({
    status: props.contact.status,
});

const deleteForm = useForm({});
const showDeleteModal = ref(false);

const updateStatus = () => {
    statusForm.patch(route('admin.contacts.update-status', props.contact.id), {
        preserveScroll: true,
    });
};

const confirmDelete = () => {
    deleteForm.delete(route('admin.contacts.destroy', props.contact.id), {
        onSuccess: () => {
            // Redirect handled by backend
        },
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
        month: 'long',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin - View Contact" />

        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Admin', href: route('admin.dashboard') },
                            { label: 'Contacts', href: route('admin.contacts.index') },
                            { label: 'View' }
                        ]" 
                        class="mb-1"
                    />
                    <h1 class="text-2xl font-bold text-gray-900">
                        Contact Message
                    </h1>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('admin.contacts.index')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Back
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Contact Details Card -->
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">{{ contact.name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ contact.email }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select
                                    v-model="statusForm.status"
                                    @change="updateStatus"
                                    :class="[
                                        'rounded-lg border text-sm font-medium py-2 px-3',
                                        getStatusBadgeClass(statusForm.status),
                                    ]"
                                >
                                    <option value="new">New</option>
                                    <option value="read">Read</option>
                                    <option value="replied">Replied</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <!-- Metadata -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                            <p class="text-sm text-gray-900">{{ contact.subject }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date Received</label>
                            <p class="text-sm text-gray-900">{{ formatDate(contact.created_at) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                            <a :href="`mailto:${contact.email}`" class="text-sm text-blue-600 hover:underline">
                                {{ contact.email }}
                            </a>
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Message</label>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap border border-gray-200">
                            {{ contact.message }}
                        </div>
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
