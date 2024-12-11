<script setup>
import { onMounted, ref } from "vue";

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border-gray-300 text-lg shadow-sm hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
        v-model="model"
        ref="input"
    />
</template>
