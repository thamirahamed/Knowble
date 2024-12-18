<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import SidebarLink from "@/Components/SidebarLink.vue";
import { ref } from "vue";
import Modules from "@/Pages/Tutor/Modules.vue";
import Sessions from "@/Pages/Tutor/Sessions.vue";
import Requests from "@/Pages/Tutor/Requests.vue";

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
    rejectedReason: {
        type: Object,
        required: true,
    },
    tutorsSelectedModules: {
        type: Array,
        required: true,
    },
    approvals: {
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
        <div class="flex justify-center pt-8">
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
                        <li>
                            <SidebarLink
                                @click.prevent="activeContent = 'Requests.vue'"
                                :active="activeContent === 'Requests.vue'"
                            >
                                Requests
                            </SidebarLink>
                        </li>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="flex w-full p-4 overflow-y-auto">
                    <template v-if="activeContent === 'Modules'">
                        <Modules
                            :approvedModules="props.approvedModules"
                            :rejectedModules="props.rejectedModules"
                            :rejectedReason="props.rejectedReason"
                            :tutorsSelectedModules="props.tutorsSelectedModules"
                            @tutorRequest="TutorRequest"
                        />
                    </template>

                    <template v-else-if="activeContent === 'Sessions'">
                        <Sessions />
                    </template>

                    <template v-else-if="activeContent === 'Requests.vue'">
                        <Requests
                            :approvals="props.approvals"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
