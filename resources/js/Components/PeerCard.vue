<script setup>
import { UserPlusIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    peerName: String,
    profilePic: String,
    peerId: String,
    degree: String,
    isMember: String,
    groupId: String,
    isGroupFull: Function,
});

const addPeer = () => {
    // Prepare the data to be sent
    const payload = {
        peer_group_id: props.groupId,
        peer_id: props.peerId
    };

    // Submit the form using router.post
    router.post('/peer-group/add', payload, {
        onSuccess: () => {
            
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            // Check if the error response contains specific error messages
            alert('Failed to add peer: ' + Object.values(errors).join(', '));
        },
    });
};
</script>

<template>
    <div class="flex w-full h-auto items-center px-4 py-2 rounded-md border border-gray-200 shadow-md space-x-4">
        <div class="flex">
            <img
                :src="props.profilePic"
                alt="Profile Picture"
                class="w-12 h-12 rounded-full object-cover"
            />
        </div>
        <div class="flex flex-1 flex-col text-lg">
            <h1 class="text-slate-900 font-semibold">{{ peerName }}</h1>
            <h2 class="text-slate-500 font-light">{{ degree }}</h2>
        </div>
        <div>
            <PrimaryButton 
                :id="'addPeerBtn-' + peerId" 
                class="!text-sm !p-2 !rounded-full" 
                @click="addPeer"
                v-if="isMember === 'False' && !isGroupFull"
            >   
                <UserPlusIcon class="text-white w-5 h-5" />
            </PrimaryButton>
            <div v-else-if="isMember === 'True'" class="text-gray-600 italic">
                <p>Added</p>
            </div>
            <div v-else>

            </div>
        </div>
    </div>
</template>
