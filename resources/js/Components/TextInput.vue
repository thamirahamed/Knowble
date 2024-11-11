<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md text-xl border-gray-300 shadow-sm focus:border-transparent focus:ring-2 focus:outline-none focus:ring-accent hover:border-accent"
        v-model="model"
        ref="input"
    />
</template>