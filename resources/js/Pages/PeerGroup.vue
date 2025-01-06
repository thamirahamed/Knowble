<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TrashIcon, UserPlusIcon, ArrowLeftEndOnRectangleIcon, UserGroupIcon } from '@heroicons/vue/24/solid';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    peerGroup: Array,
    peerGroupMembers: Array,
});

console.log(JSON.stringify(props.peerGroup, null, 2));
console.log(JSON.stringify(props.peerGroupMembers, null, 2));

const joinGroup = () => {
    // Prepare the data to be sent
    const payload = {
        peer_group_id: props.peerGroup.id
    };

    // Submit the form using router.post
    router.post('/peer-group/join', payload, {
        onSuccess: () => {
            alert('Joined peer group successfully!');
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            // Check if the error response contains specific error messages
            alert('Failed to join peer group: ' + Object.values(errors).join(', '));
        },
    });
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
                router.push('/home'); // Redirect to the home page after successful deletion
            },
            onError: (errors) => {
                console.error(errors); // Log errors to the console for debugging
                alert('Failed to delete peer group: ' + Object.values(errors).join(', '));
            },
        });
    }
};
</script>

<template>
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
                <div>
                    <PrimaryButton
                        :icon="true" 
                        iconPlacement="left"
                        @click="joinGroup"
                        v-if="peerGroup.isUserLeader === 'No' && peerGroup.isUserMember === 'No' && peerGroup.currentMembers < peerGroup.totalMembers"
                    >
                        <template #icon>
                            <UserPlusIcon class="text-white" />
                        </template>
                        Join Group
                    </PrimaryButton>
                    <DangerButton
                        :icon="true" 
                        iconPlacement="left"
                        @click="deleteGroup"
                        v-if="peerGroup.isUserLeader === 'Yes'"
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
                    >
                        <template #icon>
                            <ArrowLeftEndOnRectangleIcon class="text-white" />
                        </template>
                        Leave Group
                    </DangerButton>
                </div>
            </div>
            <span class="w-full h-0.5 bg-accent mb-4"></span>
            <div>
                <div class="flex flex-col bg-white rounded-md shadow px-6 py-4 w-full mb-4">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>