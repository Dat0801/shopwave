<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumb from '@/Components/Admin/Breadcrumb.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CogIcon, GlobeAltIcon, EnvelopeIcon, LockClosedIcon, KeyIcon } from '@heroicons/vue/24/outline';
import systemOptions from '@/Data/systemOptions.json';

const props = defineProps({
    settings: Object,
});

// Helper to get value safely
const getSetting = (group, key, defaultVal = '') => {
    // Check DB setting first
    const dbValue = props.settings[group]?.find(s => s.key === key)?.value;
    if (dbValue !== undefined && dbValue !== null) return dbValue;
    
    return defaultVal;
};

const form = useForm({
    site_name: getSetting('general', 'site_name', 'ShopWave Official'),
    contact_email: getSetting('general', 'contact_email', 'hello@shopwave.com'),
    contact_phone: getSetting('general', 'contact_phone', ''),
    address: getSetting('general', 'address', ''),
    seo_description: getSetting('general', 'seo_description', 'The ultimate destination for modern lifestyle products and digital goods.'),
    
    site_logo: null,
    site_favicon: null,
    
    currency: getSetting('regional', 'currency', 'USD'),
    timezone: getSetting('regional', 'timezone', 'America/Los_Angeles'),
    locale: getSetting('regional', 'locale', 'en'),

    // Email Settings
    mail_mailer: getSetting('email', 'mail_mailer'),
    mail_host: getSetting('email', 'mail_host'),
    mail_port: getSetting('email', 'mail_port'),
    mail_username: getSetting('email', 'mail_username'),
    mail_password: getSetting('email', 'mail_password'),
    mail_encryption: getSetting('email', 'mail_encryption'),
    mail_from_address: getSetting('email', 'mail_from_address'),
    mail_from_name: getSetting('email', 'mail_from_name'),
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
// Remove redundant horizontal tabs
const navItems = [
    { name: 'General', icon: CogIcon, id: 'General' },
    { name: 'Localization', icon: GlobeAltIcon, id: 'Localization' },
    { name: 'Email Settings', icon: EnvelopeIcon, id: 'Email Settings' },
];
const securityItems = [
    { name: 'Permissions', icon: LockClosedIcon, id: 'Permissions' },
    { name: 'API Keys', icon: KeyIcon, id: 'API Keys' },
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
                <h1 class="text-3xl font-bold text-gray-900">{{ activeTab }} Settings</h1>
                <p class="mt-2 text-gray-500">Manage your shop identity, global brand assets, and core platform behavior.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Navigation (Desktop) -->
                <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">
                    <nav class="space-y-8">
                        <div>
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Platform</h3>
                            <div class="mt-2 space-y-1">
                                <button v-for="item in navItems" :key="item.name" @click="activeTab = item.id"
                                   type="button"
                                   :class="[activeTab === item.id ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'w-full group flex items-center px-3 py-2 text-sm font-medium rounded-md']">
                                    <component :is="item.icon" :class="[activeTab === item.id ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']" aria-hidden="true" />
                                    <span class="truncate">{{ item.name }}</span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</h3>
                            <div class="mt-2 space-y-1">
                                <button v-for="item in securityItems" :key="item.name" @click="activeTab = item.id"
                                   type="button"
                                   :class="[activeTab === item.id ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'w-full group flex items-center px-3 py-2 text-sm font-medium rounded-md']">
                                    <component :is="item.icon" :class="[activeTab === item.id ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']" aria-hidden="true" />
                                    <span class="truncate">{{ item.name }}</span>
                                </button>
                            </div>
                        </div>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="flex-1">
                    <form @submit.prevent="submit" class="space-y-10">
                        
                        <!-- Site Identity (General) -->
                        <div v-if="activeTab === 'General'">
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

                                    <div class="sm:col-span-3">
                                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                                        <div class="mt-1">
                                            <input type="text" name="contact_phone" id="contact_phone" v-model="form.contact_phone"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                        </div>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700">Contact Address</label>
                                        <div class="mt-1">
                                            <input type="text" name="address" id="address" v-model="form.address"
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

                            <!-- Brand Assets (General) -->
                            <section class="pt-10 border-t border-gray-200 mt-10">
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
                        </div>

                        <!-- Regional Defaults (Localization) -->
                        <div v-if="activeTab === 'Localization'">
                            <section>
                                <h2 class="text-lg font-medium text-gray-900">Regional Defaults</h2>
                                <p class="mt-1 text-sm text-gray-500">Configure default settings for new users and storefront guests.</p>
                                
                                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                                    <div>
                                        <label for="currency" class="block text-sm font-medium text-gray-700">Default Currency</label>
                                        <select id="currency" name="currency" v-model="form.currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option v-for="currency in systemOptions.currencies" :key="currency.code" :value="currency.code">
                                                {{ currency.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                                        <select id="timezone" name="timezone" v-model="form.timezone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option v-for="timezone in systemOptions.timezones" :key="timezone.value" :value="timezone.value">
                                                {{ timezone.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="locale" class="block text-sm font-medium text-gray-700">Language</label>
                                        <select id="locale" name="locale" v-model="form.locale" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option v-for="locale in systemOptions.locales" :key="locale.code" :value="locale.code">
                                                {{ locale.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Email Settings -->
                        <div v-if="activeTab === 'Email Settings'">
                            <section>
                                <h2 class="text-lg font-medium text-gray-900">Email Settings</h2>
                                <p class="mt-1 text-sm text-gray-500">Configure your email service provider (SMTP).</p>
                                
                                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="mail_mailer" class="block text-sm font-medium text-gray-700">Mailer</label>
                                        <input type="text" id="mail_mailer" v-model="form.mail_mailer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="smtp" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label for="mail_host" class="block text-sm font-medium text-gray-700">Host</label>
                                        <input type="text" id="mail_host" v-model="form.mail_host" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="smtp.mailtrap.io" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="mail_port" class="block text-sm font-medium text-gray-700">Port</label>
                                        <input type="text" id="mail_port" v-model="form.mail_port" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="2525" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="mail_username" class="block text-sm font-medium text-gray-700">Username</label>
                                        <input type="text" id="mail_username" v-model="form.mail_username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="mail_password" class="block text-sm font-medium text-gray-700">Password</label>
                                        <input type="password" id="mail_password" v-model="form.mail_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                                        <input type="text" id="mail_encryption" v-model="form.mail_encryption" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="tls" />
                                    </div>
                                    <div class="sm:col-span-4"></div>
                                    <div class="sm:col-span-3">
                                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Address</label>
                                        <input type="email" id="mail_from_address" v-model="form.mail_from_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700">From Name</label>
                                        <input type="text" id="mail_from_name" v-model="form.mail_from_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Security Placeholders -->
                        <div v-if="['Permissions', 'API Keys'].includes(activeTab)">
                            <div class="text-center py-20 text-gray-500">
                                <LockClosedIcon class="h-12 w-12 mx-auto text-gray-300 mb-4" />
                                <h3 class="text-lg font-medium text-gray-900">Coming Soon</h3>
                                <p class="mt-1">This feature is currently under development.</p>
                            </div>
                        </div>

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
