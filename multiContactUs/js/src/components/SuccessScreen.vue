<template>
  <div
    role="status"
    aria-live="polite"
    class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow-md text-center"
  >
    <!-- Graphic -->
    <div class="mx-auto w-28 h-28 mb-4 flex items-center justify-center">
      <svg viewBox="0 0 120 120" class="w-28 h-28" aria-hidden="true" focusable="false">
        <circle cx="60" cy="60" r="54" stroke="transparent" stroke-width="6" fill="#FFFBEB" />
        <circle cx="60" cy="60" r="54" stroke="rgba(245,158,11,0.12)" stroke-width="6" fill="none" />
        <path
          d="M35 62 L52 80 L88 44"
          stroke="rgba(6,95,70,1)"
          stroke-width="6"
          stroke-linecap="round"
          stroke-linejoin="round"
          fill="none"
          class="checkstroke"
        />
      </svg>
    </div>

    <!-- Headline -->
    <h2 class="text-2xl font-extrabold text-amber-800 mb-2">
      Thanks! A real live Yummy human will write back by tomorrow.
    </h2>

    <!-- Subcopy -->
    <p class="text-slate-700 mb-4">
      We routed your message to the <span class="font-semibold">{{ audienceLabel }}</span> team.
      If this is urgent, call <a href="tel:4165325250" class="underline">416-532-5250</a> or email
      <a :href="mailtoHref" class="underline">hello@yummy.com</a>.
    </p>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-4">
      <a
        :href="bookCallHref"
        target="_blank"
        rel="noopener"
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-amber-600 text-white hover:bg-amber-700 transition"
      >
        Book a 20-min call
      </a>

      <button
        @click="startAnother"
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl border border-amber-200 bg-white hover:bg-amber-50"
      >
        Send another message
      </button>

      <a
        v-if="selectedAudience === 'careers'"
        href="/careers"
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl border border-amber-200 bg-white hover:bg-amber-50"
      >
        View Open Roles
      </a>
    </div>

    <!-- Contact & Hours -->
    <div class="mt-6 text-sm text-gray-600 leading-relaxed">
      <p class="font-medium">Contact & Hours</p>
      <p>hello@… · <a href="tel:4165325250" class="underline">416-532-5250</a></p>
      <p>Day-of Client Support: Mon–Fri 8:30–17:00 · Receiving (Plant): Mon–Fri 8:00–15:00</p>
      <p class="mt-2">Production Plant (HQ) — 25 Sheffield St., Toronto. Yummy Lab @ Dupont — by appointment.</p>
    </div>

    <!-- Newsletter opt-in (simple stub) -->
    <form @submit.prevent="subscribe" class="mt-5 flex items-center justify-center gap-2">
      <label for="newsletter" class="sr-only">Snacktime updates email</label>
      <input
        id="newsletter"
        v-model="newsletterEmail"
        type="email"
        placeholder="Snacktime updates — your email"
        class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-300"
        aria-label="Newsletter email"
      />
      <button type="submit" class="px-4 py-2 rounded-full bg-amber-600 text-white">Subscribe</button>
    </form>
    <p v-if="subscribed" class="text-green-600 mt-2">Thanks — you’re on the list!</p>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useContactState } from '../composables/useContactState'

const { selectedAudience, setAudience, success } = useContactState()

// Placeholder links — replace with your actual Calendly / mailto
const bookCallHref = 'https://calendly.com/your-team/20min' // replace me
const mailtoHref = 'mailto:hello@yummy.com'

const audienceLabel = computed(() => {
  const map: Record<string, string> = {
    schools: 'Childcare & Schools',
    camps: 'Camps & Programs',
    facilities: 'Facilities / RFPs',
    careers: 'Careers',
    media: 'Media / Vendors',
    general: 'General inquiries',
  }
  return selectedAudience.value ? (map[selectedAudience.value] || selectedAudience.value) : 'right team'
})

function startAnother() {
  // resets the UI so user can pick a new tile / resubmit
  setAudience(null)
  success.value = false
}

// Tiny newsletter stub (replace with real API call)
const newsletterEmail = ref('')
const subscribed = ref(false)
function subscribe() {
  if (!newsletterEmail.value) return
  // Integrate with your newsletter endpoint here
  subscribed.value = true
  newsletterEmail.value = ''
}
</script>

<style scoped>
/* simple check-mark stroke draw animation */
.checkstroke {
  stroke-dasharray: 120;
  stroke-dashoffset: 120;
  animation: draw 0.6s cubic-bezier(.2,.8,.2,1) forwards 0.15s;
}
@keyframes draw {
  to {
    stroke-dashoffset: 0;
  }
}
</style>

