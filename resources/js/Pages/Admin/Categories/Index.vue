<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const filterForm = useForm({
    search: props.filters?.search || '',
});

const createForm = useForm({
    name: '',
    slug: '',
    status: true,
});

const updateForm = useForm({
    id: null,
    name: '',
    slug: '',
    status: true,
});

const submitFilters = () => {
    filterForm.get(route('admin.categories.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['categories', 'filters'],
    });
};

const submitCreate = () => {
    createForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const startEdit = (category) => {
    updateForm.id = category.id;
    updateForm.name = category.name;
    updateForm.slug = category.slug;
    updateForm.status = category.status;
};

const submitUpdate = () => {
    if (!updateForm.id) return;

    updateForm.put(route('admin.categories.update', updateForm.id), {
        preserveScroll: true,
    });
};

const destroyCategory = (category) => {
    if (!confirm('Delete this category?')) return;

    useForm({})
        .delete(route('admin.categories.destroy', category.id), {
            preserveScroll: true,
        });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">
                    Categories
                </h1>
                <form
                    class="flex items-center gap-2"
                    @submit.prevent="submitFilters"
                >
                    <div class="w-48">
                        <Input
                            v-model="filterForm.search"
                            type="search"
                            placeholder="Search"
                        />
                    </div>
                    <Button type="submit">
                        Filter
                    </Button>
                </form>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-800">
                        Create category
                    </h2>

                    <form
                        class="grid gap-4 md:grid-cols-4"
                        @submit.prevent="submitCreate"
                    >
                        <div>
                            <Input
                                v-model="createForm.name"
                                placeholder="Name"
                            />
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.name"
                            />
                        </div>
                        <div>
                            <Input
                                v-model="createForm.slug"
                                placeholder="Slug"
                            />
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.slug"
                            />
                        </div>
                        <div>
                            <select
                                v-model="createForm.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option :value="true">
                                    Active
                                </option>
                                <option :value="false">
                                    Inactive
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="createForm.errors.status"
                            />
                        </div>
                        <div class="flex items-center justify-end">
                            <Button
                                type="submit"
                                :disabled="createForm.processing"
                            >
                                Save
                            </Button>
                        </div>
                    </form>
                </section>

                <section class="rounded-lg bg-white p-4 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-gray-800">
                        Existing categories
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b text-xs uppercase tracking-wide text-gray-500">
                                    <th class="px-3 py-2">
                                        Name
                                    </th>
                                    <th class="px-3 py-2">
                                        Slug
                                    </th>
                                    <th class="px-3 py-2">
                                        Status
                                    </th>
                                    <th class="px-3 py-2 text-right">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="category in categories.data"
                                    :key="category.id"
                                    class="border-b last:border-b-0"
                                >
                                    <td class="px-3 py-2">
                                        {{ category.name }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ category.slug }}
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <span
                                            v-if="category.status"
                                            class="rounded-full bg-green-100 px-2 py-0.5 text-green-700"
                                        >
                                            Active
                                        </span>
                                        <span
                                            v-else
                                            class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-700"
                                        >
                                            Inactive
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right text-xs">
                                        <button
                                            type="button"
                                            class="mr-2 text-indigo-600 hover:text-indigo-800"
                                            @click="startEdit(category)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="text-red-600 hover:text-red-700"
                                            @click="destroyCategory(category)"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section
                    v-if="updateForm.id"
                    class="rounded-lg bg-white p-4 shadow-sm"
                >
                    <h2 class="mb-4 text-sm font-semibold text-gray-800">
                        Edit category
                    </h2>

                    <form
                        class="grid gap-4 md:grid-cols-4"
                        @submit.prevent="submitUpdate"
                    >
                        <div>
                            <Input
                                v-model="updateForm.name"
                                placeholder="Name"
                            />
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.name"
                            />
                        </div>
                        <div>
                            <Input
                                v-model="updateForm.slug"
                                placeholder="Slug"
                            />
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.slug"
                            />
                        </div>
                        <div>
                            <select
                                v-model="updateForm.status"
                                class="block w-full rounded-md border-gray-300 bg-white py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option :value="true">
                                    Active
                                </option>
                                <option :value="false">
                                    Inactive
                                </option>
                            </select>
                            <InputError
                                class="mt-1"
                                :message="updateForm.errors.status"
                            />
                        </div>
                        <div class="flex items-center justify-end">
                            <Button
                                type="submit"
                                :disabled="updateForm.processing"
                            >
                                Update
                            </Button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

