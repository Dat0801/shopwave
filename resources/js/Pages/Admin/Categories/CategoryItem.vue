<script setup>
import { computed } from 'vue';
import draggable from 'vuedraggable';
import { getImageUrl } from '@/Utils/image';

const props = defineProps({
    category: Object,
});

const emit = defineEmits(['edit', 'delete', 'toggle-status', 'change']);

const categoryChildren = computed({
    get: () => props.category.children || [],
    set: (value) => {
        props.category.children = value;
    }
});

const onDragChange = () => {
   emit('change');
};
</script>

<template>
    <div class="border-b border-gray-100 bg-white last:border-b-0">
        <!-- Row Content -->
        <div class="flex items-center py-3 hover:bg-gray-50 transition-colors group">
            <!-- Drag Handle & Name -->
            <div class="flex-1 px-4 flex items-center gap-3 overflow-hidden">
                 <div class="cursor-move text-gray-300 group-hover:text-gray-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                    </svg>
                </div>
                <div class="flex items-center gap-3 overflow-hidden">
                    <img 
                        :src="getImageUrl(category.image_path, 40, 40)" 
                        class="h-10 w-10 flex-shrink-0 rounded-lg object-cover ring-1 ring-gray-200"
                        alt=""
                    />
                    <div class="min-w-0">
                        <div class="font-medium text-gray-900 truncate">{{ category.name }}</div>
                        <div class="text-xs text-gray-500 truncate" v-if="category.slug">/{{ category.slug }}</div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="flex-1 px-4 text-sm text-gray-500 hidden md:block truncate">
                {{ category.description || '-' }}
            </div>

            <!-- Product Count -->
            <div class="w-24 px-4 text-sm text-gray-500 text-center">
                {{ category.products_count }}
            </div>

             <!-- Status -->
            <div class="w-24 px-4 text-center">
                <button 
                    @click="$emit('toggle-status', category)"
                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset transition-colors"
                    :class="category.status ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-red-50 text-red-700 ring-red-600/20'"
                >
                    {{ category.status ? 'Active' : 'Inactive' }}
                </button>
            </div>

            <!-- Actions -->
            <div class="w-24 px-4 flex justify-end gap-1">
                 <button 
                    @click="$emit('edit', category)"
                    class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                    title="Edit"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>
                <button 
                    @click="$emit('delete', category)"
                    class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                    title="Delete"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Nested Children -->
        <div class="pl-6 md:pl-8 ml-6 md:ml-6 border-l-2 border-gray-100">
            <draggable
                v-model="categoryChildren"
                item-key="id"
                group="categories"
                @change="onDragChange"
                ghost-class="bg-blue-50"
                :class="categoryChildren.length ? 'space-y-0' : 'min-h-[40px] p-2'"
            >
                <template #item="{ element }">
                    <CategoryItem 
                        :category="element"
                        @edit="$emit('edit', $event)"
                        @delete="$emit('delete', $event)"
                        @toggle-status="$emit('toggle-status', $event)"
                        @change="$emit('change')"
                    />
                </template>
            </draggable>
        </div>
    </div>
</template>
