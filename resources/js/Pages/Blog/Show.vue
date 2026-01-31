<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed, nextTick } from 'vue';
import { 
    Facebook, 
    Twitter, 
    Linkedin, 
    ShoppingCart, 
    Heart,
    ChevronLeft,
    ChevronRight,
    MessageCircle,
    CornerDownRight,
    Trash2
} from 'lucide-vue-next';

const props = defineProps({
    post: Object,
    related_posts: Array,
    shop_the_look: Array,
    comments: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const form = useForm({
    content: '',
    parent_id: null
});

const replyForm = useForm({
    content: '',
    parent_id: null
});

const activeReplyId = ref(null);

const submitComment = () => {
    form.post(route('comments.store', { type: 'blog_post', id: props.post.id }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    });
};

const submitReply = () => {
    replyForm.post(route('comments.store', { type: 'blog_post', id: props.post.id }), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset();
            activeReplyId.value = null;
        }
    });
};

const openReply = (comment, rootCommentId = null) => {
    activeReplyId.value = comment.id;
    // If it's a nested reply (rootCommentId exists), we reply to the root, but tag the user
    replyForm.parent_id = rootCommentId || comment.id;
    replyForm.content = rootCommentId ? `@${comment.user.name} ` : '';
    
    nextTick(() => {
        const el = document.getElementById(`reply-input-${comment.id}`);
        if (el) el.focus();
    });
};

const cancelReply = () => {
    activeReplyId.value = null;
    replyForm.reset();
};

const deleteComment = (comment) => {
    if (confirm('Are you sure you want to delete this comment?')) {
        router.delete(route('comments.destroy', comment.id), {
            preserveScroll: true,
        });
    }
};

const addToCart = (product) => {
    router.post(route('cart.store', product.id), {
        quantity: 1
    }, {
        preserveScroll: true
    });
};

const toggleFollow = () => {
    if (!user.value) {
        router.visit(route('login'));
        return;
    }

    if (props.post.author.is_following) {
        if (confirm(`Are you sure you want to unfollow ${props.post.author.name}?`)) {
            router.delete(route('users.unfollow', props.post.author.id), {
                preserveScroll: true,
            });
        }
    } else {
        router.post(route('users.follow', props.post.author.id), {}, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <ShopLayout>
        <Head :title="post.title" />

        <div class="bg-white min-h-screen pb-20">
            <!-- Breadcrumb -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 text-sm text-gray-500">
                <Link :href="route('home')" class="hover:text-blue-600">Home</Link>
                <span class="mx-2">></span>
                <Link :href="route('blog.index')" class="hover:text-blue-600">Blog</Link>
                <span class="mx-2">></span>
                <span class="text-gray-900 truncate">{{ post.title }}</span>
            </div>

            <!-- Hero Section -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-12">
                <div class="relative rounded-2xl overflow-hidden h-[400px] md:h-[500px]">
                    <img :src="post.image" :alt="post.title" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 md:p-12 text-white max-w-3xl">
                        <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block">
                            {{ post.category }}
                        </span>
                        <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">
                            {{ post.title }}
                        </h1>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    
                    <!-- Left Sidebar (Social Share) -->
                    <div class="hidden lg:block lg:col-span-1">
                        <div class="sticky top-24 flex flex-col gap-4 items-center">
                            <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition">
                                <Facebook class="w-5 h-5" />
                            </button>
                            <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center hover:bg-blue-400 hover:text-white transition">
                                <Twitter class="w-5 h-5" />
                            </button>
                            <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center hover:bg-blue-700 hover:text-white transition">
                                <Linkedin class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="lg:col-span-8">
                        <!-- Author Header -->
                        <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-8">
                            <div class="flex items-center gap-4">
                                <div v-if="post.author.avatar" class="w-12 h-12 rounded-full overflow-hidden">
                                    <img :src="post.author.avatar" :alt="post.author.name" class="w-full h-full object-cover">
                                </div>
                                <div v-else class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                    {{ post.author.name.charAt(0) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ post.author.name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ post.author.role }} • Published {{ post.published_at }} • {{ post.read_time }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button 
                                    v-if="!user || (user.id !== post.author.id)"
                                    @click="toggleFollow"
                                    :class="[
                                        'px-4 py-2 text-sm font-semibold rounded-lg transition',
                                        post.author.is_following 
                                            ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' 
                                            : 'bg-blue-600 text-white hover:bg-blue-700'
                                    ]"
                                >
                                    {{ post.author.is_following ? 'Following' : 'Follow' }}
                                </button>
                                <button class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Blog Content -->
                        <div class="prose prose-lg prose-blue max-w-none text-gray-600" v-html="post.content"></div>

                        <!-- Shop The Look Section -->
                        <div class="mt-16 mb-16">
                            <div class="flex items-center justify-between mb-8">
                                <h2 class="text-2xl font-bold text-gray-900">Shop the Look</h2>
                                <div class="flex gap-2">
                                    <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                        <ChevronLeft class="w-4 h-4" />
                                    </button>
                                    <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                        <ChevronRight class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div v-for="product in shop_the_look" :key="product.id" class="group">
                                    <Link :href="route('shop.show', product.slug)" class="block relative bg-gray-100 rounded-xl overflow-hidden aspect-[4/5] mb-4">
                                        <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        <span class="absolute top-3 left-3 bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded">
                                            {{ product.category }}
                                        </span>
                                    </Link>
                                    <Link :href="route('shop.show', product.slug)">
                                        <h3 class="font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition">{{ product.name }}</h3>
                                    </Link>
                                    <p class="text-sm text-gray-500 mb-3">{{ product.category }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900">${{ product.price.toFixed(2) }}</span>
                                        <button @click="addToCart(product)" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition">
                                            <ShoppingCart class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keep Reading Section (Full Width) -->
                <div class="border-t border-gray-100 pt-16 mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Keep Reading</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <Link v-for="related in related_posts" :key="related.id" :href="route('blog.show', related.slug)" class="group cursor-pointer">
                            <div class="rounded-xl overflow-hidden h-48 mb-4">
                                <img :src="related.image" :alt="related.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <span class="text-blue-600 text-xs font-bold uppercase tracking-wider mb-2 block">{{ related.category }}</span>
                            <h3 class="font-bold text-gray-900 text-lg group-hover:text-blue-600 transition">{{ related.title }}</h3>
                        </Link>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="mt-16 max-w-4xl mx-auto" id="comments">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Comments ({{ comments.length }})</h2>
                    
                    <!-- Comment Form -->
                    <div v-if="user" class="bg-gray-50 rounded-xl p-6 mb-8 flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                             <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
                             <div v-else class="w-full h-full flex items-center justify-center text-gray-500 font-bold bg-gray-300">
                                {{ user.name.charAt(0) }}
                             </div>
                        </div>
                        <div class="flex-1">
                            <textarea 
                                v-model="form.content" 
                                placeholder="Share your thoughts..." 
                                class="w-full border-0 bg-transparent resize-none focus:ring-0 text-gray-900 placeholder-gray-500 min-h-[50px]"
                            ></textarea>
                            <div class="flex justify-end items-center mt-2">
                                <button 
                                    @click="submitComment" 
                                    :disabled="form.processing"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Posting...' : 'Post Comment' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="bg-gray-50 rounded-xl p-6 mb-8 text-center">
                        <p class="text-gray-600">Please <Link :href="route('login')" class="text-blue-600 hover:underline">log in</Link> to leave a comment.</p>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-8">
                        <div v-for="comment in comments" :key="comment.id" class="flex gap-4">
                            <!-- Avatar (Hide if deleted) -->
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                                <template v-if="!comment.deleted_at">
                                    <img v-if="comment.user.avatar" :src="comment.user.avatar" :alt="comment.user.name" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-500 font-bold bg-gray-300">
                                        {{ comment.user.name.charAt(0) }}
                                    </div>
                                </template>
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <div v-if="!comment.deleted_at" class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-900">{{ comment.user.name }}</h4>
                                        <span class="text-xs text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                    </div>
                                    
                                    <p v-if="!comment.deleted_at" class="text-gray-600 text-sm">{{ comment.content }}</p>
                                    <p v-else class="text-gray-400 text-sm italic">This comment has been deleted.</p>
                                </div>
                                
                                <!-- Actions (Reply/Delete) - Only if NOT deleted -->
                                <div v-if="!comment.deleted_at" class="mt-2 flex items-center gap-4 text-xs">
                                    <button 
                                        v-if="user" 
                                        @click="openReply(comment)" 
                                        class="text-gray-500 hover:text-blue-600 font-medium flex items-center gap-1"
                                    >
                                        <CornerDownRight class="w-3 h-3" /> Reply
                                    </button>
                                    <button 
                                        v-if="user && (user.id === comment.user.id || user.role === 'admin' || user.id === post.author.id)" 
                                        @click="deleteComment(comment)" 
                                        class="text-gray-400 hover:text-red-600 font-medium flex items-center gap-1 ml-2"
                                        title="Delete Comment"
                                    >
                                        <Trash2 class="w-3 h-3" />
                                    </button>
                                </div>

                                <!-- Inline Reply Form -->
                                <div v-if="activeReplyId === comment.id" class="mt-4">
                                    <div class="flex gap-4">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                                            <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
                                            <div v-else class="w-full h-full flex items-center justify-center text-gray-500 font-bold bg-gray-300">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <textarea 
                                                :id="`reply-input-${comment.id}`"
                                                v-model="replyForm.content" 
                                                placeholder="Write a reply..." 
                                                class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm min-h-[80px]"
                                            ></textarea>
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button @click="cancelReply" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                                <button 
                                                    @click="submitReply" 
                                                    :disabled="replyForm.processing"
                                                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                                >
                                                    Reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Replies -->
                                        <div v-if="comment.replies && comment.replies.length" class="mt-4 space-y-4 pl-4 border-l-2 border-gray-100">
                                            <div v-for="reply in comment.replies" :key="reply.id" class="flex gap-4">
                                                <!-- Reply Avatar -->
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                                                    <template v-if="!reply.deleted_at">
                                                        <img v-if="reply.user.avatar" :src="reply.user.avatar" :alt="reply.user.name" class="w-full h-full object-cover">
                                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-500 font-bold bg-gray-300">
                                                            {{ reply.user.name.charAt(0) }}
                                                        </div>
                                                    </template>
                                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                    </div>
                                                </div>

                                                <div class="flex-1">
                                                    <div class="bg-gray-50 rounded-xl p-3">
                                                        <div v-if="!reply.deleted_at" class="flex items-center justify-between mb-1">
                                                            <h4 class="font-bold text-gray-900 text-sm">{{ reply.user.name }}</h4>
                                                            <span class="text-xs text-gray-500">{{ new Date(reply.created_at).toLocaleDateString() }}</span>
                                                        </div>
                                                        <p v-if="!reply.deleted_at" class="text-gray-600 text-sm">{{ reply.content }}</p>
                                                        <p v-else class="text-gray-400 text-sm italic">This comment has been deleted.</p>
                                                    </div>

                                                    <!-- Reply Actions -->
                                                    <div v-if="!reply.deleted_at" class="mt-2 flex items-center gap-4 text-xs">
                                                        <button 
                                                            v-if="user" 
                                                            @click="openReply(reply, comment.id)" 
                                                            class="text-gray-500 hover:text-blue-600 font-medium flex items-center gap-1"
                                                        >
                                                            <CornerDownRight class="w-3 h-3" /> Reply
                                                        </button>
                                                        <button 
                                                            v-if="user && (user.id === reply.user.id || user.role === 'admin' || user.id === post.author.id)" 
                                                            @click="deleteComment(reply)" 
                                                            class="text-gray-400 hover:text-red-600 font-medium flex items-center gap-1 ml-2"
                                                            title="Delete Comment"
                                                        >
                                                            <Trash2 class="w-3 h-3" />
                                                        </button>
                                                    </div>

                                                    <!-- Inline Reply Form for Sub-comments -->
                                            <div v-if="activeReplyId === reply.id" class="mt-4">
                                                <div class="flex gap-4">
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                                                        <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
                                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-500 font-bold bg-gray-300">
                                                            {{ user.name.charAt(0) }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <textarea 
                                                            :id="`reply-input-${reply.id}`"
                                                            v-model="replyForm.content" 
                                                            placeholder="Write a reply..." 
                                                            class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm min-h-[80px]"
                                                        ></textarea>
                                                        <div class="flex justify-end gap-2 mt-2">
                                                            <button @click="cancelReply" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                                            <button 
                                                                @click="submitReply" 
                                                                :disabled="replyForm.processing"
                                                                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                                            >
                                                                Reply
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="comments && comments.length === 0" class="text-center text-gray-500 py-8">
                            No comments yet. Be the first to share your thoughts!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
