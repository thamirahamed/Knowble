<script setup>
import { usePage } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Get valid inputs (school and year options) from the page props
const pageProps = usePage().props;
const validInputs = pageProps?.validInputs; // Fallback if validInputs is undefined
console.log(validInputs); // Log to verify the data


// Initialize the form
const form = useForm({
    school_of_study: '',
    year: '',
    available_time: '',
    profile_picture: null,
    cb_number: '', // Will be set automatically from email
    role: 'student', // Default to student
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
            <!-- School of Study -->
            <div>
                <InputLabel for="school_of_study" value="School of Study" />
                <select
                    id="school_of_study"
                    class="mt-1 block w-full"
                    v-model="form.school_of_study"
                    required
                >
                    <option value="">Select School</option>
                    <!-- Dynamically populate the schools from validInputs -->
                    <!-- <option v-for="(semesters, school) in validInputs" :key="school" :value="school">
                        {{ school }}
                    </option> -->
                </select>
                <InputError class="mt-2" :message="form.errors.school_of_study" />
            </div>

            <!-- Year and Semester -->
            <div>
                <InputLabel for="year" value="Year and Semester" />
                <select
                    id="year"
                    class="mt-1 block w-full"
                    v-model="form.year"
                    required
                >
                    <option value="">Select Year and Semester</option>
                    <!-- Dynamically populate the semesters based on selected school -->
                    <!-- <option
                        v-for="semester in validInputs[form.school_of_study] || []"
                        :key="semester"
                        :value="semester"
                    >
                        {{ semester }}
                    </option> -->
                </select>
                <InputError class="mt-2" :message="form.errors.year" />
            </div>

            <!-- Available Time -->
            <div>
                <InputLabel for="available_time" value="Available Time" />
                <TextInput
                    id="available_time"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.available_time"
                    required
                />
                <InputError class="mt-2" :message="form.errors.available_time" />
            </div>

            <!-- Profile Picture -->
            <div>
                <InputLabel for="profile_picture" value="Profile Picture" />
                <input
                    type="file"
                    id="profile_picture"
                    class="mt-1 block w-full"
                    @change="event => form.profile_picture = event.target.files[0]"
                />
                <InputError class="mt-2" :message="form.errors.profile_picture" />
            </div>

            <!-- CB Number (Auto-filled, not editable) -->
            <div>
                <InputLabel for="cb_number" value="CB Number" />
                <TextInput
                    id="cb_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.cb_number"
                    disabled
                />
                <InputError class="mt-2" :message="form.errors.cb_number" />
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Create Profile</PrimaryButton>
            </div>
        </form>
    </section>
</template>
