<template>
    <button
      class="group inline-flex items-center justify-center whitespace-nowrap rounded-md bg-red-500 px-5 py-2.5 text-center font-semibold tracking-wide text-white relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:translate-x-[-150%] after:translate-y-[150%] after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-red-700 after:transition-transform after:duration-550  hover:after:translate-x-[0%] hover:after:translate-y-[0%]"
    >
        <!-- Icon on the Left (if provided) -->
        <div
            v-if="icon && iconPlacement === 'left'"
            class="w-0 pr-0 opacity-0 translate-x-[-100%] transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:mr-2 group-hover:-ml-1 group-hover:opacity-100"
        >
            <slot name="icon" />
        </div>
    
        <!-- Button Text -->
        <span>
            <slot />
        </span>
    
        <!-- Icon on the Right (if provided) -->
        <div
            v-if="icon && iconPlacement === 'right'"
            class="w-0 pl-0 opacity-0 translate-x-[100%] transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-2 group-hover:opacity-100"
        >
            <slot name="icon" />
        </div>
    </button>
</template>
  
<script>
    export default {
        name: "DangerButton",
        props: {
                icon: {
                type: Boolean,
                default: false,
            },
            iconPlacement: {
                type: String,
                default: "right", // Default placement of the icon: "left" or "right"
                validator: (value) => ["left", "right"].includes(value),
            },
        },
    };
</script>
  
  