<script setup>
const modelValue = defineModel({
    type: [String, Number],
    default: '',
});

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    },
});
</script>

<template>
    <input
        v-model="modelValue"
        :type="props.type"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
    />
</template>

