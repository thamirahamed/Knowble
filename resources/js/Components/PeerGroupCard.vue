<script setup>
import { UserGroupIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    peerGroup: Array,
});

const viewGroup = (grpid) => {
    router.visit(route('peergroup', { id: grpid }));
}  
</script>

<template>
    <div class="flex w-full h-auto items-center px-4 py-2 rounded-md border border-gray-200 shadow-md space-x-4 mb-4">
        <div class="flex flex-1 flex-col text-lg">
            <h1 v-if="peerGroup.isUserLeader === true" class="text-slate-900 font-semibold flex items-center">{{ peerGroup.name }} <span class="font-normal text-sm ml-2 text-accentdark/95">(Leader)</span></h1>
            <h1 v-else class="text-slate-900 font-semibold">{{ peerGroup.name }}</h1>
            <h2 class="text-slate-500 font-light">{{ peerGroup.degree }}</h2>
            <h2 class="text-slate-500 font-light">{{ peerGroup.module }}</h2>
            <h2 class="text-slate-500 inline-flex items-center"><UserGroupIcon class="w-5 h-5 mr-2" /> {{ peerGroup.currentMembers }} / {{ peerGroup.totalMembers }}</h2>
        </div>
        <div>
            <PrimaryButton 
                @click="viewGroup(peerGroup.id)"
                :id = "'viewGroupBtn' + peerGroup.id"
            >   
                View Group
            </PrimaryButton>
        </div>
    </div>
</template>
