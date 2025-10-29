<script setup lang="ts">
import { ref } from 'vue'
import { useSubmitForm } from '@/composables/useSubmitForm'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

const form = ref({
  name: '',
  email: '',
  message: '',
})

const submitted = ref(false)
const { submitForm, loading, error } = useSubmitForm()

const handleSubmit = async () => {
  submitted.value = false
  try {
    await submitForm(form.value)
    submitted.value = true
    form.value = { name: '', email: '', message: '' }
  } catch (err) {
    console.error(err)
  }
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="yc:space-y-5 yc:w-full">
    <div>
      <Label for="name" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Full Name</Label>
      <Input
        id="name"
        v-model="form.name"
        type="text"
        required
        placeholder="John Doe"
        class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm"
      />
    </div>

    <div>
      <Label for="email" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Email Address</Label>
      <Input
        id="email"
        v-model="form.email"
        type="email"
        required
        placeholder="john@example.com"
        class="yc:w-full yc:rounded-xl yc:border yc:border-slate-300 yc:focus:ring-2 yc:focus:ring-red-500 yc:focus:border-red-500 yc:p-3 yc:outline-none yc:transition"
      />
    </div>

    <div>
      <Label for="message" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Message</Label>
      <Textarea
        id="message"
        v-model="form.message"
        required
        rows="5"
        placeholder="Your message here..."
        class="yc:w-full yc:rounded-xl yc:border yc:border-slate-300 yc:focus:ring-2 yc:focus:ring-red-500 yc:focus:border-red-500 yc:p-3 yc:outline-none yc:resize-none yc:transition"
      />
    </div>

    <div class="yc:pt-3 yc:mt-3">
      <button
        type="submit"
        :disabled="loading"
        class="yc:w-full yc:bg-red-600 yc:hover:bg-red-700 yc:text-white yc:font-semibold yc:py-3 yc:rounded-xl yc:shadow-md yc:transition yc:active:scale-[.98]"
      >
        {{ loading ? 'Sending...' : 'Send Message' }}
      </button>
    </div>

    <p v-if="error" class="yc:text-red-600 yc:mt-2">{{ error }}</p>
    <p v-if="submitted" class="yc:text-green-600 yc:text-center yc:mt-4 yc:font-medium yc:animate-fade-in">
      ✅ Your message has been sent successfully!
    </p>
  </form>
</template>

