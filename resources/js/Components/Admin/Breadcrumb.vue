<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
        // Format: [{ label: 'Home', href: route('home') }, { label: 'Current Page' }]
    }
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-2">
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center">
                    <svg v-if="index > 0" class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <Link 
                        v-if="item.href" 
                        :href="item.href" 
                        class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700"
                        :class="{ 'ml-0': index === 0 }"
                    >
                        {{ item.label }}
                    </Link>
                    <span 
                        v-else 
                        class="ml-2 text-sm font-medium text-gray-500"
                        :class="{ 'ml-0': index === 0 }"
                    >
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>
