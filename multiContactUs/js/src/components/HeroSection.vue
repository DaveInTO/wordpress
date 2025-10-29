<!-- src/components/HeroSection.vue -->
<template>
  <section class="relative w-full aspect-[16/9] overflow-hidden rounded-2xl shadow-lg">
    <!-- Background Image -->
    <img
      src="@/assets/hero.png"
      alt="Yummy hero image"
      class="absolute inset-0 w-full h-full object-cover"
    />

    <!-- Overlay for text readability -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Text + CTA Content -->
    <div class="relative z-10 flex flex-col justify-center items-center text-center text-white h-full px-6">
      <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow">
        Let’s feed tiny legends.
      </h1>
      <p class="text-lg md:text-xl max-w-2xl mb-6 drop-shadow">
        Tell us who you are and what you need—we’ll match you with the right Yummy human. We reply within 1 business day.
      </p>

      <div class="flex flex-wrap justify-center gap-4">
        <button
          class="bg-yellow-400 text-black font-semibold py-2 px-6 rounded-xl shadow hover:bg-yellow-300 transition"
        >
          Get a Quote
        </button>
        <button
          class="bg-white text-black font-semibold py-2 px-6 rounded-xl shadow hover:bg-gray-100 transition"
        >
          Book a Call
        </button>
        <button
          class="bg-pink-500 text-white font-semibold py-2 px-6 rounded-xl shadow hover:bg-pink-400 transition"
        >
          Ask a Question
        </button>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
// No logic needed here yet, but if you later want props for text or links, 
// you can easily add them here.
</script>

