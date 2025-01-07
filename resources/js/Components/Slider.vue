<script setup>
import { ref, watch } from "vue";

// Props to accept min, max, and default value
const props = defineProps({
    min: {
        type: Number,
        required: true,
        default: 0,
    },
    max: {
        type: Number,
        required: true,
        default: 100,
    },
    value: {
        type: Number,
        default: 0, // Optional: Defaults to midpoint if not provided
    },
    sLabel: {
        type: String,
        default: "Value",
    }
});

// Emit changes to the parent
const emit = defineEmits(["update:modelValue"]);

// Reactive value for the slider
const currentValue = ref(props.value);

watch(currentValue, (newValue) => {
    emit("update:modelValue", newValue);
});
</script>

<template>
    <div class="flex flex-col space-y-2 w-full">
      <!-- Label -->
      <label class="text-gray-700 text-base">
        {{ sLabel }} : <span class="text-accent">{{ currentValue }}</span>
      </label>
  
      <!-- Slider -->
      <input
        type="range"
        :min="min"
        :max="max"
        v-model="currentValue"
        id="slider"
        class="w-full h-2 bg-secondary/40 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-0 slider-thumb"
      />
  
      <!-- Min and Max Labels -->
      <div class="flex justify-between w-full text-gray-500 px-1">
        <span>{{ min }}</span>
        <span>{{ max }}</span>
      </div>
    </div>
</template>
  
<style scoped>
/* Custom slider circle (thumb) */
input[type="range"].slider-thumb::-webkit-slider-thumb {
  appearance: none;
  width: 16px;
  height: 16px;
  background-color: #1b8f67; /* Change this color for the circle */
  border-radius: 50%;
  border: 2px solid #ffffff; /* Optional: Add a border */
  cursor: pointer;
  transition: background-color 0.2s ease;
}

input[type="range"].slider-thumb::-moz-range-thumb {
  width: 16px;
  height: 16px;
  background-color: #1b8f67; /* Change this color for Firefox */
  border-radius: 50%;
  border: 2px solid #ffffff;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

input[type="range"].slider-thumb::-ms-thumb {
  width: 16px;
  height: 16px;
  background-color: #1b8f67; /* Change this color for Edge */
  border-radius: 50%;
  border: 2px solid #ffffff;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

/* Optional: Hover effect for the circle */
input[type="range"].slider-thumb:hover::-webkit-slider-thumb {
  background-color: #136b4d;
}

input[type="range"].slider-thumb:hover::-moz-range-thumb {
  background-color: #136b4d;
}

input[type="range"].slider-thumb:hover::-ms-thumb {
  background-color: #136b4d;
}
</style>
  