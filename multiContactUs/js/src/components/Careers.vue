<script setup lang="ts">
import { ref } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

const form = ref({
  atsLink: '',
  description: ''
})

const submitForm = () => {
  console.log('Careers form:', form.value)
  // handle API submission or CMS update here
}
</script>

<template>
  <form @submit.prevent="submitForm" class="yc:w-full yc:mx-auto yc:bg-white yc:p-8 yc:space-y-6">

    <!-- ATS or Careers Link -->
    <div>
      <Label for="atsLink" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
        Link to ATS or “View Open Roles”
      </Label>
      <Input
        type="url"
        id="atsLink"
        v-model="form.atsLink"
        placeholder="https://jobs.example.com (or leave blank for placeholder)"
        class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition-shadow yc:duration-200 yc:shadow-sm"
      />
    </div>

    <!-- Optional Description -->
    <div>
      <Label for="description" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
        Optional Message
      </Label>
      <Textarea
        id="description"
        v-model="form.description"
        placeholder="e.g. We’re always looking for passionate people to join our team."
        rows="3"
        class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition-shadow yc:duration-200 yc:shadow-sm"
      />
    </div>

    <!-- Submit -->
    <button
      type="submit"
      class="yc:w-full yc:bg-red-500 yc:hover:bg-red-600 yc:text-white yc:font-semibold yc:py-3 yc:rounded-xl yc:shadow-md yc:transition yc:duration-200"
    >
      Submit
    </button>
  </form>
</template>

