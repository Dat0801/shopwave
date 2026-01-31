<script setup>
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    notifications: Array,
    unreadCount: Number,
});

const notifications = ref(props.notifications || []);
const selectedNotifications = ref(new Set());
const isLoading = ref(false);

const getNotificationIcon = (type) => {
    const icons = {
        new_order: '📦',
        new_contact: '📧',
        low_stock: '⚠️',
        new_post: '📝',
    };
    return icons[type] || '🔔';
};

const getNotificationTitle = (notification) => {
    const data = notification.data || {};
    
    switch (data.type) {
        case 'new_order':
            return `New Order ${data.order_number}`;
        case 'new_contact':
            return `New Contact from ${data.sender_name}`;
        case 'low_stock':
            return `Low Stock: ${data.product_name}`;
        case 'new_post':
            return 'New Blog Post';
        default:
            return 'New Notification';
    }
};

const getNotificationMessage = (notification) => {
    const data = notification.data || {};
    
    switch (data.type) {
        case 'new_order':
            return `Order from ${data.customer_name} - $${data.total_price}`;
        case 'new_contact':
            return `${data.subject}`;
        case 'low_stock':
            return `Only ${data.current_stock} units left (threshold: ${data.threshold})`;
        default:
            return 'New notification';
    }
};

const getBadgeColor = (type) => {
    const colors = {
        new_order: 'bg-blue-100 text-blue-800',
        new_contact: 'bg-green-100 text-green-800',
        low_stock: 'bg-yellow-100 text-yellow-800',
        new_post: 'bg-purple-100 text-purple-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/notifications/${notificationId}/read`);
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification) {
            notification.read_at = new Date();
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
};

const handleNotificationClick = async (notification) => {
    const data = notification.data || {};
    
    // Mark as read
    await markAsRead(notification.id);
    
    // Navigate to the relevant page
    switch (data.type) {
        case 'new_order':
            if (data.order_id) {
                router.visit(route('admin.orders.show', data.order_id));
            }
            break;
        case 'new_contact':
            if (data.contact_id) {
                router.visit(route('admin.contacts.show', data.contact_id));
            }
            break;
        case 'low_stock':
            if (data.product_id) {
                router.visit(route('admin.products.show', data.product_id));
            }
            break;
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/read-all');
        notifications.value.forEach(n => n.read_at = new Date());
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

const deleteNotification = async (notificationId) => {
    try {
        await axios.delete(`/notifications/${notificationId}`);
        notifications.value = notifications.value.filter(n => n.id !== notificationId);
        selectedNotifications.value.delete(notificationId);
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
};

const deleteSelectedNotifications = async () => {
    isLoading.value = true;
    try {
        for (const notificationId of selectedNotifications.value) {
            await axios.delete(`/notifications/${notificationId}`);
        }
        notifications.value = notifications.value.filter(
            n => !selectedNotifications.value.has(n.id)
        );
        selectedNotifications.value.clear();
    } catch (error) {
        console.error('Failed to delete notifications:', error);
    } finally {
        isLoading.value = false;
    }
};

const toggleNotificationSelection = (notificationId) => {
    if (selectedNotifications.value.has(notificationId)) {
        selectedNotifications.value.delete(notificationId);
    } else {
        selectedNotifications.value.add(notificationId);
    }
};

const toggleSelectAll = () => {
    if (selectedNotifications.value.size === notifications.value.length) {
        selectedNotifications.value.clear();
    } else {
        notifications.value.forEach(n => selectedNotifications.value.add(n.id));
    }
};

const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read_at);
});

const readNotifications = computed(() => {
    return notifications.value.filter(n => n.read_at);
});
</script>

<template>
    <AdminLayout>
        <Head title="Admin - Notifications" />

        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Breadcrumb 
                        :items="[
                            { label: 'Dashboard', href: route('admin.dashboard') },
                            { label: 'Notifications' }
                        ]"
                    />
                    <h1 class="mt-2 text-3xl font-bold text-gray-900">Thông báo</h1>
                </div>
                <div class="flex gap-2">
                    <button
                        v-if="unreadNotifications.length > 0"
                        @click="markAllAsRead"
                        class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-50 transition"
                    >
                        Đánh dấu tất cả đã đọc
                    </button>
                    <button
                        v-if="selectedNotifications.size > 0"
                        @click="deleteSelectedNotifications"
                        :disabled="isLoading"
                        class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-300 rounded-lg hover:bg-red-50 disabled:opacity-50 transition"
                    >
                        Xóa đã chọn ({{ selectedNotifications.size }})
                    </button>
                </div>
            </div>
        </template>

        <!-- Unread Notifications Section -->
        <div v-if="unreadNotifications.length > 0" class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Chưa đọc ({{ unreadNotifications.length }})
            </h2>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div
                    v-for="notification in unreadNotifications"
                    :key="notification.id"
                    class="px-4 py-4 border-b border-gray-100 last:border-b-0 bg-blue-50 hover:bg-blue-100 cursor-pointer transition group"
                    @click="handleNotificationClick(notification)"
                >
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">
                            {{ getNotificationIcon(notification.data?.type) }}
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-700">
                                    {{ getNotificationTitle(notification) }}
                                </h3>
                                <span :class="['text-xs font-medium px-2 py-1 rounded whitespace-nowrap', getBadgeColor(notification.data?.type)]">
                                    {{ notification.data?.type?.replace('_', ' ').toUpperCase() }}
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2">
                                {{ getNotificationMessage(notification) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ new Date(notification.created_at).toLocaleString('vi-VN') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                            <button
                                @click.stop="deleteNotification(notification.id)"
                                class="text-gray-400 hover:text-red-600 p-2 hover:bg-red-50 rounded transition"
                                title="Xóa thông báo"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex-shrink-0 w-3 h-3 bg-blue-600 rounded-full mt-1"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Read Notifications Section -->
        <div v-if="readNotifications.length > 0">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Đã đọc ({{ readNotifications.length }})
            </h2>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div
                    v-for="notification in readNotifications"
                    :key="notification.id"
                    class="px-4 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition group"
                >
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">
                            {{ getNotificationIcon(notification.data?.type) }}
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-semibold text-gray-700">
                                    {{ getNotificationTitle(notification) }}
                                </h3>
                                <span :class="['text-xs font-medium px-2 py-1 rounded whitespace-nowrap', getBadgeColor(notification.data?.type)]">
                                    {{ notification.data?.type?.replace('_', ' ').toUpperCase() }}
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2">
                                {{ getNotificationMessage(notification) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ new Date(notification.created_at).toLocaleString('vi-VN') }}
                            </p>
                        </div>

                        <button
                            @click="deleteNotification(notification.id)"
                            class="text-gray-400 hover:text-red-600 p-2 hover:bg-red-50 rounded transition flex-shrink-0 opacity-0 group-hover:opacity-100"
                            title="Xóa thông báo"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="notifications.length === 0" class="text-center py-16">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Không có thông báo</h3>
            <p class="mt-1 text-sm text-gray-500">Bạn đã xem hết tất cả thông báo!</p>
        </div>
    </AdminLayout>
</template>
