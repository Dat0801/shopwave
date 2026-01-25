<script setup>
defineProps({
    categories: {
        type: Array,
        required: true,
    },
    selectedCategory: {
        type: String,
        default: null,
    },
    level: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['toggle']);
</script>

<template>
    <div class="space-y-3">
        <div v-for="category in categories" :key="category.id">
            <div class="flex items-center" :style="{ paddingLeft: `${level * 1.5}rem` }">
                <input
                    :id="`category-${category.id}`"
                    :value="category.slug"
                    :checked="selectedCategory === category.slug"
                    @change="emit('toggle', category.slug)"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                />
                <label 
                    :for="`category-${category.id}`" 
                    class="ml-3 min-w-0 flex-1 text-gray-500 text-sm hover:text-gray-900 cursor-pointer"
                    :class="{ 'font-medium text-gray-900': selectedCategory === category.slug }"
                >
                    {{ category.name }}
                </label>
            </div>

            <!-- Recursive Children -->
            <CategoryFilter
                v-if="category.children && category.children.length > 0"
                :categories="category.children"
                :selected-category="selectedCategory"
                :level="level + 1"
                @toggle="emit('toggle', $event)"
                class="mt-3"
            />
        </div>
    </div>
</template>
