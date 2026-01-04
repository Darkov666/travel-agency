<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    commentableId: Number,
    commentableType: String,
    comments: {
        type: Array,
        default: () => [],
    }
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const replyingTo = ref(null);

const form = useForm({
    content: '',
    guest_name: '',
    guest_email: '',
    commentable_id: props.commentableId,
    commentable_type: props.commentableType,
    parent_id: null,
});

const submit = () => {
    form.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('content');
            form.parent_id = null;
            if (!user.value) {
                form.reset('guest_name', 'guest_email');
            }
            replyingTo.value = null;
        },
    });
};

const toggleLike = (commentId) => {
    router.post(route('comments.like', commentId), {}, {
        preserveScroll: true,
    });
};

const startReply = (commentId) => {
    replyingTo.value = commentId;
    form.parent_id = commentId;
};

const cancelReply = () => {
    replyingTo.value = null;
    form.reset('content');
    form.parent_id = null;
};

const rootComments = computed(() => {
    return props.comments.filter(c => !c.parent_id);
});

const getReplies = (commentId) => {
    return props.comments.filter(c => c.parent_id === commentId);
};
</script>

<template>
    <div class="space-y-8">
        <h3 class="text-2xl font-serif font-bold text-gray-900">Comentarios ({{ comments.length }})</h3>

        <!-- Main Comment Form -->
        <form @submit.prevent="submit" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <!-- Guest Inputs -->
            <div v-if="!user" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="guest_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input 
                        id="guest_name"
                        v-model="form.guest_name"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-black bg-white"
                        placeholder="Tu nombre"
                        required
                    >
                    <InputError :message="form.errors.guest_name" class="mt-2" />
                </div>
                <div>
                    <label for="guest_email" class="block text-sm font-medium text-gray-700">Email (Opcional)</label>
                    <input 
                        id="guest_email"
                        v-model="form.guest_email"
                        type="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-black bg-white"
                        placeholder="tu@email.com"
                    >
                    <InputError :message="form.errors.guest_email" class="mt-2" />
                </div>
            </div>

            <div class="mb-4">
                <label v-if="!user" for="comment" class="block text-sm font-medium text-gray-700 mb-1">Comentario</label>
                <textarea 
                    id="comment" 
                    v-model="form.content" 
                    rows="3"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                    placeholder="Comparte tu opinión..."
                    required
                ></textarea>
                <InputError :message="form.errors.content" class="mt-2" />
            </div>
            <div class="flex justify-end">
                <PrimaryButton :disabled="form.processing" class="!rounded-full hover:shadow-lg transition-transform transform active:scale-95">
                    Publicar Comentario
                </PrimaryButton>
            </div>
        </form>

        <!-- Comments List -->
        <div class="space-y-6">
            <div v-for="comment in rootComments" :key="comment.id" class="animate-fade-in">
                <!-- Parent Comment Wrapper -->
                <div class="flex space-x-4">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border-2 border-primary-200">
                             {{ comment.user ? comment.user.name.charAt(0) : (comment.guest_name ? comment.guest_name.charAt(0) : 'G') }}
                        </div>
                    </div>
                    <!-- Body -->
                    <div class="flex-grow">
                         <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-bold text-gray-900">
                                {{ comment.user ? comment.user.name : (comment.guest_name || 'Invitado') }}
                            </h4>
                            <span class="text-xs text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                         </div>
                         <p class="mt-1 text-sm text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-br-xl rounded-bl-xl rounded-tr-xl">
                             {{ comment.content }}
                         </p>
                         
                         <!-- Actions -->
                         <div class="flex items-center space-x-4 mt-2 ml-1">
                            <button 
                                @click="toggleLike(comment.id)" 
                                class="flex items-center space-x-1 text-xs font-medium transition-colors duration-200" 
                                :class="comment.isLiked ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-primary-600 dark:hover:text-primary-400'"
                            >
                                <svg class="h-4 w-4" :fill="comment.isLiked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span>{{ comment.likes_count || 0 }}</span>
                            </button>
                            <button @click="startReply(comment.id)" class="text-xs text-gray-500 hover:text-primary-600 font-medium transition-colors">
                                Responder
                            </button>
                        </div>
                        
                        <!-- Reply Form -->
                        <div v-if="replyingTo === comment.id" class="mt-4">
                             <form @submit.prevent="submit">
                                <textarea v-model="form.content" rows="2" class="w-full text-sm rounded-md border-gray-300 bg-white text-gray-900" placeholder="Escribe tu respuesta..."></textarea>
                                 <!-- Guest Inputs in Reply DO NOT SHOW (Assuming only simple reply or user logged in? No, guests can reply too. Should show inputs?) -->
                                 <!-- Simplifying: If Guest, show simplified inputs or force login? -->
                                 <!-- For now, assuming logged in or using main logic. Wait, form.guest_name is bound. If guest replies, they need to see inputs again? -->
                                 <!-- Let's just allow quick content reply. If guest check fails, controller will bounce. -->
                                 <!-- Better: Show simple inputs if !user inside reply form too. -->
                                <div v-if="!user" class="grid grid-cols-2 gap-2 mt-2">
                                     <input v-model="form.guest_name" placeholder="Nombre" class="text-xs border-gray-300 rounded" required>
                                     <input v-model="form.guest_email" placeholder="Email" class="text-xs border-gray-300 rounded">
                                </div>

                                <div class="flex justify-end space-x-2 mt-2">
                                    <button type="button" @click="cancelReply" class="text-xs text-gray-500">Cancelar</button>
                                    <PrimaryButton class="text-xs px-3 py-1">Responder</PrimaryButton>
                                </div>
                             </form>
                        </div>

                        <!-- Nested Replies (1 Level Deep visually) -->
                        <div v-if="getReplies(comment.id).length > 0" class="mt-4 space-y-4 ml-8 border-l-2 border-primary-100 pl-4">
                             <div v-for="reply in getReplies(comment.id)" :key="reply.id" class="flex space-x-3">
                                  <div class="flex-shrink-0">
                                      <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold border border-gray-200">
                                         {{ reply.user ? reply.user.name.charAt(0) : (reply.guest_name ? reply.guest_name.charAt(0) : 'G') }}
                                      </div>
                                  </div>
                                  <div class="flex-grow">
                                       <div class="flex items-center justify-between">
                                          <h5 class="text-xs font-bold text-gray-800">
                                              {{ reply.user ? reply.user.name : (reply.guest_name || 'Invitado') }}
                                          </h5>
                                          <span class="text-[10px] text-gray-500">{{ new Date(reply.created_at).toLocaleDateString() }}</span>
                                       </div>
                                       <p class="mt-1 text-xs text-gray-600 bg-white p-2 rounded shadow-sm">
                                           {{ reply.content }}
                                       </p>
                                  </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
             <div v-if="comments.length === 0" class="text-center py-8">
                 <p class="text-gray-500 italic">Sé el primero en comentar.</p>
             </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
