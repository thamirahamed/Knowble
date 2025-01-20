<script setup>
import AddMembersModal from '@/Components/AddMembersModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TrashIcon, UserPlusIcon, ArrowLeftEndOnRectangleIcon, UserGroupIcon, CheckBadgeIcon, UserMinusIcon } from '@heroicons/vue/24/solid';
import { router, Head } from '@inertiajs/vue3';
import { defineProps, ref } from "vue";

const props = defineProps({
    peerGroup: Array,
    peerGroupMembers: Array,
    groupSessions: Array,
    pastGroupSessions: Array,
    peers: Array,
});

console.log(JSON.stringify(props.peerGroupMembers, null, 2));

const openModal = ref(null);

const closeModal = () => {
    openModal.value = null;
};

const openModalWithData = () => {
    openModal.value = true;
};

const isPeerGroupFull = () => {
    return props.peerGroup.currentMembers >= props.peerGroup.totalMembers;
};

const removePeer = (peerId) => {
    if (confirm("Are you sure you want to remove this member?")) {
        // Prepare the data to be sent
        const payload = {
            peer_group_id: props.peerGroup.id,
            peer_id: peerId
        };
    
        // Submit the form using router.post
        router.post('/peer-group/remove', payload, {
            onSuccess: () => {
                alert('Peer member removed successfully!');
            },
            onError: (errors) => {
                console.log(errors); // Log errors to the console for debugging
                // Check if the error response contains specific error messages
                alert('Failed to remove peer: ' + Object.values(errors).join(', '));
            },
        });
    }
};

const leaveGroup = () => {
    // Prepare the data to be sent
    const payload = {
        peer_group_id: props.peerGroup.id
    };

    // Submit the form using router.post
    router.post('/peer-group/leave', payload, {
        onSuccess: () => {
            alert('Left peer group successfully!');
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            alert('Failed to leave peer group: ' + Object.values(errors).join(', '));
        },
    });
};

const deleteGroup = () => {
    // Show a confirmation popup before proceeding
    if (confirm("Are you sure you want to delete this group? This action cannot be undone.")) {
        // Prepare the data to be sent
        const payload = {
            peer_group_id: props.peerGroup.id
        };

        // Submit the form using router.post
        router.post('/peer-group/delete', payload, {
            onSuccess: () => {
                alert('Peer group deleted successfully!');
            },
            onError: (errors) => {
                console.error(errors); // Log errors to the console for debugging
                alert('Failed to delete peer group: ' + Object.values(errors).join(', '));
            },
        });
    }
};

// Redirect to Join Meeting with the meeting_url from a specific booking
const joinMeeting = (booking) => {
    // Get the base meeting URL from the booking
    let meetingUrl = booking.meeting_url;
    
    // Create the tutor's name (convert to lowercase and replace spaces with hyphens)
    const studentName = booking.student_name.replace(" ", "-").toLowerCase();  // Example: Emily Davis -> emily-davis
    
    // append the 'name' parameter
    meetingUrl += `&name=${studentName}`;

    router.visit(route("meetings.index", { meetingUrl, id: booking.id }));
};

// Function to format date to words with suffix
const formatDateToWords = (date) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  const d = new Date(date);
  const day = d.getDate();
  const suffix = getDaySuffix(day);
  
  const formatter = new Intl.DateTimeFormat('en-US', options);
  return `${formatter.format(d).replace(day, day + suffix)}`;
};

// Function to get the day suffix (st, nd, rd, th)
const getDaySuffix = (day) => {
  const j = day % 10;
  const k = day % 100;
  if (j === 1 && k !== 11) {
    return 'st';
  }
  if (j === 2 && k !== 12) {
    return 'nd';
  }
  if (j === 3 && k !== 13) {
    return 'rd';
  }
  return 'th';
};

</script>

<template>
<Head :title='peerGroup.name' />
<AuthenticatedLayout>
    <div class="flex flex-col items-center w-full">
        <div class="flex flex-col max-w-[85rem] w-full mt-8 ">
            <div class="flex bg-white rounded-md shadow px-6 py-4 w-full justify-between items-center mb-4">
                <div>
                    <h1 class="text-3xl text-slate-900 font-bold tracking-wide">{{ peerGroup.name }}</h1>
                    <h2 class="text-xl text-slate-500">{{ peerGroup.degree }}</h2>
                    <h2 class="text-xl text-slate-500">{{ peerGroup.module }}</h2>
                    <h2 class="text-xl text-slate-500 inline-flex items-center"><UserGroupIcon class="w-6 h-6 mr-2" /> {{ peerGroup.currentMembers }} / {{ peerGroup.totalMembers }}</h2>
                </div>
                <div class="flex gap-2">
                    <PrimaryButton
                        :icon="true" 
                        iconPlacement="left"
                        @click="openModalWithData"
                        id="addMembersBtn"
                        v-if="peerGroup.isUserLeader === 'Yes' && !isPeerGroupFull() "
                    >
                        <template #icon>
                            <UserPlusIcon class="text-white" />
                        </template>
                        Add Members
                    </PrimaryButton>
                    <DangerButton
                        :icon="true" 
                        iconPlacement="left"
                        @click="deleteGroup"
                        v-if="peerGroup.isUserLeader === 'Yes'"
                        id="deleteGroupBtn"
                    >
                        <template #icon>
                            <TrashIcon class="text-white" />
                        </template>
                        Delete Group
                    </DangerButton>
                    <DangerButton
                        :icon="true" 
                        iconPlacement="left"
                        @click="leaveGroup"
                        v-if="peerGroup.isUserMember === 'Yes'"
                        id="leaveGroupBtn"
                    >
                        <template #icon>
                            <ArrowLeftEndOnRectangleIcon class="text-white" />
                        </template>
                        Leave Group
                    </DangerButton>
                </div>
                <AddMembersModal
                    :openModal="openModal"
                    :closeModal="closeModal"
                    :peers="peers"
                    :groupId="peerGroup.id"
                    :isGroupFull="isPeerGroupFull()"
                />
            </div>
            <span class="w-full h-0.5 bg-accent mb-4"></span>
            <div class="flex gap-8">
                <!-- Upcoming Sessions -->
                <div class="flex flex-col flex-1">
                    <div v-if="peerGroup.isUserLeader === 'Yes' || peerGroup.isUserMember === 'Yes'" class="flex flex-col flex-1 bg-white rounded-md shadow px-6 py-4 mb-4 min-h-72 max-h-[30rem] overflow-y-auto">
                        <h1 class="text-xl text-slate-900 font-bold mb-4">Upcoming Sessions</h1>
                        <div v-if="groupSessions.length > 0" class="flex flex-col gap-3 overflow-y-auto">
                            <div
                                v-for="(session, index) in groupSessions"
                                :key="index"
                                class="flex flex-col bg-secondary/5 px-5 py-4 rounded-md shadow-md"
                            >
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="flex items-center">
                                            <img
                                                :src="session.profile_pic"
                                                alt="Profile Picture"
                                                class="w-8 h-8 mr-3 rounded-full object-cover"
                                            />
                                            <p class="text-lg font-semibold text-gray-800">{{ session.tutor_name }}</p>
                                            <CheckBadgeIcon class="ml-1 w-5 h-5 text-accent" />
                                        </div>
                                        <p class="text-lg text-gray-600">{{ session.module_name }}</p>
                                        <p class="text-lg text-gray-600">{{ formatDateToWords(session.session_date) }} | {{ session.start_time }} - {{ session.end_time }}</p>            
                                        <p v-if="session.notes " class="text-lg text-gray-600">Notes: {{ session.notes }}</p>
                                    </div>
                                    <PrimaryButton :id="'joinMeeting-' + session.id" class="!text-sm" @click="joinMeeting(session)">Join Now</PrimaryButton>
                                </div>
                            </div>
                        </div>
                        <div v-else class="m-auto flex flex-col items-center">
                            <p class="text-base text-gray-600">No upcoming group sessions with tutor.</p>
                            <p class="text-base text-gray-600">Group leader has to place booking.</p>
                        </div>
                    </div>
                    <div class="flex flex-col flex-1 bg-white rounded-md shadow px-6 py-4 mb-4 min-h-64 h-fit max-h-[30rem] overflow-y-auto">
                        <h1 class="text-xl text-slate-900 font-bold mb-4">Past Sessions</h1>
                        <div v-if="pastGroupSessions.length > 0" class="flex flex-col gap-3 overflow-y-auto flex-wrap">
                            <div   
                                v-for="session in pastGroupSessions"
                                :key="session.id"
                                class="flex justify-between items-center bg-secondary/5 p-4 rounded-md shadow-md xl:w-[49%]" 
                            >
                                <div class="flex flex-col">
                                    <div class="inline-flex">
                                        <img
                                            :src="session.profile_pic"
                                            alt="Profile Picture"
                                            class="w-8 h-8 mr-3 rounded-full object-cover"
                                        />
                                        <div class="flex items-center text-lg font-semibold text-gray-800">
                                            <p>{{ session.tutor_name }}</p>
                                            <CheckBadgeIcon class="ml-1 w-5 h-5 text-accent" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-lg text-gray-600">{{ session.module_name }}</p>
                                        <p class="text-lg text-gray-600">
                                            {{ formatDateToWords(session.session_date) }} | {{ session.start_time }} - {{ session.end_time }}
                                        </p>
                                        <p v-if="session.notes && session.status==='completed'" class="text-lg text-gray-600">Notes: {{ session.notes }}</p>
                                        <p v-if="session.notes && session.status==='cancelled'" class="text-lg text-gray-600">Reason: {{ session.notes }}</p>
                                        <p v-if="session.status==='cancelled'" class="text-lg text-red-500">Cancelled</p>
                                        <p v-if="session.status==='completed'" class="text-lg text-accent">Completed</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col w-full">
                            <p class="text-gray-600 m-auto">No past group sessions found.</p>
                        </div>
                    </div>
                </div>
                <!-- Members -->
                <div class="flex flex-col bg-white max-w-xl rounded-md shadow px-6 py-4 flex-1 mb-4 h-fit">
                    <h1 class="text-xl text-slate-900 font-bold mb-4">Members</h1>
                    <div class="bg-secondary/5 py-4 px-5 shadow rounded-md mb-2 w-full flex items-center gap-4" >
                        <div class="flex">
                            <img
                                :src="peerGroup.leaderPfp"
                                alt="Profile Picture"
                                class="w-12 h-12 rounded-full object-cover"
                            />
                        </div>
                        <div class="flex flex-1 flex-col text-lg">
                            <h1 class="text-slate-900 font-semibold flex items-center">{{ peerGroup.leaderName }} <span class="font-normal text-sm ml-2 text-accentdark/95">(Leader)</span></h1>
                            <h2 class="text-slate-500 font-light">{{ peerGroup.degree }}</h2>
                        </div>
                    </div>
                    <div
                        class="bg-secondary/5 py-4 px-5 shadow rounded-md mb-2 w-full flex items-center gap-4" 
                        v-for="members in peerGroupMembers"
                    >
                        <div class="flex">
                            <img
                                :src="members.profile_pic"
                                alt="Profile Picture"
                                class="w-12 h-12 rounded-full object-cover"
                            />
                        </div>
                        <div class="flex flex-1 flex-col text-lg">
                            <h1 class="text-slate-900 font-semibold flex items-center">{{ members.name }}<span v-if="members.isUser === 'Yes'" class="font-normal text-sm ml-2 text-accentdark/95">(You)</span></h1>
                            <h2 class="text-slate-500 font-light">{{ members.degree }}</h2>
                        </div>
                        <div v-if="peerGroup.isUserLeader === 'Yes'" class="flex">
                            <DangerButton 
                                :id="'removePeerBtn-' + members.id" 
                                class="!p-2 !rounded-full" 
                                @click="removePeer(members.id)"
                            >
                                <UserMinusIcon class="w-5 h-5 text-white" />
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>