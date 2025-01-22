<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import Echo from "laravel-echo";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowUpRightIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    auth: Object,
});

// Reactive Variables
const users = ref([]);
const activeChat = ref(null);
const messages = ref([]);
const newMessage = ref("");
const currentUserId = ref(null);
const isTyping = ref(false);
const chatWithCurrentUsers = ref([]);
const searchQuery = ref("");

// Computed Property to Filter Users
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    return users.value.filter((user) =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Fetch Users List
const fetchUsers = async () => {
    try {
        const response = await axios.get("/api/chat/users");
        users.value = response.data;
        console.log(users.value);
    } catch (error) {
        console.error("Error fetching users:", error);
    }
};

// Fetch Current Chat Users
const fetchCurrentChatUser = async () => {
    try {
        const response = await axios.get("/api/chat/chatwithcurrentuser");
        chatWithCurrentUsers.value = response.data;
        console.log(chatWithCurrentUsers.value);
    } catch (error) {
        console.error("Error fetching current chat users:", error);
    }
};

// Start Chat with a Specific User
const startChat = async (userId) => {
    try {
        const response = await axios.post("/api/chat/start", { user_id: userId });
        activeChat.value = response.data.chat;
        messages.value = response.data.messages;
        fetchMessages(); // Fetch messages immediately after starting a chat
    } catch (error) {
        console.error("Error starting chat:", error);
    }
};

// Fetch Messages for the Active Chat
const fetchMessages = async () => {
    if (activeChat.value) {
        try {
            const response = await axios.get(
                `/api/chat/messages/${activeChat.value.id}`
            );
            messages.value = response.data.messages;
        } catch (error) {
            console.error("Error fetching messages:", error);
        }
    }
};

// Send a Message
const sendMessage = async () => {
    if (!newMessage.value) return;

    try {
        await axios.post("/api/chat/message", {
            chat_id: activeChat.value.id,
            message: newMessage.value,
        });
        newMessage.value = ""; // Clear the input after sending the message
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};


// Lifecycle Hooks
onMounted(() => {
    fetchUsers();
    fetchCurrentChatUser();

    // Assign the authenticated user's ID
    if (props.auth && props.auth.id) {
        currentUserId.value = props.auth.id;
    }

    // Initialize Echo for real-time updates
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: "8b3c3cd9313f2c8cdd03",
        cluster: "ap2",
        encrypted: true,
    });

    // Refresh messages periodically
    setInterval(fetchMessages, 3000);
});

onBeforeUnmount(() => {
    if (window.Echo) {
        window.Echo.disconnect();
    }
});
const activeChatUser = computed(() => {
    if (!activeChat.value || !users.value.length) return null;

    const otherUserId =
        activeChat.value.user_id_1 === currentUserId.value
            ? activeChat.value.user_id_2
            : activeChat.value.user_id_1;

    return users.value.find((user) => user.id === otherUserId) || null;
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

                    <!-- Conditionally Render User Lists -->
                    <ul class="px-4 overflow-y-auto">
                        <!-- Show Currently Chatted Users if no search query -->
                        <template v-if="!searchQuery">
                            <li
                                v-for="chatUser in chatWithCurrentUsers"
                                :key="chatUser.id"
                                @click="startChat(chatUser.id)"
                                class="flex cursor-pointer items-center p-2 py-4 transition hover:bg-secondary/15 border-t border-gray-200"
                            >
                                <div class="flex justify-evenly items-center">
                                    <img
                                        :src="chatUser.profile.profile_pic"
                                        alt="User Avatar"
                                        class="w-12 h-12 rounded-full"
                                    />
                                    <p class="font-medium text-lg pl-5">{{ chatUser.name }}</p>
                                    <span class="text-sm text-gray-500">{{ chatUser.last_message }}</span>
                                </div>
                            </li>
                        </template>

                        <!-- Show Filtered Users if there is a search query -->
                        <template v-else>
                            <li
                                v-for="user in filteredUsers"
                                :key="user.id"
                                @click="startChat(user.id)"
                                class="flex cursor-pointer items-center p-2 py-4 transition hover:bg-secondary/15 border-t border-gray-200"
                            >
                                <div class="flex justify-evenly items-center">
                                    <img
                                        :src="user.profile.profile_pic"
                                        alt="User Avatar"
                                        class="w-12 h-12 rounded-full"
                                    />
                                    <p class="font-medium text-lg pl-5">{{ user.name }}</p>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Chat Area -->
                <div class="flex flex-1 h-full border-l border-gray-300 text-lg">
                    <div class="p-6 flex h-full w-full">
                        <div v-if="activeChat" class="flex flex-col w-full gap-4">
                            <div class="flex items-center gap-4 p-4 bg-gray-100 rounded-lg shadow">
                                <img
                                    v-if="activeChatUser"
                                    :src="activeChatUser.profile.profile_pic"
                                    alt="User Avatar"
                                    class="w-12 h-12 rounded-full"
                                />
                                <div>
                                    <p class="font-bold text-xl">
                                        {{ activeChatUser ? activeChatUser.name : "Loading..." }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ activeChatUser ? activeChatUser.status || "Active now" : "" }}
                                    </p>
                                </div>
                            </div>

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
