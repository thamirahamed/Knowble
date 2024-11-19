<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { defineProps, computed, ref, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Access profile from props or usePage()
const props = defineProps({
    profile: {
        type: Object,
        required: true
    },
    courses: {
        type: Array,
        required: true
    },
    levels: {
        type: Array,
        required: true
    }
});

// Define the computed property for the profile picture URL
const profilePictureUrl = computed(() => {
    return `/private-profile-picture/${props.profile.profile_pic}`;
});

const user = usePage().props.auth.user;

// Initialize the form with profile data
const form = useForm({
    name: user.name,
    email: user.email,
    cb_number: props.profile?.cb_number || '',
    profile_pic: null, // Set this as null initially
    course_id: props.profile?.course_id || '',
    level_id: props.profile?.level_id || ''
});

// Reactive variable to hold the selected profile picture preview
const profilePicPreview = ref(profilePictureUrl.value);

// Handle file change for profile picture
const handleFileChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            profilePicPreview.value = reader.result; // Preview the image
        };
        reader.readAsDataURL(file);
        form.profile_pic = file; // Add the file to the form
    } else {
        form.profile_pic = null; // Explicitly set to null if no file is selected
    }
};
// Watch for changes to course_id and filter levels
const filteredLevels = ref(props.levels);
watch(() => form.course_id, (newCourseId) => {
    filteredLevels.value = props.levels.filter(level => level.course_id === newCourseId);
});
const removeProfilePic = async () => {
    const defaultImageUrl = '/private-profile-picture/default.png';
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
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information, email address, and more.
            </p>
        </header>

        <!-- Form with enctype -->
        <form @submit.prevent="form.post(route('profile.update'))" enctype="multipart/form-data" class="mt-6 space-y-6">

                <!-- Name Field -->
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        readonly
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
                        class="mt-1 block w-full"
                        v-model="form.email"
                        readonly
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- CB Number Field -->
                <div>
                    <InputLabel for="cb_number" value="CB Number" />
                    <TextInput
                        id="cb_number"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.cb_number"
                        readonly
                    />
                    <InputError class="mt-2" :message="form.errors.cb_number" />
                </div>

            <!-- Profile Picture Field -->
            <div>
                <InputLabel for="profile_pic" value="Profile Picture" />
                <input
                    type="file"
                    id="profile_pic"
                    @change="handleFileChange"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                />
                <InputError class="mt-2" :message="form.errors.profile_pic" />

                <!-- Display profile picture preview -->
                <div v-if="profilePicPreview">
                    <img :src="profilePicPreview" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover" />
                </div>
                <button
                    v-if="profilePicPreview"
                    type="button"
                    @click="removeProfilePic"
                    class="top-2 right-2 bg-red-500 text-white text-sm px-2 py-1 rounded-md shadow-md"
                >
                    Remove
                </button>
            </div>

            <!-- Course Dropdown -->
            <div>
                <InputLabel for="course" value="Course" />
                <select id="course" v-model="form.course_id" class="mt-1 block w-full">
                    <option v-for="course in courses" :key="course.id" :value="course.id">
                        {{ course.CourseName }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.course_id" />
            </div>

            <!-- Level Dropdown -->
            <div>
                <InputLabel for="level" value="Level" />
                <select id="level" v-model="form.level_id" class="mt-1 block w-full">
                    <option v-for="level in filteredLevels" :key="level.id" :value="level.id">
                        {{ level.level }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.level_id" />
            </div>

            <!-- Save Button -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
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
