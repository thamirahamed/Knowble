<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SidebarLink from "@/Components/SidebarLink.vue";
import { ref } from "vue";

// Track the currently active content
const activeContent = ref("Modules");

const props = defineProps({
    approvedModules: {
        type: Array,
        required: true,
    },
    rejectedModules: {
        type: Array,
        required: true,
    },
});

const TutorRequest = (id) => {
    router.post(`/admin/request/${id}`, {}, {
        onSuccess: () => {
            console.log("Request sent");
        },
    });
};
</script>

<template>
    <Head title="Tutor Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-center pt-8 ">
            <div class="flex max-w-7xl w-full bg-white rounded-md shadow-sm h-[85vh]">
                <!-- Sidebar -->
                <div class="max-w-72 w-full flex flex-col border-r border-gray-300">
                    <h2 class="text-xl font-bold p-4">Tutor Menu</h2>
                    <ul class="text-lg">
                        <li>
                            <SidebarLink
                                @click.prevent="activeContent = 'Modules'"
                                :active="activeContent === 'Modules'"
                            >
                                Modules
                            </SidebarLink>
                        </li>
                        <li>
                            <SidebarLink 
                                @click.prevent="activeContent = 'Sessions'"
                                :active="activeContent === 'Sessions'"
                            >
                                Sessions
                            </SidebarLink>
                        </li>
                    </ul>
                </div>

                <!-- Main Content -->
                <!-- <div class="flex w-full p-4">
                    <h1 class="text-2xl font-semibold">
                        {{ $page.component === 'ApprovedModules' ? 'Approved Modules' : 'Rejected Modules' }}
                    </h1>

                    <div v-if="$page.component === 'ApprovedModules'">
                        <div v-for="module in approvedModules" :key="module.id" class="my-3">
                            <p class="text-gray-700">{{ module.module_name }}</p>
                        </div>
                    </div>

                    <div v-else-if="$page.component === 'RejectedModules'">
                        <div v-for="module in rejectedModules" :key="module.id" class="flex justify-between items-center my-3">
                            <p class="text-gray-700">{{ module.module_name }}</p>
                            <SecondaryButton @click="TutorRequest(module.id)">Request Again</SecondaryButton>
                        </div>
                    </div>
                </div> -->

                <div class="flex w-full p-4 overflow-y-auto">
                    <!-- Conditionally Render Content Based on Active Link -->
                    <template v-if="activeContent === 'Modules'">
                        <div class="flex flex-col w-full">
                            <div class="mb-4" >
                                <h1 class="text-xl font-semibold">Approved Modules</h1>
                                <div v-for="module in approvedModules" :key="module.id" class="my-2">
                                    <p class="text-gray-700 text-lg">{{ module.module_name }}</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h1 class="text-xl font-semibold">Rejected Modules</h1>
                                <div v-for="module in rejectedModules" :key="module.id" class="my-2">
                                    <p class="text-gray-700 text-lg">{{ module.module_name }}</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h1 class="text-xl font-semibold">Reason for Rejection</h1>
                                
                            </div>
                        </div>
                    </template>

                    <template v-else-if="activeContent === 'Sessions'">
                        <h1 class="text-2xl font-semibold">Sessions</h1>
                        
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
