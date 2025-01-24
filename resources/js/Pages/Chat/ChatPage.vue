<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import Echo from "laravel-echo";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PaperAirplaneIcon, CheckBadgeIcon } from "@heroicons/vue/24/solid";

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
const chatWithCurrentUsers = ref([]);
const searchQuery = ref("");

// Computed Property to Filter Users
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    return users.value.filter((user) =>
        user.user.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Fetch Users List
const fetchUsers = async () => {
    try {
        const response = await axios.get("/api/chat/users");
        users.value = response.data;
        console.log(users.value);
    } catch (error) {
        console.log("Error fetching users:", error);
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
        console.log("Error starting chat:", error);
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
            const options = { hour: "2-digit", minute: "2-digit", timeZone: "Asia/Kolkata" };

            messages.value = messages.value.map((msg) => {
            return {
                ...msg,
                created_at_IST: new Intl.DateTimeFormat("en-US", options).format(new Date(msg.created_at)),
                updated_at_IST: new Intl.DateTimeFormat("en-US", options).format(new Date(msg.updated_at)),
            };
            });
            console.log(messages.value);
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
    setInterval(fetchMessages, 1000);
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
                    <div class="px-4">
                        <TextInput
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search users to chat"  
                            class=" w-full p-2 !text-base"
                            id="chatSearch"
                        />
                    </div>
                    <div class="flex my-4 px-4">
                        <span class="w-full h-0.5 bg-accent"></span>
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
                                        :src="chatUser.profile_pic"
                                        alt="User Avatar"
                                        class="w-12 h-12 rounded-full object-cover"
                                    />
                                    <div class="flex flex-col">
                                        <div class="font-medium text-lg pl-5 flex flex-row items-center">{{ chatUser.user }} <CheckBadgeIcon v-if="chatUser.isTutor === 'Yes'" class="w-5 h-5 text-accent ml-1.5"/></div>
                                        <p class="text-gray-600 text-base pl-5">{{ chatUser.degree }}</p>
                                        <p v-if="chatUser.lastMessage != null" class="text-gray-600 text-base pl-5 italic">"{{ chatUser.lastMessage }}"</p>
                                    </div>
                                </div>
                            </li>
                        </template>

                        <!-- Show Filtered Users if there is a search query -->
                        <template v-else>
                            <li
                                v-if="filteredUsers.length > 0"
                                v-for="user in filteredUsers"
                                :key="user.id"
                                @click="startChat(user.id)"
                                class="flex cursor-pointer items-center p-2 py-4 transition hover:bg-secondary/15 border-t border-gray-200"
                            >
                                <div class="flex justify-evenly items-center">
                                    <img
                                        :src="user.profile_pic"
                                        alt="User Avatar"
                                        class="w-12 h-12 rounded-full object-cover"
                                    />
                                    <div class="flex flex-col">
                                        <div class="font-medium text-lg pl-5 flex flex-row items-center">{{ user.user }} <CheckBadgeIcon v-if="user.isTutor === 'Yes'" class="w-5 h-5 text-accent ml-1.5"/></div>
                                        <p class="text-gray-600 text-base pl-5">{{ user.degree }}</p>
                                    </div>
                                </div>
                            </li>
                            <p v-else class="text-gray-600 text-center mt-8">
                                No matching user found. Please try again.
                            </p>
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
                                    :src="activeChatUser.profile_pic"
                                    alt="User Avatar"
                                    class="w-12 h-12 rounded-full object-cover"
                                />
                                <div v-if="activeChatUser">
                                    <p  class="font-bold text-xl">
                                        {{ activeChatUser.user }}
                                    </p>
                                    <p  class="text-lg text-gray-600">
                                        {{ activeChatUser.degree }}
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
                                        class="flex flex-col rounded-lg py-2 px-3 text-white max-w-96 break-all justify-end whitespace-normal h-full text-left"
                                    >
                                        <p>
                                            {{ message.message }}
                                        </p>
                                        <p 
                                            class="text-gray-300 text-xs "
                                            :class="{
                                                'self-end':
                                                    message.user_id === currentUserId,
                                            }"
                                        >
                                            {{ message.created_at_IST }}
                                        </p>
                                    </div>
                                </div>
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
                                    class="!p-3"
                                    @click="sendMessage"
                                >
                                    <PaperAirplaneIcon class="w-6 font-bold" />
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
