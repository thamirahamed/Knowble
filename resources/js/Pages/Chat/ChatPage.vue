<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from "@inertiajs/vue3";

import { ref } from 'vue';

// Props received from the backend via Inertia
defineProps({
  users: Array, // Array of user objects
});

// Reactive variables
const selectedUser = ref(null);

// Methods
const selectUser = (user) => {
  selectedUser.value = user;
};

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Chat" />

        <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-1/4 bg-gray-100 border-r">
            <h2 class="text-lg font-semibold p-4">Users</h2>
            <ul>
            <li 
                v-for="user in users" 
                :key="user.id" 
                @click="selectUser(user)" 
                class="p-3 cursor-pointer hover:bg-gray-200"
            >
                {{ user.name }}
            </li>
            </ul>
        </div>
    
        <!-- Chat Window -->
        <div class="w-3/4 flex items-center justify-center">
            <p class="text-gray-500">Select a user to start chatting</p>
        </div>
        </div>
    </AuthenticatedLayout>
</template>

  