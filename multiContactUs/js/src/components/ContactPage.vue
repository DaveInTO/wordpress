<script setup lang="ts">
import HeroSection from './HeroSection.vue'
import AudienceTiles from './AudienceTiles.vue'
import ContactForm from './ContactForm.vue'
import SuccessScreen from './SuccessScreen.vue'
import { useContactState } from '../composables/useContactState'

const { selectedAudience, success } = useContactState()
</script>

<template>
  <main class="bg-gradient-to-b from-amber-50 to-white min-h-screen">
    <HeroSection />

    <section class="max-w-6xl mx-auto px-4 py-10">
      <AudienceTiles />
    </section>

    <section class="max-w-3xl mx-auto px-4 py-10">
      <Transition name="fade">
        <SuccessScreen v-if="success" />
        <ContactForm v-else :audience="selectedAudience" />
      </Transition>
    </section>
  </main>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

