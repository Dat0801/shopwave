<script setup>
import { computed, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useFlash } from '@/Composables/useFlash';

const showingNavigationDropdown = ref(false);

const page = usePage();

const isAdminUser = computed(
    () => page.props.auth.user && page.props.auth.user.role === 'admin',
);

const isProfileRoute = computed(() => route().current('profile.*'));

const isAdminRoute = computed(
    () =>
        isAdminUser.value &&
        (page.props.ziggy?.location?.includes('/admin') ||
            route().current('admin.*')),
);

const { success, error } = useFlash();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50">
            <nav class="border-b border-gray-100 bg-white/80 backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center gap-3">
                            <Link
                                :href="isAdminUser ? route('admin.dashboard') : route('shop.index')"
                                class="flex items-center gap-2"
                            >
                                <ApplicationLogo class="block h-8 w-auto fill-current text-gray-800" />
                                <span class="hidden text-sm font-semibold text-gray-900 sm:inline">
                                    {{ isAdminUser ? 'Admin' : 'Shop' }}
                                </span>
                            </Link>

                            <div
                                v-if="isAdminUser && !isAdminRoute && !isProfileRoute"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('admin.dashboard')"
                                    :active="route().current('admin.*')"
                                >
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            v-if="isAdminUser && !isProfileRoute"
                            :href="route('admin.dashboard')"
                            :active="route().current('admin.*')"
                        >
                            Admin dashboard
                        </ResponsiveNavLink>
                    </div>

                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <div
                v-if="success || error"
                class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8"
            >
                <div
                    v-if="success"
                    class="mb-3 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm"
                >
                    <span class="mt-0.5 h-2 w-2 rounded-full bg-emerald-500" />
                    <p class="flex-1">
                        {{ success }}
                    </p>
                </div>
                <div
                    v-if="error"
                    class="flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm"
                >
                    <span class="mt-0.5 h-2 w-2 rounded-full bg-rose-500" />
                    <p class="flex-1">
                        {{ error }}
                    </p>
                </div>
            </div>

            <div
                v-if="isAdminRoute"
                class="mx-auto flex max-w-7xl gap-8 px-4 pb-10 pt-8 sm:px-6 lg:px-8"
            >
                <aside class="hidden w-64 shrink-0 pt-2 md:block">
                    <div class="sticky top-20 rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-gray-100">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Admin
                        </p>

                        <nav class="space-y-1 text-sm">
                            <Link
                                :href="route('admin.dashboard')"
                                :class="[
                                    'flex items-center gap-2 rounded-xl px-3 py-2.5 font-medium transition',
                                    route().current('admin.dashboard')
                                        ? 'bg-gray-900 text-white shadow-sm'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-md bg-gray-800/90 text-xs text-white"
                                >
                                    AD
                                </span>
                                <span>Dashboard</span>
                            </Link>

                            <Link
                                :href="route('admin.orders.index')"
                                :class="[
                                    'flex items-center justify-between rounded-xl px-3 py-2.5 font-medium transition',
                                    route().current('admin.orders.*')
                                        ? 'bg-gray-900 text-white shadow-sm'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                <span class="flex items-center gap-2">
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-indigo-50 text-xs text-indigo-600"
                                    >
                                        O
                                    </span>
                                    <span>Orders</span>
                                </span>
                            </Link>

                            <Link
                                :href="route('admin.products.index')"
                                :class="[
                                    'flex items-center justify-between rounded-xl px-3 py-2.5 font-medium transition',
                                    route().current('admin.products.*')
                                        ? 'bg-gray-900 text-white shadow-sm'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                <span class="flex items-center gap-2">
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-emerald-50 text-xs text-emerald-600"
                                    >
                                        P
                                    </span>
                                    <span>Products</span>
                                </span>
                            </Link>

                            <Link
                                :href="route('admin.categories.index')"
                                :class="[
                                    'flex items-center justify-between rounded-xl px-3 py-2.5 font-medium transition',
                                    route().current('admin.categories.*')
                                        ? 'bg-gray-900 text-white shadow-sm'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                <span class="flex items-center gap-2">
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-amber-50 text-xs text-amber-600"
                                    >
                                        C
                                    </span>
                                    <span>Categories</span>
                                </span>
                            </Link>
                        </nav>
                    </div>
                </aside>

                <div class="flex-1 py-8">
                    <nav class="mb-4 md:hidden">
                        <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
                            <Link
                                :href="route('admin.dashboard')"
                                :class="[
                                    'flex-shrink-0 rounded-full border px-3 py-1.5 text-xs font-medium transition',
                                    route().current('admin.dashboard')
                                        ? 'border-gray-900 bg-gray-900 text-white shadow-sm'
                                        : 'border-gray-200 bg-white/90 text-gray-700 hover:border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                                Dashboard
                            </Link>
                            <Link
                                :href="route('admin.orders.index')"
                                :class="[
                                    'flex-shrink-0 rounded-full border px-3 py-1.5 text-xs font-medium transition',
                                    route().current('admin.orders.*')
                                        ? 'border-gray-900 bg-gray-900 text-white shadow-sm'
                                        : 'border-gray-200 bg-white/90 text-gray-700 hover:border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                                Orders
                            </Link>
                            <Link
                                :href="route('admin.products.index')"
                                :class="[
                                    'flex-shrink-0 rounded-full border px-3 py-1.5 text-xs font-medium transition',
                                    route().current('admin.products.*')
                                        ? 'border-gray-900 bg-gray-900 text-white shadow-sm'
                                        : 'border-gray-200 bg-white/90 text-gray-700 hover:border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                                Products
                            </Link>
                            <Link
                                :href="route('admin.categories.index')"
                                :class="[
                                    'flex-shrink-0 rounded-full border px-3 py-1.5 text-xs font-medium transition',
                                    route().current('admin.categories.*')
                                        ? 'border-gray-900 bg-gray-900 text-white shadow-sm'
                                        : 'border-gray-200 bg-white/90 text-gray-700 hover:border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                                Categories
                            </Link>
                        </div>
                    </nav>

                    <header v-if="$slots.header" class="mb-6">
                        <div class="rounded-2xl bg-white/80 px-5 py-4 shadow-sm ring-1 ring-gray-100">
                            <slot name="header" />
                        </div>
                    </header>

                    <main>
                        <slot />
                    </main>
                </div>
            </div>

            <div v-else>
                <header
                    class="bg-white shadow"
                    v-if="$slots.header"
                >
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
