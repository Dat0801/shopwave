<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const notifications = ref([]);
const isLoading = ref(false);
const showNotifications = ref(false);

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.read_at).length;
});

const groupedNotifications = computed(() => {
    const grouped = {
        new_order: [],
        new_contact: [],
        low_stock: [],
        new_post: [],
        other: [],
    };

    notifications.value.forEach(notification => {
        const type = notification.data?.type || 'other';
        if (grouped[type]) {
            grouped[type].push(notification);
        } else {
            grouped[type].push(notification);
        }
    });

    return grouped;
});

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

const fetchNotifications = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/notifications');
        notifications.value = response.data.notifications;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    } finally {
        isLoading.value = false;
    }
};

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/notifications/${notificationId}/read`);
        // Cập nhật trạng thái read_at mà không xóa thông báo
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification) {
            notification.read_at = new Date();
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
};

const handleNotificationClick = async (notification) => {
    // Mark as read first
    await markAsRead(notification.id);
    
    const data = notification.data || {};
    
    // Then navigate
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
    
    showNotifications.value = false;
};

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/read-all');
        // Cập nhật tất cả thông báo thành đã đọc mà không xóa chúng
        notifications.value.forEach(n => {
            n.read_at = new Date();
        });
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

const deleteNotification = async (notificationId) => {
    try {
        await axios.delete(`/notifications/${notificationId}`);
        notifications.value = notifications.value.filter(n => n.id !== notificationId);
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
};

onMounted(() => {
    fetchNotifications();
    
    // Refresh notifications every 30 seconds
    const interval = setInterval(fetchNotifications, 30000);
    
    // Cleanup interval on unmount
    return () => clearInterval(interval);
});
</script>

<template>
    <div class="relative">
        <!-- Notification Bell Button -->
        <button
            @click="showNotifications = !showNotifications"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
            :title="`${unreadCount} unread notifications`"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            <!-- Unread Badge -->
            <span 
                v-if="unreadCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
            >
                {{ unreadCount }}
            </span>
        </button>

        <!-- Notifications Dropdown -->
        <div
            v-if="showNotifications"
            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl z-50"
        >
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-sm text-blue-600 hover:text-blue-700"
                >
                    Mark all as read
                </button>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="px-4 py-8 text-center">
                <p class="text-gray-500">Loading notifications...</p>
            </div>

            <!-- Notifications List -->
            <div v-else-if="notifications.length > 0" class="max-h-96 overflow-y-auto">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition"
                    :class="{ 'bg-blue-50': !notification.read_at }"
                    @click="handleNotificationClick(notification)"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <span class="text-xl flex-shrink-0">
                            {{ getNotificationIcon(notification.data?.type) }}
                        </span>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">
                                {{ getNotificationTitle(notification) }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ getNotificationMessage(notification) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ new Date(notification.created_at).toLocaleString() }}
                            </p>
                        </div>

                        <!-- Unread Indicator -->
                        <div v-if="!notification.read_at" class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2"></div>

                        <!-- Delete Button -->
                        <button
                            @click.stop="deleteNotification(notification.id)"
                            class="flex-shrink-0 text-gray-400 hover:text-red-600"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="px-4 py-8 text-center">
                <p class="text-gray-500">No notifications yet</p>
            </div>

            <!-- Footer -->
            <div v-if="notifications.length > 0" class="px-4 py-3 border-t border-gray-200">
                <button
                    @click="showNotifications = false; $router.visit(route('notifications.index'))"
                    class="w-full text-center text-sm text-blue-600 hover:text-blue-700 py-2"
                >
                    View all notifications
                </button>
            </div>
        </div>

        <!-- Click Outside to Close -->
        <div
            v-if="showNotifications"
            @click="showNotifications = false"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<style scoped>
/* Add smooth transition for dropdown */
transition: all 0.2s ease-in-out;
</style>
