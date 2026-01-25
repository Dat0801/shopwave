<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    options: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: [String, Number],
        default: null,
    },
    excludeId: {
        type: [String, Number],
        default: null,
    },
    name: {
        type: String,
        default: 'category_parent',
    },
    depth: {
        type: Number,
        default: 0,
    }
});

const emit = defineEmits(['update:modelValue']);

// Transform flat list to tree if necessary
const treeData = computed(() => {
    if (props.depth === 0) {
        // Check if data is flat (has parent_id and no children structure)
        const isFlat = props.options.some(item => !item.children && item.parent_id !== undefined);
        if (isFlat) {
            return buildTree(props.options);
        }
    }
    return props.options;
});

const buildTree = (items, parentId = null) => {
    const tree = [];
    items.forEach(item => {
        // loose comparison for parent_id (null vs "" vs 0)
        // Adjust logic: if parentId is null, match null or empty string or 0 (if root is 0)
        // Usually database stores null for root.
        
        const itemParentId = item.parent_id;
        const isRoot = parentId === null ? (itemParentId === null || itemParentId === '') : (itemParentId == parentId);

        if (isRoot) {
            const children = buildTree(items, item.id);
            tree.push({
                ...item,
                children: children.length ? children : undefined
            });
        }
    });
    return tree;
};

const expandedIds = ref(new Set());

const toggleExpand = (id) => {
    const newSet = new Set(expandedIds.value);
    if (newSet.has(id)) {
        newSet.delete(id);
    } else {
        newSet.add(id);
    }
    expandedIds.value = newSet;
};

const updateValue = (value) => {
    emit('update:modelValue', value);
};

// Watch for modelValue changes to auto-expand parents? 
// For now, simple manual expansion.

</script>

<template>
    <ul class="space-y-1">
        <!-- Root Option (Only at depth 0) -->
        <li v-if="depth === 0">
            <div class="flex items-center gap-2 group p-1">
                 <div class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded w-full transition-colors">
                    <!-- Spacer for alignment -->
                    <span class="w-5 h-5 flex items-center justify-center">
                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                    </span>
                    
                    <!-- Folder Icon for Root -->
                     <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    
                    <label class="flex items-center gap-2 cursor-pointer select-none flex-1">
                        <input 
                            type="radio" 
                            :name="name" 
                            :value="''" 
                            :checked="!modelValue"
                            @change="updateValue(null)"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        >
                        <span class="text-sm font-medium text-gray-700">Root</span>
                    </label>
                 </div>
            </div>
        </li>

        <li v-for="category in treeData" :key="category.id">
            <template v-if="category.id !== excludeId">
                <div class="flex flex-col">
                    <div class="flex items-center gap-2 group p-1">
                        <!-- Chevron -->
                        <button 
                            @click.prevent="toggleExpand(category.id)"
                            class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                            :class="{ 'invisible': !category.children || !category.children.length }"
                        >
                            <svg 
                                class="w-3 h-3 transition-transform duration-200"
                                :class="{ 'rotate-90': expandedIds.has(category.id) }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <div class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded flex-1 transition-colors">
                            <!-- Icon -->
                            <svg v-if="category.children && category.children.length" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <svg v-else class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>

                            <label class="flex items-center gap-2 cursor-pointer select-none flex-1">
                                <input 
                                    type="radio" 
                                    :name="name" 
                                    :value="category.id" 
                                    :checked="modelValue === category.id"
                                    @change="updateValue(category.id)"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                >
                                <span class="text-sm text-gray-700">{{ category.name }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Children -->
                    <div 
                        v-if="category.children && category.children.length && expandedIds.has(category.id)" 
                        class="ml-6 pl-1"
                    >
                         <CategoryTreeSelect 
                            :options="category.children"
                            :modelValue="modelValue"
                            :excludeId="excludeId"
                            :depth="depth + 1"
                            :name="name"
                            @update:modelValue="updateValue"
                        />
                    </div>
                </div>
            </template>
        </li>
    </ul>
</template>
