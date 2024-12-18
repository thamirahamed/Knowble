<script setup>
import { router } from "@inertiajs/vue3";
import requests from "@/Pages/Tutor/Requests.vue";

const props = defineProps({
    approvals: Array,
});
// // Map user details to sessions
// const requests = ref(
//     props.approvals.map((session) => {
//         const user = props.userdetails.find((user) => user.id === session.tutor_id);
//         return {
//             id: session.id,
//             tutor_name: user ? user.name : 'Unknown Tutor',
//             status: session.status,
//         };
//     })
// );

// Approve a request
const approveRequest = (id) => {
    router.post(route('session.approve', { id }), {}, {
        onSuccess: () => {
            // Update the local list (optional: fetch from backend again)
            requests.value = requests.value.map((req) =>
                req.id === id ? { ...req, status: 'approved' } : req
            );
        },
    });
};

// Reject a request
const rejectRequest = (id) => {
    router.post(route('session.reject', { id }), {}, {
        onSuccess: () => {
            // Update the local list (optional: fetch from backend again)
            requests.value = requests.value.map((req) =>
                req.id === id ? { ...req, status: 'rejected' } : req
            );
        },
    });
};
</script>

<template>
    <div class="flex justify-center items-start min-h-screen bg-gray-100 p-6">
        <div class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
            <!-- Header -->
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Approval Requests</h1>

            <!-- Request List -->
            <div v-if="requests.length > 0" class="space-y-4">
                <div
                    v-for="request in requests"
                    :key="request.id"
                    class="flex justify-between items-center p-4 bg-gray-50 rounded-md shadow-sm"
                >
                    <!-- Tutor Info -->
                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ request.tutor_name }}</p>
                        <p class="text-sm text-gray-500">Status: {{ request.status }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button
                            v-if="request.status === 'pending'"
                            @click="approveRequest(request.id)"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded-md transition"
                        >
                            Approve
                        </button>
                        <button
                            v-if="request.status === 'pending'"
                            @click="rejectRequest(request.id)"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded-md transition"
                        >
                            Reject
                        </button>
                        <span
                            v-else
                            :class="{
                                'text-green-500': request.status === 'approved',
                                'text-red-500': request.status === 'rejected',
                            }"
                            class="font-semibold"
                        >
                            {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-gray-500 text-center py-6">
                No approval requests available.
            </div>
        </div>
    </div>
</template>

