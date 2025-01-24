<script setup>
 import AdminLayout from "@/Layouts/AdminLayout.vue";
 import UserCard from "@/Components/UserCard.vue";
 import { computed, ref, watch } from "vue";

 const props = defineProps(
     {
         users: {
             type: Array,
             required: true,
         },
     }
 )

 const search = ref("");

 const filteredUsers = computed(() => {
     return props.users.filter((user) => {
         const searchTerm = search.value.toLowerCase();
         return (
             user.name.toLowerCase().includes(searchTerm) ||
             user.profile.cb_number?.toLowerCase().includes(searchTerm)
         );
     });
 });
</script>
<template>
    <AdminLayout>
        <!-- Search bar -->
        <div class="m-12">
            <input
                type="text"
                class="w-full border-2 border-gray-300 bg-white h-10 px-5 pr-16 rounded-lg text-sm focus:outline-none"
                placeholder="Search for users..."
                v-model="search"
            />
        </div>
        <div class="m-12">
            <UserCard
                v-for="user in filteredUsers"
                :key="user.id"
                :username="user.name"
                :user_id="user.id"
                :profile="user.profile"
                :isTutor="user.tutor"
            />
        </div>


    </AdminLayout>

</template>
