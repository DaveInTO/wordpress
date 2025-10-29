<script setup lang="ts">
	import { ref } from 'vue'
	import { useContactState } from '../composables/useContactState'

	const props = defineProps<{ audience: string | null }>()
	const { success } = useContactState()

	const form = ref<Record<string, any>>({})
	const loading = ref(false)
	const error = ref('')

	const submit = async () => {
	  loading.value = true
	    error.value = ''
	      try {
		  const res = await fetch('/wp-json/contact/v1/send', {
			method: 'POST',
			      headers: { 'Content-Type': 'application/json' },
				    body: JSON.stringify({ audience: props.audience, ...form.value }),
					})
					    if (!res.ok) throw new Error('Failed to send')
						success.value = true
						  } catch (err: any) {
						      error.value = err.message
							} finally {
							    loading.value = false
							      }
							      }
</script>

<template>
	<form @submit.prevent="submit" class="space-y-4">
		<h3 class="text-xl font-semibold text-gray-800 mb-2">
			{{ props.audience ? props.audience.replace(/^\w/, c => c.toUpperCase()) : 'Select an audience' }}
		</h3>

		<!-- Conditionally render fields -->
		<div v-if="props.audience === 'schools'">
			<input v-model="form.organization" placeholder="Organization Name" class="form-input" />
			<input v-model="form.email" type="email" placeholder="Email" class="form-input" />
			<textarea v-model="form.message" placeholder="Tell us about your needs" class="form-input"></textarea>
		</div>

		<div v-else-if="props.audience === 'careers'">
			<a href="/careers" class="text-indigo-600 underline">View Open Roles</a>
		</div>

		<div v-else>
			<textarea v-model="form.message" placeholder="Your Message" class="form-input"></textarea>
		</div>

		<button
				type="submit"
				:disabled="loading"
				class="w-full bg-amber-600 text-white py-3 rounded-xl hover:bg-amber-700 transition"
				>
				{{ loading ? 'Sending...' : 'Send Message' }}
		</button>

			<p v-if="error" class="text-red-600">{{ error }}</p>
	</form>
</template>
