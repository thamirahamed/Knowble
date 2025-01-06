<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import InputLabel from './InputLabel.vue';
import PrimaryButton from './PrimaryButton.vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';
import TextInput from './TextInput.vue';
import Slider from './Slider.vue';
import DynamicDropdown from './DynamicDropdown.vue';

const props = defineProps({
    openModal: Boolean,
    closeModal: Function,
    sModules: Array,
});

// Initialize the form
const form = useForm({
    name: '',
    module: '',
    groupSize: 4,
});

const submitCreateGroup = () => {

    // Prepare the data to be sent
    const payload = {
        name: form.name,
        module: form.module,
        groupSize: form.groupSize,
    };

    // Submit the form using router.post
    router.post('/peer-group/create', payload, {
        onSuccess: () => {
            alert('Peer group created successfully!');
            props.closeModal(); // Close the modal on success
            form.reset(); // Reset the form fields
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            alert('Failed to create peer group: ' + Object.values(errors).join(', ')); // Show error messages
        },
    });
};

</script>

<template>
    <div v-if="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-md max-w-lg w-full overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center py-2 px-6 bg-primary">
                <h2 class="text-lg  font-light text-white">Create Peer Group</h2>
                <button id="closeModal" @click="closeModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 px-6 pb-4">
                <form @submit.prevent="submitCreateGroup">
                    <div class="mb-4">
                        <inputLabel for="name" value="Group Name" />
                        <TextInput
                            id="name"
                            placeholder="Enter Group Name"
                            v-model="form.name"
                            class="w-full mt-1"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="group_module" value="Module" />
                        <DynamicDropdown 
                            label="Module"
                            id="module-dropdown"
                            :options="sModules"
                            v-model="form.module"
                        />
                        <InputError class="mt-2" :message="form.errors.module" />
                    </div>
                    <div class="mb-4">
                        <Slider 
                            :min="2" 
                            :max="5" 
                            :value="form.groupSize" 
                            sLabel="Group Size" 
                            v-model="form.groupSize"
                        />
                    </div>
                    <div class="mt-2 text-right">
                        <PrimaryButton
                            id="createGroupBtn"
                            type="submit"
                        >
                            Create Group
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>