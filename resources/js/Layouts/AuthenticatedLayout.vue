<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";

const tutor = usePage().props.tutor;
const showingNavigationDropdown = ref(false);
const confirmingTutorRequest = ref(false);

const confirmTutorRequest = () => {
    confirmingTutorRequest.value = true;
};
const TutorRequest = () => {
    router.visit(route("admin.tutor.request"))
    closeModal()
    setTimeout(() => {
        location.reload();
    }, 3000);

};

const closeModal = () => {
    confirmingTutorRequest.value = false;
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="w-full px-4 sm:px-6 lg:px-8 flex justify-center">
                    <div class=" container flex h-16 justify-between">
                        <div class="flex align-baseline">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Home
                                </NavLink>
                                <NavLink
                                    :href="route('chatpage')"
                                    :active="route().current('chatpage')"
                                >
                                    Chat
                                </NavLink>
                                <template v-if="tutor === 'approved'">
                                    <NavLink
                                        :href="route('tutor.dashboard')"
                                        :active="route().current('tutor.dashboard')"
                                    >
                                        Tutor Dashboard
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex align-baseline rounded-md border border-transparent bg-white px-3 py-2 text-lg font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ usePage().props.auth.name ? usePage().props.auth.name : usePage().props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            Profile
                                        </DropdownLink>

                                        <!-- Tutor Status -->
                                        <template v-if="tutor === null">
                                            <button class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none" @click="confirmTutorRequest">
                                                Request to be a Tutor
                                            </button>
                                        </template>

                                        <template v-if="tutor === 'pending'">
                                            <DropdownLink class="pointer-events-none !text-gray-400">
                                                Tutor Request Sent
                                            </DropdownLink>
                                        </template>

                                        <template v-else-if="tutor === 'rejected'">
                                            <DropdownLink class="pointer-events-none !text-red-400">
                                                Tutor Request Rejected
                                            </DropdownLink>
                                        </template>

                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tutor Request Confirmation Modak -->
                <Modal :show="confirmingTutorRequest" @close="closeModal">
                    <div class="p-6">
                        <h2 class="text-xl font-medium text-gray-900">
                            Are you sure you want to become a Tutor?
                        </h2>

                        <p class="mt-1 text-md text-gray-600">
                            To qualify as a tutor, you must be beyond Foundation Sem 1 or Degree Year 1 - Sem 1, with a minimum score of 60% per module.
                        </p>

                        <div class="mt-6 flex justify-end gap-4">
                            <SecondaryButton @click="closeModal">
                                Cancel
                            </SecondaryButton>

                            <PrimaryButton
                                @click="TutorRequest"
                            >
                                Send Request
                            </PrimaryButton>
                        </div>
                    </div>
                </Modal>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Home
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('chatpage')"
                            :active="route().current('chatpage')"
                        >
                            Chat
                        </ResponsiveNavLink>
                        <template v-if="tutor === 'approved'">
                            <ResponsiveNavLink
                                :href="route('tutor.dashboard')"
                                :active="route().current('tutor.dashboard')"
                            >
                                Tutor Dashboard
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{usePage().props.auth.name ? usePage().props.auth.name : usePage().props.auth.user.name}}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ usePage().props.auth.email ? usePage().props.auth.email : usePage().props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <!-- Tutor Status -->
                            <template v-if="tutor === null">
                                <button class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out" @click="confirmTutorRequest">
                                    Request to be a Tutor
                                </button>
                            </template>

                            <template v-if="tutor === 'pending'">
                                <ResponsiveNavLink class="pointer-events-none !text-gray-400">
                                    Tutor Request Sent
                                </ResponsiveNavLink>
                            </template>

                            <template v-else-if="tutor === 'rejected'">
                                <ResponsiveNavLink class="pointer-events-none !text-red-400">
                                    Tutor Request Rejected
                                </ResponsiveNavLink>
                            </template>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
