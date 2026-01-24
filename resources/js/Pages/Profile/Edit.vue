<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    birthday: user.birthday || '',
    bio: user.bio || '',
    avatar: null,
    _method: 'PATCH',
});

const avatarPreview = ref(user.avatar ? `/storage/${user.avatar}` : null);
const avatarInput = ref(null);

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const triggerAvatarUpload = () => {
    avatarInput.value.click();
};

const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // handle success
        },
    });
};
</script>

<template>
    <Head title="Profile" />

    <ProfileLayout>
        <template #breadcrumb>
            <span class="text-gray-900 font-medium">My Profile</span>
        </template>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                <p class="mt-1 text-gray-500">Update your personal information and profile picture.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                View Public Profile
            </button>
        </div>

        <div class="space-y-6">
            <!-- Profile Picture Card -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center gap-6">
                    <div class="relative h-24 w-24 flex-shrink-0">
                        <div class="h-24 w-24 overflow-hidden rounded-full ring-4 ring-gray-50 bg-gray-100">
                            <img 
                                v-if="avatarPreview" 
                                :src="avatarPreview" 
                                class="h-full w-full object-cover" 
                                alt="Profile Preview" 
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-indigo-100 text-indigo-600 font-bold text-3xl">
                                {{ user.name.charAt(0) }}
                            </div>
                        </div>
                        <button 
                            type="button"
                            class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm ring-2 ring-white hover:bg-blue-700"
                            @click="triggerAvatarUpload"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M1 8a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 018.007 3h3.986a2 2 0 011.6.91l.812 1.22A2 2 0 0016.07 6H17a2 2 0 012 2v8a2 2 0 01-2 2H3a2 2 0 01-2-2V8zm13.5 3a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM10 14a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Profile Picture</h3>
                        <p class="mt-1 text-sm text-gray-500">JPG, GIF or PNG. Max size of 800K</p>
                        <div class="mt-4 flex gap-3">
                            <input 
                                type="file" 
                                ref="avatarInput" 
                                class="hidden" 
                                accept="image/*" 
                                @change="handleAvatarChange" 
                            />
                            <button 
                                type="button" 
                                @click="triggerAvatarUpload"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-sm transition-colors"
                            >
                                Upload New Photo
                            </button>
                            <button 
                                type="button" 
                                class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Card -->
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900">Personal Information</h3>
                    
                    <form @submit.prevent="submit" class="mt-6 grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input 
                                v-model="form.name"
                                type="text" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input 
                                v-model="form.email"
                                type="email" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input 
                                v-model="form.phone"
                                type="text" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="+1 (555) 000-0000"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Birthdate</label>
                            <input 
                                v-model="form.birthday"
                                type="date" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            />
                            <p v-if="form.errors.birthday" class="mt-1 text-xs text-red-600">{{ form.errors.birthday }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea 
                                v-model="form.bio"
                                rows="4"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Tell us about yourself..."
                            ></textarea>
                            <p v-if="form.errors.bio" class="mt-1 text-xs text-red-600">{{ form.errors.bio }}</p>
                        </div>
                    </form>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-6 py-4 rounded-b-2xl">
                    <p class="text-xs text-gray-500">Last updated on Oct 24, 2023</p>
                    <div class="flex gap-3">
                        <button 
                            type="button" 
                            class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors"
                            @click="form.reset()"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-sm transition-colors disabled:opacity-50"
                            @click="submit"
                            :disabled="form.processing"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                <div class="flex gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.352-.272-2.636-.759-3.808a.75.75 0 00-.722-.515 11.208 11.208 0 01-7.877-3.08zM12 10.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" />
                            <path d="M9.5 10.5a2.5 2.5 0 115 0V12h2a1 1 0 011 1v6a1 1 0 01-1 1h-8a1 1 0 01-1-1v-6a1 1 0 011-1h2v-1.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Keep your account secure</h3>
                        <p class="mt-1 text-sm text-gray-600">We recommend changing your password every 6 months to ensure your data stays protected. <Link :href="route('password.request')" class="font-medium text-blue-600 hover:text-blue-500">Manage security settings</Link></p>
                    </div>
                </div>
            </div>
        </div>
    </ProfileLayout>
</template>
