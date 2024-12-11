<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import Echo from "laravel-echo";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowUpRightIcon, MagnifyingGlassIcon } from "@heroicons/vue/24/solid";

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
const newMessage = ref("");
const currentUserId = ref(null);
const isTyping = ref(false); // Typing indicator state
let refreshInterval = null;
const profilePhoto = ref(null);

const searchQuery = ref(""); // Holds the search input

// Computed property to filter users based on the search query
const filteredUsers = computed(() => {
    // if (!searchQuery.value) return;
    return users.value.filter(
        (user) =>
            user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
            // user.profile.cb_number
            //     .toLowerCase()
            //     .includes(searchQuery.value.toLowerCase()),
    );
});

// Fetch users list
const fetchUsers = async () => {
    const response = await axios.get("/api/chat/users");
    users.value = response.data;
};

// Start chat with a specific user
const startChat = async (userId) => {
    const response = await axios.post("/api/chat/start", { user_id: userId });
    activeChat.value = response.data.chat;
    messages.value = response.data.messages;
    fetchMessages(); // Fetch messages immediately when starting a chat
    console.log(activeChat.value);
};

// Fetch messages for the active chat
const fetchMessages = async () => {
    if (activeChat.value) {
        const response = await axios.get(
            `/api/chat/messages/${activeChat.value.id}`,
        );
        messages.value = response.data.messages;
    }
};

// Send a message
const sendMessage = async () => {
    if (!newMessage.value) return;

    try {
        await axios.post("/api/chat/message", {
            chat_id: activeChat.value.id,
            message: newMessage.value,
        });
    } catch (error) {
        // Clear the message input field after sending the message
        newMessage.value = ""; 
        console.error("Failed to send message:", error);
    }
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
        console.error(
            "Auth data is missing or malformed in props:",
            props.auth,
        );
    }

    // Initialize Echo for real-time features
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: "8b3c3cd9313f2c8cdd03", // Replace with your Pusher key or other broadcaster configuration
        cluster: "ap2", // Replace with the cluster if you're using Pusher
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
        <div class="mt-8 flex h-min flex-1 justify-center">
            <div class="container flex h-[87vh] rounded-lg bg-white shadow">
                <!-- Users Sidebar -->
                <div
                    class="flex flex-col h-full w-1/4 overflow-y-auto"
                >
                    <h1 class="p-4 text-3xl font-bold">Users</h1>

                    <!-- Search Bar -->
                    <div class="mb-4 px-4">
                        <TextInput
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search users..."
                            class=" w-full p-2 !text-base"
                        />
                    </div>

                    <!-- Filtered Users List -->
                    <ul class="px-4 overflow-y-auto">
                        <li
                            v-for="user in filteredUsers"
                            :key="user.id"
                            @click="startChat(user.id)"
                            class="flex cursor-pointer items-center p-2 py-4 transition hover:bg-secondary/15 border-t border-gray-200"
                        >
                            <!-- <img
                                class="mr-3 h-10 w-10 rounded-full object-cover"
                                :src="user.profile.profile_pic"
                                :alt="user.name"
                            /> -->
                            <div>
                                <p class="font-medium text-lg">{{ user.name }}</p>
                                <!-- <span class="text-sm text-gray-500">{{
                                    user.profile.cb_number
                                }}</span> -->
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Chat Area -->
                <div class="flex flex-1 h-full border-l border-gray-300 text-lg">
                    <div class="p-6 flex h-full w-full">
                        <div v-if="activeChat" class="flex flex-col w-full gap-4">
                            <!-- <h1 class=" text-xl font-bold">
                                {{ activeChat.name }}
                            </h1> -->

                            <!-- Messages Section -->
                            <div
                                class="flex shadow flex-col overflow-y-auto rounded-lg bg-primary/10 border p-4 h-full"
                            >
                                <div
                                    v-for="message in messages"
                                    :key="message.id"
                                    :class="{
                                        'self-end bg-accentdark rounded-lg':
                                            message.user_id === currentUserId,
                                        'bg-primary rounded-lg':
                                            message.user_id !== currentUserId,
                                    }"
                                    class="flex w-fit mb-1"
                                >
                                    <div
                                        class="flex rounded-lg py-2 px-3 text-white max-w-96 break-all justify-end whitespace-normal h-full text-left"
                                    >
                                        <p>
                                            {{ message.message }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Typing Indicator -->
                            <div
                                v-if="isTyping"
                                class="mb-4 italic text-gray-500"
                            >
                                {{ activeChat.name }} is typing...
                            </div>

                            <!-- Message Input -->
                            <div class="flex gap-4">
                                <TextInput
                                    v-model="newMessage"
                                    type="text"
                                    placeholder="Type your message here..."
                                    class="flex-1 rounded-lg border p-2"
                                    @keyup.enter="sendMessage"
                                />
                                <PrimaryButton
                                    @click="sendMessage"
                                >
                                    <ArrowUpRightIcon class="w-6 font-bold" />
                                </PrimaryButton>
                            </div>
                        </div>
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <p class="italic text-gray-500 text-xl">
                                Select a user to start chatting
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
