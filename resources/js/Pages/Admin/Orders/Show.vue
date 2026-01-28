<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { 
    PrinterIcon, 
    XCircleIcon, 
    TruckIcon,
    MapPinIcon,
    EnvelopeIcon,
    PhoneIcon,
    CreditCardIcon,
    CalendarIcon,
    CheckCircleIcon,
    ShoppingBagIcon,
    ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    order: Object,
    customerStats: Object,
    timeline: Array,
});

const noteForm = useForm({
    notes: props.order.notes || '',
});

const statusForm = useForm({
    status: '',
});

const updateStatus = (status) => {
    if (confirm(`Are you sure you want to mark this order as ${status}?`)) {
        statusForm.status = status;
        statusForm.patch(route('admin.orders.update', props.order.id), {
            preserveScroll: true,
        });
    }
};

const saveNote = () => {
    noteForm.patch(route('admin.orders.update', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: show toast
        },
    });
};

const printInvoice = () => {
    window.print();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount || 0);
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'processing': return 'bg-blue-100 text-blue-800';
        case 'paid': return 'bg-green-100 text-green-800';
        case 'shipped': return 'bg-purple-100 text-purple-800';
        case 'completed': return 'bg-emerald-100 text-emerald-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="`Order #${order.id} - Details`" />

        <template #header>
            <div class="flex flex-col gap-4">
                <Breadcrumb 
                    :items="[
                        { label: 'Admin', href: route('admin.dashboard') },
                        { label: 'Orders', href: route('admin.orders.index') },
                        { label: `Order #${order.id}` }
                    ]" 
                />
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold text-gray-900">
                                Order #SW-{{ order.id }}
                            </h1>
                            <span 
                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide"
                                :class="getStatusColor(order.status)"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 flex items-center gap-2">
                            <span>Placed on {{ formatDate(order.created_at) }}</span>
                            <span v-if="order.transaction_id" class="text-gray-300">•</span>
                            <span v-if="order.transaction_id">Transaction ID: {{ order.transaction_id }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3 print:hidden">
                        <button 
                            @click="printInvoice"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-all"
                        >
                            <PrinterIcon class="w-4 h-4" />
                            Print Invoice
                        </button>
                        
                        <button 
                            v-if="order.status !== 'cancelled' && order.status !== 'completed'"
                            @click="updateStatus('cancelled')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 shadow-sm transition-all"
                        >
                            <XCircleIcon class="w-4 h-4" />
                            Cancel Order
                        </button>
                        
                        <button 
                            v-if="order.status === 'processing' || order.status === 'paid'"
                            @click="updateStatus('shipped')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 shadow-sm transition-all"
                        >
                            <TruckIcon class="w-4 h-4" />
                            Ship Order
                        </button>

                        <button 
                            v-if="order.status === 'shipped'"
                            @click="updateStatus('completed')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 shadow-sm transition-all"
                        >
                            <CheckCircleIcon class="w-4 h-4" />
                            Mark Completed
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                        <h2 class="text-base font-semibold text-gray-900">
                            Order Items ({{ order.items.length }})
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-gray-500 uppercase text-xs">
                                    <th class="px-6 py-3 font-medium">Product</th>
                                    <th class="px-6 py-3 font-medium text-center">Qty</th>
                                    <th class="px-6 py-3 font-medium text-right">Price</th>
                                    <th class="px-6 py-3 font-medium text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="item in order.items" :key="item.id" class="group hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 flex-shrink-0 rounded-lg bg-gray-100 overflow-hidden border border-gray-200">
                                                <img 
                                                    v-if="item.product?.images?.[0]" 
                                                    :src="item.product.images[0]" 
                                                    class="h-full w-full object-cover" 
                                                    alt=""
                                                />
                                                <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                                                    <ShoppingBagIcon class="w-6 h-6" />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ item.product?.name || 'Unknown Product' }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">SKU: {{ item.product?.sku || 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">{{ item.quantity }}</td>
                                    <td class="px-6 py-4 text-right text-gray-900">{{ formatCurrency(item.price) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900">{{ formatCurrency(item.price * item.quantity) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Payment Summary -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Payment Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">{{ formatCurrency(order.total_price - (order.shipping_cost || 0) - (order.tax_amount || 0)) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping (Standard Express)</span>
                            <span class="font-medium text-gray-900">{{ formatCurrency(order.shipping_cost) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Estimated Tax</span>
                            <span class="font-medium text-gray-900">{{ formatCurrency(order.tax_amount) }}</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                            <span class="text-base font-bold text-gray-900">Total</span>
                            <span class="text-xl font-bold text-blue-600">{{ formatCurrency(order.total_price) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-gray-50 rounded-lg p-4 flex items-center justify-between border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-white p-2 rounded border border-gray-200">
                                <CreditCardIcon class="w-6 h-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Payment Method</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ order.payment_method === 'stripe' ? 'Credit Card' : (order.payment_method || 'Unknown') }}
                                </p>
                            </div>
                        </div>
                        <span 
                            class="px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wide"
                            :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700'"
                        >
                            {{ order.payment_status || 'Unpaid' }}
                        </span>
                    </div>
                </section>

                <!-- Order Timeline -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-6">Order Timeline</h2>
                    <div class="relative pl-4 border-l-2 border-gray-100 space-y-8">
                        <div v-for="(event, index) in timeline" :key="index" class="relative">
                            <!-- Dot -->
                            <div 
                                class="absolute -left-[21px] top-0 h-10 w-10 rounded-full flex items-center justify-center ring-4 ring-white"
                                :class="index === 0 ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600'"
                            >
                                <component :is="event.icon === 'cart' ? ShoppingBagIcon : (event.icon === 'credit-card' ? CreditCardIcon : (event.icon === 'package' ? TruckIcon : CheckCircleIcon))" class="w-5 h-5" />
                            </div>
                            
                            <div class="pl-6">
                                <h3 class="text-sm font-bold text-gray-900">{{ event.title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ event.description }}</p>
                                <p class="text-xs font-medium text-blue-600 mt-2 uppercase tracking-wide">{{ formatDate(event.date) }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Customer Info -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Customer</h2>
                        <Link :href="route('admin.customers.index')" class="text-xs font-medium text-blue-600 hover:text-blue-700">View Profile</Link>
                    </div>
                    
                    <div class="flex items-start gap-4 mb-6">
                        <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                            {{ order.user?.name?.charAt(0) || '?' }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ order.user?.name || 'Guest User' }}</div>
                            <div class="text-xs text-gray-500">Customer since {{ formatDate(customerStats.customer_since).split(',')[0] }}</div>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3 text-gray-600">
                            <EnvelopeIcon class="w-4 h-4" />
                            <a :href="`mailto:${order.user?.email}`" class="hover:text-blue-600 transition-colors">{{ order.user?.email }}</a>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <PhoneIcon class="w-4 h-4" />
                            <span>{{ order.user?.phone || 'No phone number' }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</div>
                            <div class="mt-1 text-lg font-bold text-gray-900">{{ customerStats.total_orders }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lifetime Value</div>
                            <div class="mt-1 text-lg font-bold text-blue-600">{{ formatCurrency(customerStats.total_spent) }}</div>
                        </div>
                    </div>
                </section>

                <!-- Shipping Address -->
                <section v-if="order.shipping_address" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Shipping Address</h2>
                        <button class="text-xs font-medium text-blue-600 hover:text-blue-700">Edit</button>
                    </div>
                    
                    <div class="mb-4">
                        <div class="font-bold text-gray-900">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</div>
                        <div class="text-sm text-gray-600 mt-1 leading-relaxed">
                            {{ order.shipping_address.address }}<br>
                            <span v-if="order.shipping_address.suite">{{ order.shipping_address.suite }}<br></span>
                            {{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.postal_code }}<br>
                            {{ order.shipping_address.country || 'United States' }}
                        </div>
                    </div>

                    <!-- Map Placeholder -->
                    <div class="h-24 w-full bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gray-200 opacity-50 pattern-grid-lg"></div>
                        <MapPinIcon class="w-6 h-6 text-gray-400 z-10" />
                        <span class="text-xs text-gray-500 z-10 ml-2 font-medium">Map Preview</span>
                    </div>
                </section>

                <!-- Shipping Method -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Shipping Method</h2>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 flex items-start gap-3">
                        <div class="bg-white p-1.5 rounded-md shadow-sm text-blue-600">
                            <TruckIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 text-sm">{{ order.shipping_method || 'Standard Delivery' }}</div>
                            <div class="text-xs text-blue-700 mt-0.5">Estimated: 3-5 Business Days</div>
                        </div>
                    </div>
                </section>

                <!-- Internal Notes -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Internal Notes</h2>
                    <form @submit.prevent="saveNote">
                        <textarea
                            v-model="noteForm.notes"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm resize-none bg-gray-50"
                            placeholder="Add a private note for other staff..."
                        ></textarea>
                        <div class="mt-3">
                            <button
                                type="submit"
                                :disabled="noteForm.processing"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                            >
                                <span v-if="noteForm.processing">Saving...</span>
                                <span v-else>Save Note</span>
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
@media print {
    button, nav {
        display: none !important;
    }
}
</style>
