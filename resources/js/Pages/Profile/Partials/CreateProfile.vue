<script setup>
import { usePage } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Get courses and levels from the page props
const pageProps = usePage().props;
const validInputs = {
    courses: pageProps?.courses || [], // Fallback if courses are undefined
    levels: pageProps?.levels || [], // Fallback if levels are undefined
};
console.log(validInputs); // Log to verify the data

// Initialize the form
const form = useForm({
    course: '',
    level: '',
    profile_pic: null,
    cb_number: '', // Will be set automatically from email
});

// Extract CB Number from the email automatically
const userEmail = usePage().props.auth.user.email;
const cbNumber = userEmail.split('@')[0]; // Assuming CB Number is part of the email prefix
form.cb_number = cbNumber; // Set CB Number in the form

// Handle form submission
const submitForm = () => {
    form.post(route('profile.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Create Your Profile</h2>
            <p class="mt-1 text-sm text-gray-600">
                Please fill in the details below to complete your profile.
            </p>
        </header>

        <form @submit.prevent="submitForm" class="mt-6 space-y-6">
            <!-- Courses -->
            <div>
                <InputLabel for="course" value="Course" />
                <select
                    id="course"
                    class="mt-1 block w-full"
                    v-model="form.course"
                    required
                >
                    <option value="">Select Course</option>
                    <!-- Dynamically populate the courses -->
                    <option v-for="course in validInputs.courses" :key="course.id" :value="course.id">
                        {{ course.CourseName }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.course" />
            </div>

            <!-- Levels -->
            <div>
                <InputLabel for="level" value="Level" />
                <select
                    id="level"
                    class="mt-1 block w-full"
                    v-model="form.level"
                    required
                >
                    <option value="">Select Level</option>
                    <!-- Dynamically populate levels based on the selected course -->
                    <option
                        v-for="level in validInputs.levels.filter(l => l.cource_id === form.course)"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.level }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.level" />
            </div>

            <!-- Profile Picture -->
            <div>
                <InputLabel for="profile_pic" value="Profile Picture" />
                <input
                    type="file"
                    id="profile_pic"
                    class="mt-1 block w-full"
                    @change="event => form.profile_pic = event.target.files[0]"
                />
                <InputError class="mt-2" :message="form.errors.profile_pic" />
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Create Profile</PrimaryButton>
            </div>
        </form>
    </section>
</template>
