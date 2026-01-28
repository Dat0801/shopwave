<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Mail, Phone, MapPin, Facebook, Twitter, Globe, Send } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    page: Object
});

const page = usePage();
const flash = computed(() => page.props.flash);

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const submit = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head :title="props.page?.meta?.seo_title || 'Contact Us'">
        <meta v-if="props.page?.meta?.seo_description" name="description" :content="props.page.meta.seo_description" />
    </Head>

    <ShopLayout>
        <!-- Contact Section -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Left: Info (Blue) -->
                <div class="bg-blue-600 p-10 text-white flex flex-col justify-between relative overflow-hidden">
                    <!-- Background decoration -->
                     <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-blue-500 rounded-full opacity-50 blur-3xl"></div>
                     <div class="absolute top-10 right-10 w-32 h-32 bg-blue-400 rounded-full opacity-20 blur-2xl"></div>

                    <div class="relative z-10">
                        <h1 class="text-4xl font-bold mb-6">{{ props.page?.meta?.header_title || "Let's connect." }}</h1>
                        <p class="text-blue-100 text-lg mb-12 max-w-md">
                            {{ props.page?.meta?.header_description || "We're here to help you ride the wave of the latest fashion trends. Reach out to us for any queries about orders, styling, or collaborations." }}
                        </p>

                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                                    <Mail class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">Email Us</h3>
                                    <p class="text-blue-100">support@shopwave.com</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                                    <Phone class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">Call Us</h3>
                                    <p class="text-blue-100">+1 (555) 123-4567</p>
                                    <p class="text-sm text-blue-200 mt-1">Mon-Fri: 9am - 6pm EST</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                                    <MapPin class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">Visit Us</h3>
                                    <p class="text-blue-100">123 Fashion Ave, Soho, NY 10012</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 mt-12">
                        <h4 class="font-bold tracking-widest text-sm uppercase mb-6 opacity-80">Follow Our Wave</h4>
                        <div class="flex gap-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                                <span class="sr-only">Instagram</span>
                                <!-- Instagram Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                                <Twitter class="w-5 h-5" />
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                                <Globe class="w-5 h-5" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Form (White) -->
                <div class="p-10 lg:p-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Send us a message</h2>
                    <p class="text-gray-500 mb-8">Fill out the form below and our team will get back to you within 24 hours.</p>

                    <div v-if="flash.success" class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                        {{ flash.success }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Full Name</label>
                                <input type="text" id="name" v-model="form.name" placeholder="John Doe" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50">
                                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-900 mb-2">Email Address</label>
                                <input type="email" id="email" v-model="form.email" placeholder="john@example.com" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50">
                                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-bold text-gray-900 mb-2">Subject</label>
                            <div class="relative">
                                <select id="subject" v-model="form.subject" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50 appearance-none">
                                    <option value="" disabled selected>Select a subject</option>
                                    <template v-if="props.page?.meta?.subjects && props.page.meta.subjects.length > 0">
                                        <option v-for="subject in props.page.meta.subjects" :key="subject" :value="subject">
                                            {{ subject }}
                                        </option>
                                    </template>
                                    <template v-else>
                                        <option value="Order Support">Order Support</option>
                                        <option value="Returns & Exchanges">Returns & Exchanges</option>
                                        <option value="Product Inquiry">Product Inquiry</option>
                                        <option value="Collaboration">Collaboration</option>
                                        <option value="Other">Other</option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <div v-if="form.errors.subject" class="text-red-500 text-sm mt-1">{{ form.errors.subject }}</div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-gray-900 mb-2">Your Message</label>
                            <textarea id="message" v-model="form.message" rows="5" placeholder="How can we help you?" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-gray-50"></textarea>
                            <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors shadow-lg shadow-blue-600/30 disabled:opacity-50">
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                            <Send v-if="!form.processing" class="w-4 h-4 ml-2" />
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 flex items-center justify-between border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <MapPin class="w-5 h-5 text-blue-600" />
                    <h3 class="font-bold text-gray-900">Our Soho Flagship Store</h3>
                </div>
                <a href="#" class="text-blue-600 font-semibold text-sm hover:underline flex items-center gap-1">
                    Get Directions
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
            <div class="relative h-[400px] bg-gray-200">
                 <!-- Placeholder Map Image using CSS Pattern or grayscale filter -->
                 <div class="absolute inset-0 bg-[url('https://cartodb-basemaps-a.global.ssl.fastly.net/light_all/13/2411/3078.png')] bg-cover bg-center grayscale contrast-125 opacity-80"></div>
                 
                 <!-- Map Marker -->
                 <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                    <div class="relative">
                        <div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg z-10 relative"></div>
                        <div class="w-12 h-12 bg-blue-500/30 rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 animate-ping"></div>
                        
                        <!-- Tooltip -->
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-blue-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow-lg whitespace-nowrap">
                            ShopWave
                            <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-blue-600"></div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </ShopLayout>
</template>
