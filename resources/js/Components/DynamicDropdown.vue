<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    id: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: [String, Number, null],
        default: null,
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

// Reflect the modelValue in the local state
const selectedValue = ref(props.modelValue);

// Watch for changes in modelValue and update local state
watch(
    () => props.modelValue,
    (newValue) => {
        selectedValue.value = newValue;
    }
);

// Emit changes to the parent
watch(selectedValue, (newValue) => {
    emit("update:modelValue", newValue);
});
</script>

<template>
    <div>
        <!-- Label -->
        <!-- <InputLabel :for="id" :value="label" /> -->

        <!-- Dropdown -->
        <select
            :id="id"
            v-model="selectedValue"
            :class="[
                'cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500',
                selectedValue === '' ? 'text-gray-500' : 'text-black'
            ]"
        >
            <option value="" >Select {{ label }}</option>
            <option
                v-for="option in options"
                :key="option.id"
                :value="option.id"
                class="text-black"
                :id="label + '-' + option.id"
            >
                {{ option.level_name || option.degree_name || option.school_name || option.semester_name || option.module_name}}
            </option>
        </select>


        <!-- Error -->
        <InputError class="mt-2" :message="error" />
    </div>
</template>
