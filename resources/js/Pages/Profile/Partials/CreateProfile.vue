<script setup>
import { usePage } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
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
            <h2 class="text-7xl font-black text-gray-900">Create Your Profile</h2>
            <p class="mt-1 text-md text-gray-600">
                Please fill in the details below to complete your profile.
            </p>
        </header>

        <form @submit.prevent="submitForm" class="flex flex-col mt-6 gap-6">
            <!-- Courses -->
            <div>
                <InputLabel for="course" value="Course" />
                <select
                    id="course"
                    class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-accent focus:border-transparent focus:outline-none focus:ring-2 focus:ring-accent"
                    v-model="form.course"
                    required
                >
                    <option value="">Select Course</option>
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
                    class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-accent focus:border-transparent focus:outline-none focus:ring-2 focus:ring-accent"
                    v-model="form.level"
                    required
                >
                    <option value="">Select Level</option>
                    <option
                        v-for="level in validInputs.levels.filter(l => l.course_id === form.course)"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.level }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.level" />
            </div>

            <!-- Submit Button -->
            <div class="flex mt-2 w-full justify-end">
                <PrimaryButton :disabled="form.processing">Create Profile</PrimaryButton>
            </div>
        </form>
    </section>
</template>
