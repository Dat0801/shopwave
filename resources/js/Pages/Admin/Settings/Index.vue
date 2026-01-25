<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    // General
    site_name: props.settings.general?.find(s => s.key === 'site_name')?.value || 'ShopWave',
    site_description: props.settings.general?.find(s => s.key === 'site_description')?.value || '',
    contact_email: props.settings.general?.find(s => s.key === 'contact_email')?.value || '',
    contact_phone: props.settings.general?.find(s => s.key === 'contact_phone')?.value || '',
    address: props.settings.general?.find(s => s.key === 'address')?.value || '',

    // Social
    facebook_url: props.settings.social?.find(s => s.key === 'facebook_url')?.value || '',
    instagram_url: props.settings.social?.find(s => s.key === 'instagram_url')?.value || '',
    twitter_url: props.settings.social?.find(s => s.key === 'twitter_url')?.value || '',
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Settings" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    System Settings
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <!-- General Settings -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">General Information</h3>
                            
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                                    <div class="mt-1">
                                        <input type="text" v-model="form.site_name" id="site_name" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                                    <div class="mt-1">
                                        <textarea id="site_description" v-model="form.site_description" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                                    <div class="mt-1">
                                        <input type="email" v-model="form.contact_email" id="contact_email" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                                    <div class="mt-1">
                                        <input type="text" v-model="form.contact_phone" id="contact_phone" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <div class="mt-1">
                                        <input type="text" v-model="form.address" id="address" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Settings -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Social Media Links</h3>
                            
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="facebook_url" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                    <div class="mt-1">
                                        <input type="text" v-model="form.facebook_url" id="facebook_url" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="instagram_url" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                    <div class="mt-1">
                                        <input type="text" v-model="form.instagram_url" id="instagram_url" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="twitter_url" class="block text-sm font-medium text-gray-700">Twitter (X) URL</label>
                                    <div class="mt-1">
                                        <input type="text" v-model="form.twitter_url" id="twitter_url" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" :disabled="form.processing">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
