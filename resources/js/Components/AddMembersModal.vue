<script setup>
import { ref } from 'vue';
import { XMarkIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid';
import { computed } from 'vue';
import TextInput from './TextInput.vue';
import PeerCard from './PeerCard.vue';

const props = defineProps({
    openModal: Boolean,
    peers: Array,
    groupId: String,
    closeModal: Function,
    isGroupFull: Function
});

const searchQuery = ref(''); // Define search query

// Computed property to filter peers based on the search query
const filteredPeers = computed(() => {
    if (!searchQuery.value) {
        return props.peers; // If no search query, return all peers
    }
    return props.peers.filter(peer => 
        peer.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});


</script>

<template>
    <div v-if="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-md max-w-2xl w-full h-fit overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center py-2 px-6 bg-primary">
                <h2 class="text-lg  font-light text-white">Add Members</h2>
                <button id="closeModal" @click="closeModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 px-6 pb-4 flex flex-col gap-3">
                <div class="mb-2">
                    <TextInput 
                        v-model="searchQuery"
                        placeholder="Search Peer by Name"
                        class="w-full"
                    />
                </div>
                <div v-if="isGroupFull" class="-mt-2 pl-1">
                    <p class="text-gray-600 flex items-center"> 
                        <ExclamationCircleIcon class="w-5 h-5 mr-1.5" />
                        The group has reached its maximum capacity.
                    </p>
                </div>
                <div class="flex mb-2">
                    <span class="w-full h-0.5 bg-accent"></span>
                </div>
                <div class="flex flex-col gap-3 overflow-y-auto max-h-96 min-h-fit">
                    <PeerCard 
                        v-for="peer in filteredPeers"
                        :key="peer.user_id"
                        :peerName="peer.name"
                        :profilePic="peer.profilePic"
                        :peerId="peer.user_id"
                        :degree="peer.degreeName"
                        :isMember="peer.isMember"
                        :groupId="groupId"
                        :isGroupFull="isGroupFull"
                    />
                </div>
            </div>
        </div>
    </div>
</template>