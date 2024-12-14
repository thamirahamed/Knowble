<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { defineProps, computed, ref, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DynamicDropdown from "@/Components/DynamicDropdown.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ArrowUpTrayIcon, TrashIcon } from '@heroicons/vue/24/solid'

// Access profile from props or usePage()
const props = defineProps({
    profile: {
        type: Object,
        required: true
    },
    school: {
        type: Array,
        required: true
    },
    level: {
        type: Array,
        required: true
    },
    degree: {
        type: Array,
        required: true
    },
    semester: {
        type: Array,
        required: true
    }
});
console.log(props.school);
// Define the computed property for the profile picture URL
const profilePictureUrl = computed(() => {
    return `${props.profile.profile_pic}`;
});

const user = usePage().props.auth.user;

// Initialize the form with profile data
const form = useForm({
    name: user.name,
    email: user.email,
    cb_number: props.profile?.cb_number || '',
    profile_pic: null, // Set this as null initially
    school_id: props.profile?.school_id || '',
    level_id: props.profile?.level_id || '',
    degree_id: props.profile?.degree_id || '',
    semester_id: props.profile?.semester_id || '',
});


// Reactive variable to hold the selected profile picture preview
const profilePicPreview = ref(profilePictureUrl.value);

// Handle file change for profile picture
const handleFileChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();

        // Listen for the file load event
        reader.onload = (e) => {
            profilePicPreview.value = e.target.result; // Update the preview with the base64 data
        };

        reader.readAsDataURL(file); // Read the file as a Data URL
        form.profile_pic = file; // Attach the file to the form
    } else {
        form.profile_pic = null; // Clear the form's profile picture if no file is selected
        profilePicPreview.value = profilePictureUrl.value; // Reset to the existing profile picture
    }
};

// Watch for changes to course_id and filter levels
const filteredLevels = ref(props.degree);
watch(() => form.school_id, (newCourseId) => {
    filteredLevels.value = props.degree.filter(level => level.school_id === newCourseId);
});

//Remove and Set default Pfp
const removeProfilePic = async () => {
    const defaultImageUrl = 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg';
    profilePicPreview.value = defaultImageUrl;

    // Fetch the default image as a Blob
    const response = await fetch(defaultImageUrl);
    const blob = await response.blob();

    // Create a File object from the Blob
    const file = new File([blob], 'default.png', { type: blob.type });

    // Update the form with the File object
    form.profile_pic = file;
};




</script>

<template>

    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>
            
            <p class=" text-base text-gray-600">
                Update your account's profile information, email address, and more.
            </p>
        </header>

        <!-- Form with enctype -->
        <form @submit.prevent="form.post(route('profile.update'))" enctype="multipart/form-data" class="mt-6 flex flex-col">
            <div class="flex flex-row items-center">
                <!-- Profile Picture Field -->
                <div class="flex flex-col p-4 px-10 gap-4">
                    <!-- Display profile picture preview -->
                    <InputLabel for="profile_pic" value="Profile Picture" />
                    <div v-if="profilePicPreview">
                        <img :src="profilePicPreview" alt="Profile Picture" class="w-80 h-80 aspect-square rounded-full object-cover" />
                    </div>
                    <InputError class="mt-2" :message="form.errors.profile_pic" />
                    <div class="flex items-center justify-between">
                        <input
                            type="file"
                            id="profile_pic"
                            @change="handleFileChange"
                            accept=".jpg, .png, .jpeg"
                            class="hidden"
                        />
                        <!-- Custom button styled as a label -->
                        <label
                            for="profile_pic"
                            class="cursor-pointer rounded-lg bg-accent px-5 py-2.5 text-center font-semibold tracking-wide text-white relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:translate-x-[-150%] after:translate-y-[150%] after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-accentdark after:transition-transform after:duration-550  hover:after:translate-x-[0%] hover:after:translate-y-[0%]"
                        >
                            Upload
                        </label>
                        <DangerButton
                            v-if="profilePicPreview"
                            type="button"
                            @click="removeProfilePic"
                            class="w-fit"
                            :icon="true" 
                            iconPlacement="left"
                        >
                            <template #icon>
                                <TrashIcon class="h-5 w-5 text-white" />
                            </template>
                            Remove  
                        </DangerButton>
                    </div>
                </div>

                <div class="flex flex-col h-full flex-1 p-4 px-10 gap-4">
                    <!-- Name Field -->
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full text-gray-500 hover:border-gray-300 focus:!ring-0 focus:border-gray-400"
                            v-model="form.name"
                            disabled
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full text-gray-500 hover:border-gray-300 focus:!ring-0 focus:border-gray-400"
                            v-model="form.email"
                            disabled
                            autocomplete="email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- School Dropdown -->
                    <div>
                        <InputLabel for="school" value="School of Study"/>
                        <DynamicDropdown
                            label="School of Study"
                            id="school"
                            :options="school"
                            v-model="form.school_id"
                            :error="form.errors.school_id"
                        />
                    </div>

                    <!-- Degree Dropdown -->
                    <div>
                        <InputLabel for="degree" value="Degree Program"/>
                        <DynamicDropdown
                            label="Degree Program"
                            id="degree"
                            :options="filteredLevels"
                            v-model="form.degree_id"
                            :error="form.errors.degree_id"
                        />
                    </div>
                    <!-- Level Dropdown -->
                    <div>
                        <InputLabel for="level" value="Level"/>
                        <DynamicDropdown
                            label="Level"
                            id="level"
                            :options="level"
                            v-model="form.level_id"
                            :error="form.errors.level_id"
                        />
                    </div>
                    <!-- Semester Dropdown -->
                    <div>
                        <InputLabel for="semester" value="Semester"/>
                        <DynamicDropdown
                            label="Semester"
                            id="semester"
                            :options="semester"
                            v-model="form.semester_id"
                            :error="form.errors.semester_id"
                        />
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center px-10 w-full justify-end">
                <PrimaryButton class="flex gap-1 items-center" :disabled="form.processing">Save</PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
