<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Placeholder from '@tiptap/extension-placeholder'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Write something amazing...',
    },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            link: {
                openOnClick: false,
            },
        }),
        Image,
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
    ],
    content: props.modelValue,
    editorProps: {
        attributes: {
            class: 'prose prose-sm sm:prose-base max-w-none focus:outline-none min-h-[300px] p-4',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML())
    },
})

watch(() => props.modelValue, (newValue) => {
    // Only update if the content is different to avoid cursor jumping
    const isSame = editor.value?.getHTML() === newValue
    if (!isSame && editor.value) {
        editor.value.commands.setContent(newValue, false)
    }
})

onBeforeUnmount(() => {
    editor.value?.destroy()
})

const setLink = () => {
    const previousUrl = editor.value.getAttributes('link').href
    const url = window.prompt('URL', previousUrl)

    // cancelled
    if (url === null) {
        return
    }

    // empty
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
        return
    }

    // update link
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

const addImage = () => {
    const url = window.prompt('Image URL')

    if (url) {
        editor.value.chain().focus().setImage({ src: url }).run()
    }
}
</script>

<template>
    <div class="border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white">
        <div v-if="editor" class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 p-2">
            <!-- Headings -->
            <button 
                @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('heading', { level: 2 }), 'text-gray-600 hover:bg-gray-200': !editor.isActive('heading', { level: 2 }) }"
                class="p-1.5 rounded text-sm font-semibold"
                title="Heading 2"
                type="button"
            >
                H2
            </button>
            <button 
                @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('heading', { level: 3 }), 'text-gray-600 hover:bg-gray-200': !editor.isActive('heading', { level: 3 }) }"
                class="p-1.5 rounded text-sm font-semibold"
                title="Heading 3"
                type="button"
            >
                H3
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Basic Formatting -->
            <button 
                @click="editor.chain().focus().toggleBold().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('bold'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('bold') }"
                class="p-1.5 rounded"
                title="Bold"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleItalic().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('italic'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('italic') }"
                class="p-1.5 rounded"
                title="Italic"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="4" x2="10" y2="22"></line><path d="M15 4L6 22"></path></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleUnderline().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('underline'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('underline') }"
                class="p-1.5 rounded"
                title="Underline"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path><line x1="4" y1="21" x2="20" y2="21"></line></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleStrike().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('strike'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('strike') }"
                class="p-1.5 rounded"
                title="Strike"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.3 19c-1.4 1.2-3.2 2-5.3 2-4.4 0-8-3.6-8-8 0-4.4 3.6-8 8-8 2.1 0 3.9.8 5.3 2"></path><line x1="4" y1="12" x2="20" y2="12"></line></svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Lists -->
            <button 
                @click="editor.chain().focus().toggleBulletList().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('bulletList'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('bulletList') }"
                class="p-1.5 rounded"
                title="Bullet List"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleOrderedList().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('orderedList'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('orderedList') }"
                class="p-1.5 rounded"
                title="Ordered List"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4"></path><path d="M4 10h2"></path><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleBlockquote().run()" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('blockquote'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('blockquote') }"
                class="p-1.5 rounded"
                title="Blockquote"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path></svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Media -->
            <button 
                @click="setLink" 
                :class="{ 'bg-gray-200 text-gray-900': editor.isActive('link'), 'text-gray-600 hover:bg-gray-200': !editor.isActive('link') }"
                class="p-1.5 rounded"
                title="Link"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </button>
            <button 
                @click="addImage" 
                class="p-1.5 rounded text-gray-600 hover:bg-gray-200"
                title="Image"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            </button>

            <div class="flex-1"></div>

            <!-- History -->
            <button 
                @click="editor.chain().focus().undo().run()" 
                :disabled="!editor.can().chain().focus().undo().run()"
                class="p-1.5 rounded text-gray-600 hover:bg-gray-200 disabled:opacity-50"
                title="Undo"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"></path><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path></svg>
            </button>
            <button 
                @click="editor.chain().focus().redo().run()" 
                :disabled="!editor.can().chain().focus().redo().run()"
                class="p-1.5 rounded text-gray-600 hover:bg-gray-200 disabled:opacity-50"
                title="Redo"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"></path><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"></path></svg>
            </button>
        </div>
        <editor-content :editor="editor" />
    </div>
</template>

<style>
.ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #adb5bd;
  pointer-events: none;
  height: 0;
}
</style>