<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CogIcon, GlobeAltIcon, EnvelopeIcon, LockClosedIcon, KeyIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    settings: Object,
});

// Helper to get value safely
const getSetting = (group, key, defaultVal = '') => {
    return props.settings[group]?.find(s => s.key === key)?.value || defaultVal;
};

const form = useForm({
    site_name: getSetting('general', 'site_name', 'ShopWave Official'),
    contact_email: getSetting('general', 'contact_email', 'hello@shopwave.com'),
    seo_description: getSetting('general', 'seo_description', 'The ultimate destination for modern lifestyle products and digital goods.'),
    
    site_logo: null,
    site_favicon: null,
    
    currency: getSetting('regional', 'currency', 'USD ($)'),
    timezone: getSetting('regional', 'timezone', '(GMT-08:00) Pacific Time'),
    locale: getSetting('regional', 'locale', 'English (US)'),
});

// Previews
const logoPreview = ref(getSetting('brand', 'site_logo'));
const faviconPreview = ref(getSetting('brand', 'site_favicon'));

const handleFileChange = (field, event) => {
    const file = event.target.files[0];
    if (file) {
        form[field] = file;
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            if (field === 'site_logo') logoPreview.value = e.target.result;
            if (field === 'site_favicon') faviconPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};

const activeTab = ref('General');
const tabs = ['General', 'Localization', 'Email Settings', 'Advanced'];
const navItems = [
    { name: 'General', icon: CogIcon, current: true },
    { name: 'Localization', icon: GlobeAltIcon, current: false },
    { name: 'Email Settings', icon: EnvelopeIcon, current: false },
];
const securityItems = [
    { name: 'Permissions', icon: LockClosedIcon, current: false },
    { name: 'API Keys', icon: KeyIcon, current: false },
];
</script>

<template>
    <AdminLayout>
        <Head title="General Settings" />

        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div>
                <Breadcrumb 
                    :items="[
                        { label: 'Settings', href: route('admin.settings.index') },
                        { label: 'General Settings' }
                    ]" 
                    class="mb-2"
                />
                <h1 class="text-3xl font-bold text-gray-900">General Settings</h1>
                <p class="mt-2 text-gray-500">Manage your shop identity, global brand assets, and core platform behavior.</p>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a v-for="tab in tabs" :key="tab" href="#" 
                       :class="[activeTab === tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                       @click.prevent="activeTab = tab">
                        {{ tab }}
                    </a>
                </nav>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Navigation (Desktop) -->
                <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">
                    <nav class="space-y-8">
                        <div>
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Platform</h3>
                            <div class="mt-2 space-y-1">
                                <a v-for="item in navItems" :key="item.name" href="#" 
                                   :class="[item.current ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium rounded-md']">
                                    <component :is="item.icon" :class="[item.current ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']" aria-hidden="true" />
                                    <span class="truncate">{{ item.name }}</span>
                                </a>
                            </div>
                        </div>
                        <div>
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</h3>
                            <div class="mt-2 space-y-1">
                                <a v-for="item in securityItems" :key="item.name" href="#" 
                                   :class="[item.current ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium rounded-md']">
                                    <span class="truncate">{{ item.name }}</span>
                                </a>
                            </div>
                        </div>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="flex-1">
                    <form @submit.prevent="submit" class="space-y-10">
                        
                        <!-- Site Identity -->
                        <section>
                            <h2 class="text-lg font-medium text-gray-900">Site Identity</h2>
                            <p class="mt-1 text-sm text-gray-500">How your store appears to customers and search engines.</p>
                            
                            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                                    <div class="mt-1">
                                        <input type="text" name="site_name" id="site_name" v-model="form.site_name"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                                    <div class="mt-1">
                                        <input type="email" name="contact_email" id="contact_email" v-model="form.contact_email"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label for="seo_description" class="block text-sm font-medium text-gray-700">SEO Description</label>
                                    <div class="mt-1">
                                        <textarea id="seo_description" name="seo_description" rows="3" v-model="form.seo_description"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-400">Used for Google search snippets and social sharing.</p>
                                </div>
                            </div>
                        </section>

                        <!-- Brand Assets -->
                        <section class="pt-10 border-t border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Brand Assets</h2>
                            <p class="mt-1 text-sm text-gray-500">Upload your brand logo and favicon used throughout the platform.</p>

                            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Main Logo</label>
                                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-10 bg-gray-50 hover:bg-gray-100 transition-colors relative">
                                        <div class="text-center">
                                            <div v-if="logoPreview" class="mb-4">
                                                <img :src="logoPreview" alt="Logo Preview" class="mx-auto h-12 object-contain" />
                                            </div>
                                            <div v-else class="mx-auto h-12 w-12 text-gray-300">
                                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="site_logo" class="relative cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:text-blue-500">
                                                    <span>Upload a file</span>
                                                    <input id="site_logo" name="site_logo" type="file" class="sr-only" accept="image/*" @change="handleFileChange('site_logo', $event)" />
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, SVG up to 2MB (Recommended: 400x100px)</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Favicon</label>
                                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-10 bg-gray-50 hover:bg-gray-100 transition-colors relative">
                                        <div class="text-center">
                                            <div v-if="faviconPreview" class="mb-4">
                                                <img :src="faviconPreview" alt="Favicon Preview" class="mx-auto h-8 w-8 object-contain" />
                                            </div>
                                            <div v-else class="mx-auto h-8 w-8 text-gray-300">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="site_favicon" class="relative cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:text-blue-500">
                                                    <span>Upload a file</span>
                                                    <input id="site_favicon" name="site_favicon" type="file" class="sr-only" accept="image/*" @change="handleFileChange('site_favicon', $event)" />
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, ICO up to 1MB (Recommended: 32x32px)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Regional Defaults -->
                        <section class="pt-10 border-t border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Regional Defaults</h2>
                            <p class="mt-1 text-sm text-gray-500">Configure default settings for new users and storefront guests.</p>
                            
                            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700">Default Currency</label>
                                    <select id="currency" name="currency" v-model="form.currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option>USD ($)</option>
                                        <option>EUR (€)</option>
                                        <option>GBP (£)</option>
                                        <option>VND (₫)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                                    <select id="timezone" name="timezone" v-model="form.timezone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option>(GMT-08:00) Pacific Time</option>
                                        <option>(GMT+00:00) UTC</option>
                                        <option>(GMT+07:00) Indochina Time</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="locale" class="block text-sm font-medium text-gray-700">Language</label>
                                    <select id="locale" name="locale" v-model="form.locale" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option>English (US)</option>
                                        <option>Vietnamese</option>
                                        <option>French</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <!-- Footer Actions -->
                        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 py-4 px-6 lg:pl-72 flex items-center justify-between z-10">
                            <div class="flex items-center text-sm text-green-600">
                                <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                All changes are synced locally
                            </div>
                            <div class="flex items-center gap-4">
                                <button type="button" class="text-sm font-medium text-gray-700 hover:text-gray-900" @click="form.reset()">
                                    Discard Changes
                                </button>
                                <button type="submit" 
                                    :disabled="form.processing"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                                    Save Settings
                                </button>
                            </div>
                        </div>
                        
                        <!-- Spacer for fixed footer -->
                        <div class="h-16"></div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
