<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div class="flex flex-col w-full gap-3">
            <h1 class="text-7xl tracking-wide text-gray-900 font-semibold ">Login</h1>
        </div>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>



        <form @submit.prevent="submit" class=" text-xl text-primary">
            <div class="my-6">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter email here"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="my-6">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter password here"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="-mt-2 flex justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-gray-700"
                        >Remember me</span
                    >
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-gray-700 underline hover:text-accentdark"
                >
                    Forgot your password?
                </Link>
            </div>

            <div class="mt-8 flex flex-col flex-grow-1 items-center gap-6">
                <PrimaryButton
                    class="w-full text-2xl"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Login
                </PrimaryButton>

                <span class="text-gray-900"> Don't have an account, 
                <Link
                    :href="route('register')"
                    class="text-gray-700 underline hover:text-accentdark"
                >
                    Sign Up
                </Link>
                </span>
            </div>
        </form>
    </GuestLayout>
</template>
