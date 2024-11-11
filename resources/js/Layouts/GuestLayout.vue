<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue'
import { Gradient } from './Gradient';

onMounted(() => {
  const gradient = new Gradient()
  gradient.initGradient('#gradient-canvas')
})
</script>

<template>
    <div
        class="flex max-h-screen bg-transparent flex-col items-center lg:flex-row"
    >
        
        <div
            class="relative flex items-center w-full h-screen overflow-hidden lg:w-3/5 bg-transparent"
        >
            <div class="absolute flex flex-col w-full h-full text-white pl-12 pr-[13%] bg-transparent md:pl-20 md:pr-[20%]">
                <div class="flex items-start pt-8">
                    <img class="w-12 h-12" src="./images/logo-gr.png" alt="logo">
                </div>
                <div class="flex flex-col justify-center flex-grow -mt-8 bg-gradient-to-b from-white via-gray-100 to-gray-400 bg-clip-text text-transparent">
                    <h1 class="h-fit text-5xl tracking-wide md:text-7xl 2xl:text-8xl font-normal font-mainh">Welcome to,</h1>
                    <h1 class="h-fit text-6xl tracking-wider md:text-8xl xl:text-9xl 2xl:text-[9rem] font-bold font-mainh">Knowble</h1>
                    <h3 class="text-xl md:text-1xl xl:text-2xl font-subh tracking-wide font-light">Empowering APIIT students through peer-led support, sharing knowledge, and fostering community</h3>
                </div>
            </div>
            <canvas id="gradient-canvas" data-transition-in class=" flex w-full h-full gradient-bg lg:h-full lg:w-auto"></canvas> 
        </div>
        
        <div
            class="flex flex-col justify-center w-full h-screen overflow-hidden bg-white px-24 py-4 lg:w-2/5"
        >
            <slot />
        </div>
        
    </div>
</template>
