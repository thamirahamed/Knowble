<script setup>
 import AdminLayout from "@/Layouts/AdminLayout.vue";
 import UserCard from "@/Components/UserCard.vue";

 const props = defineProps(
     {
         users: {
             type: Array,
             required: true,
         },
     }
 )

</script>
<template>
    <AdminLayout>
        <div class="m-12">
            <UserCard
                v-for="user in props.users"
                :key="user.id"
                :username="user.name"
                :user_id="user.id"
                :profile="user.profile"
                :isTutor="user.tutor"
            />
        </div>


    </AdminLayout>

</template>
