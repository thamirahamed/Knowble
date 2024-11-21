<script setup>
import { Link } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { Gradient } from "../../../public/Gradient";

onMounted(() => {
    const gradient = new Gradient();
    gradient.initGradient("#gradient-canvas");
});
</script>

<template>
    <div
        class="flex max-h-screen flex-col items-center bg-transparent lg:flex-row"
    >
        <div
            class="relative flex h-screen w-full items-center overflow-hidden bg-transparent lg:w-3/5 pointer-events-none select-none"
        >
            <div
                class="absolute flex h-full w-full flex-col bg-transparent pl-12 pr-[12%] text-white md:pl-20 md:pr-[10%]"
            >
                <div class="flex items-start pt-8">
                    <img
                        class="h-12 w-12"
                        src="/images/logo-gr.webp"
                        alt="logo"
                    />
                </div>
                <div
                    class="-mt-8 flex flex-grow flex-col justify-center bg-gradient-to-b from-white via-gray-100 to-gray-400 bg-clip-text text-transparent"
                >
                    <h1
                        class="h-fit font-mainh text-5xl font-normal tracking-wide md:text-7xl 2xl:text-8xl"
                    >
                        Welcome to,
                    </h1>
                    <h1
                        class="h-fit font-mainh text-6xl font-bold tracking-wider md:text-8xl xl:text-9xl 2xl:text-[8.5rem]"
                    >
                        Knowble
                    </h1>
                    <h3
                        class="md:text-1xl font-subh text-xl font-light tracking-wide xl:text-2xl"
                    >
                        Empowering APIIT students through peer-led support,
                        sharing knowledge, and fostering community
                    </h3>
                </div>
            </div>
            <canvas
                id="gradient-canvas"
                data-transition-in
                class="gradient-bg flex h-full w-full lg:h-full lg:w-auto"
            ></canvas>
        </div>

        <div
            class="flex h-screen w-full flex-col justify-center overflow-hidden bg-white px-24 py-4 lg:w-2/5"
        >
            <slot />
        </div>
    </div>
</template>
