import { ref } from 'vue'

const selectedAudience = ref<string | null>(null)
const success = ref(false)

export function useContactState() {
	const setAudience = (audience: string | null) => {
		selectedAudience.value = audience
		success.value = false
	}
	return { selectedAudience, setAudience, success }
}

