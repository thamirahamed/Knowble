<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import SidebarLink from "@/Components/SidebarLink.vue";
import { ref } from "vue";
import Overview from "@/Pages/Tutor/Overview.vue";
import UpcomingBookings from "@/Pages/Tutor/UpcomingBookings.vue";

// Track the currently active content
const activeContent = ref("Overview");

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
    sessionSlots: {
        type: Array,
        required: true,
    },
    bookings: {
        type: Object,
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
                                id="overviewBtn"
                                @click.prevent="activeContent = 'Overview'"
                                :active="activeContent === 'Overview'"
                            >
                                Overview
                            </SidebarLink>
                        </li>
                        <li>
                            <SidebarLink 
                                id="upcBookingsBtn"
                                @click.prevent="activeContent = 'UpcomingBookings'"
                                :active="activeContent === 'UpcomingBookings'"
                            >
                                Upcoming Bookings
                            </SidebarLink>
                        </li>
                        <li>
                            <SidebarLink 
                                id="comBookingsBtn"
                                @click.prevent="activeContent = 'CompletedBookings'"
                                :active="activeContent === 'CompletedBookings'"
                            >
                                Completed Bookings
                            </SidebarLink>
                        </li>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="flex flex-col w-full px-10 py-8 overflow-y-auto">
                    <template v-if="activeContent === 'Overview'">
                        <Overview
                            :approvedModules="props.approvedModules"
                            :rejectedModules="props.rejectedModules"
                            :rejectedReason="props.rejectedReason"
                            :tutorsSelectedModules="props.tutorsSelectedModules"
                            :sessionSlots="props.sessionSlots"
                            @tutorRequest="TutorRequest"
                        />
                    </template>

                    <template v-else-if="activeContent === 'UpcomingBookings'">
                        <UpcomingBookings
                            :bookings="props.bookings"
                        />
                    </template>
                    <template v-else-if="activeContent === 'CompletedBookings'">
                        <p>Completed / Cancelled Bookings here</p>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
