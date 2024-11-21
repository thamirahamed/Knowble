<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    users: Array,
    activeChat: Object,
    messages: Array,
    newMessage: String,
    currentUserId: Number,
    isTyping: Boolean,
    auth: Object,
});


const users = ref([]);
const activeChat = ref(null);
const messages = ref([]);
const newMessage = ref('');
const currentUserId = ref(null);
const isTyping = ref(false); // Typing indicator state
let refreshInterval = null;
const profilePhoto = ref(null);

const searchQuery = ref(''); // Holds the search input

// Computed property to filter users based on the search query
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.profile.cb_number.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Fetch users list
const fetchUsers = async () => {
    const response = await axios.get('/api/chat/users');
    users.value = response.data;
};
// Start chat with a specific user
const startChat = async (userId) => {
    const response = await axios.post('/api/chat/start', { user_id: userId });
    activeChat.value = response.data.chat;
    messages.value = response.data.messages;
    fetchMessages(); // Fetch messages immediately when starting a chat
    console.log(activeChat);

};


// Fetch messages for the active chat
const fetchMessages = async () => {
    if (activeChat.value) {
        const response = await axios.get(`/api/chat/messages/${activeChat.value.id}`);
        messages.value = response.data.messages;
    }
};

// Send a message
const sendMessage = async () => {
    if (!newMessage.value) return;

    await axios.post('/api/chat/message', {
        chat_id: activeChat.value.id,
        message: newMessage.value,
    });

    // Clear the message input field after sending the message

};

// Handle typing indicator
// const handleTyping = () => {
//     if (activeChat.value) {
//         window.Echo.channel('chat').whisper('UserTyping', {
//             chat_id: activeChat.value.id,
//             is_typing: newMessage.value.length > 0, // Send typing state based on input
//         });
//     }
// };
fetchUsers();
// Setup Echo and event listeners for real-time updates
onMounted(() => {
    fetchUsers();

    // Ensure pageProps and auth are available
    if (props.auth && props.auth.id) {
        currentUserId.value = props.auth.id;
    } else {
        console.error("Auth data is missing or malformed in props:", props.auth);
    }

    // Initialize Echo for real-time features
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '8b3c3cd9313f2c8cdd03', // Replace with your Pusher key or other broadcaster configuration
        cluster: 'ap2', // Replace with the cluster if you're using Pusher
        encrypted: true,
    });

    // Start the interval to refresh messages every 3 seconds
    refreshInterval = setInterval(fetchMessages, 1000);

    // // Listen for new messages
    // Echo.channel('chat')
    //     .listen('MessageSent', (event) => {
    //         if (activeChat.value && event.chat_id === activeChat.value.id) {
    //             messages.value.push(event.message);
    //         }
    //     })
    //     .listen('UserTyping', (event) => {
    //         if (event.chat_id === activeChat.value.id) {
    //             isTyping.value = event.is_typing; // Update typing state
    //         }
    //     })
    //     .listen('UserPresence', (event) => {
    //         const user = users.value.find((user) => user.id === event.user_id);
    //         if (user) {
    //             user.is_online = event.is_online;
    //         }
    //     });
});



// Cleanup interval on component unmount
onBeforeUnmount(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<template>
    <Head title="Chat" />
    <AuthenticatedLayout>
        <div class="flex h-screen">
            <!-- Users Sidebar -->
            <div class="w-1/4 bg-gray-100 border-r border-gray-300 overflow-y-auto">
                <h2 class="p-4 font-bold text-lg">Users</h2>

                <!-- Search Bar -->
                <div class="px-4 mb-4">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search users..."
                        class="w-full border p-2 rounded-lg"
                    />
                </div>

                <!-- Filtered Users List -->
                <ul class="space-y-4 px-4">
                    <li
                        v-for="user in filteredUsers"
                        :key="user.id"
                        @click="startChat(user.id)"
                        class="cursor-pointer flex items-center p-2 rounded hover:bg-gray-200 transition"
                    >
                        <img
                            class="h-10 w-10 rounded-full object-cover mr-3"
                            :src="user.profile.profile_pic"
                            :alt="user.name"
                        />
                        <div>
                            <p class="font-medium">{{ user.name }}</p>
                            <span class="text-gray-500 text-sm">{{user.profile.cb_number}}</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 bg-white">
                <div class="p-6">
                    <div v-if="activeChat">
                        <h1 class="text-xl font-bold mb-4">
                            Chat with {{ activeChat.name }}
                        </h1>

                        <!-- Messages Section -->
                        <div class="flex flex-col space-y-2 bg-gray-300 p-4 rounded-lg mb-4 h-[60vh] overflow-y-auto">
                            <div
                                v-for="message in messages"
                                :key="message.id"
                                :class="{
                                    'self-end text-right': message.user_id === currentUserId,
                                }"
                                class="flex"
                            >
                                <div
                                    class="bg-blue-200 text-black p-2 rounded-lg max-w-xs break-words"
                                >
                                    {{ message.message }}
                                </div>
                            </div>
                        </div>

                        <!-- Typing Indicator -->
                        <div v-if="isTyping" class="text-gray-500 italic mb-4">
                            {{ activeChat.name }} is typing...
                        </div>

                        <!-- Message Input -->
                        <div class="flex space-x-4">
                            <input
                                v-model="newMessage"
                                type="text"
                                placeholder="Type your message here..."
                                class="border p-2 rounded-lg flex-1"
                            />
                            <button
                                @click="sendMessage"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg"
                            >
                                Send
                            </button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-gray-500 italic">Select a user to start chatting.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
