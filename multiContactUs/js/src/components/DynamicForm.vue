<script setup lang="ts">
import { reactive, ref, computed, watch } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import { Label } from '@/components/ui/label'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import type { FieldDefinition } from '@/types'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import { useSubmitForm } from '@/composables/useSubmitForm'

const props = defineProps<{
	fields: FieldDefinition[]
	title: string
	submitLabel?: string
	successMessage?: string
}>()

const form = ref<Record<string, any>>({})
const submitted = ref(false)
const missingFields = ref<string[]>([])
const uploadProgress = reactive<Record<string, number[]>>({})

const { submitForm, loading, error } = useSubmitForm()

const INPUT_TYPES = ['text', 'email', 'number', 'url', 'date', 'tel', 'datetime-local']

const fieldClass =
	'yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm'

const defaultFor = (field: FieldDefinition) => {
	switch (field.fieldtype) {
		case 'checkbox':
			return false
		case 'checkbox-group':
		case 'file':
			return []
		default:
			return ''
	}
}

const initModel = () => {
	const next: Record<string, any> = {}
	props.fields.forEach((field) => {
		if (field.fieldtype === 'section') return
		next[field.name] = defaultFor(field)
	})
	form.value = next
}
initModel()

const matchesCondition = (field: FieldDefinition) => {
	if (!field.showIf) return true
	const current = form.value[field.showIf.field]
	const expected = Array.isArray(field.showIf.equals) ? field.showIf.equals : [field.showIf.equals]
	return Array.isArray(current)
		? current.some((v) => expected.includes(v))
		: expected.includes(current)
}

const visibleFields = computed(() => props.fields.filter(matchesCondition))

// Clear any field that has just been hidden, so a stale answer is never submitted.
watch(visibleFields, (next, previous) => {
	const stillVisible = new Set(next.map((f) => f.name))
	previous
		.filter((f) => f.fieldtype !== 'section' && !stillVisible.has(f.name))
		.forEach((f) => {
			form.value[f.name] = defaultFor(f)
		})
})

const isEmpty = (value: any) => {
	if (Array.isArray(value)) return value.length === 0
	if (typeof value === 'boolean') return value === false
	return value === null || value === undefined || String(value).trim() === ''
}

const validate = () => {
	missingFields.value = visibleFields.value
		.filter((f) => f.fieldtype !== 'section' && f.required && isEmpty(form.value[f.name]))
		.map((f) => f.label)
	return missingFields.value.length === 0
}

const toggleInGroup = (fieldName: string, value: string | number, checked: boolean) => {
	const current: Array<string | number> = form.value[fieldName] ?? []
	form.value[fieldName] = checked
		? [...current, value]
		: current.filter((v) => v !== value)
}

/**
 * Build the submission payload.
 * Option-backed fields are sent as their human labels so the sales email reads
 * like the form; checkbox groups join in option order, not click order.
 */
const serialize = () => {
	const payload: Record<string, any> = {}
	const labels: Record<string, string> = {}

	visibleFields.value.forEach((field) => {
		if (field.fieldtype === 'section') return
		const value = form.value[field.name]
		const options = field.options ?? []

		if (field.fieldtype === 'checkbox-group') {
			payload[field.name] = options
				.filter((o) => (value as Array<string | number>).includes(o.value))
				.map((o) => o.label)
				.join(field.joinWith ?? ', ')
		} else if (field.fieldtype === 'dropdown' || field.fieldtype === 'radio') {
			payload[field.name] =
				options.find((o) => String(o.value) === String(value))?.label ?? value
		} else if (field.fieldtype === 'checkbox') {
			payload[field.name] = value ? 'Yes' : 'No'
		} else {
			payload[field.name] = value
		}

		labels[field.name] = field.label
	})

	payload.formType = props.title
	// Stringified so it survives both the JSON and multipart submission paths.
	payload._labels = JSON.stringify(labels)
	return payload
}

const handleSubmit = async () => {
	if (!validate()) {
		window.scrollTo({ top: 0, behavior: 'smooth' })
		return
	}
	try {
		await submitForm(serialize())
		submitted.value = true
		initModel()
		window.scrollTo({ top: 0, behavior: 'smooth' })
	} catch (err) {
		console.error(err)
	}
}

const onFileChange = (event: Event, fieldName: string, multiple?: boolean) => {
	const target = event.target as HTMLInputElement
	if (!target.files) return

	const files = multiple ? Array.from(target.files) : [target.files[0]]
	form.value[fieldName] = files
	uploadProgress[fieldName] = files.map(() => 0)
}
</script>

<template>
	<div
			v-if="submitted"
			role="status"
			aria-live="polite"
			class="yc:w-full yc:py-10 yc:px-6 yc:text-center"
			>
			<div class="yc:mx-auto yc:mb-5 yc:flex yc:h-16 yc:w-16 yc:items-center yc:justify-center yc:rounded-full yc:bg-red-50">
				<svg viewBox="0 0 48 48" class="yc:h-9 yc:w-9" aria-hidden="true">
				<path d="M14 25 L21 32 L34 17" fill="none" stroke="#dc2626" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</div>
			<p class="yc:text-lg yc:text-slate-700 yc:max-w-xl yc:mx-auto">
			{{ submitted && props.successMessage ? props.successMessage : 'Thank you for contacting Yummy Catering. A member of our team will be in touch shortly.' }}
			</p>
	</div>

	<form v-else @submit.prevent="handleSubmit" novalidate class="yc:space-y-5 yc:w-full">
		<Alert v-if="missingFields.length" variant="destructive" class="yc:mb-4">
		<AlertTitle>Please complete the required fields</AlertTitle>
		<AlertDescription>
		<ul class="yc:ml-4 yc:list-disc">
			<li v-for="field in missingFields" :key="field">{{ field }}</li>
		</ul>
		</AlertDescription>
		</Alert>

		<template v-for="field in visibleFields" :key="field.name">
			<!-- Section heading -->
			<h3
					v-if="field.fieldtype === 'section'"
					class="yc:text-sm yc:font-bold yc:uppercase yc:tracking-wide yc:text-red-600 yc:pt-6 yc:pb-1 yc:border-b yc:border-slate-200"
					>
					{{ field.label }}
			</h3>

			<div v-else class="yc:space-y-1">
				<Label
						v-if="field.fieldtype !== 'checkbox'"
						:for="field.name"
						class="yc:block yc:text-sm yc:font-medium yc:text-slate-700"
						>
						{{ field.label }}
						<span v-if="field.required" class="yc:text-red-500">*</span>
				</Label>

				<!-- Text, Email, Tel, Number, URL, Date -->
				<Input
						v-if="INPUT_TYPES.includes(field.fieldtype)"
						v-model="form[field.name]"
						:id="field.name"
						:type="field.fieldtype"
						:autocomplete="field.autocomplete"
						:placeholder="field.placeholder"
						:min="field.min"
						:max="field.max"
						:class="fieldClass"
						/>

				<Textarea
						v-else-if="field.fieldtype === 'textarea'"
						v-model="form[field.name]"
						:id="field.name"
						:rows="field.rows ?? 4"
						:placeholder="field.placeholder"
						class="yc:w-full yc:rounded-xl yc:border yc:border-gray-300 yc:focus:ring-1 yc:focus:ring-red-500 yc:focus:border-red-500 yc:p-3 yc:outline-none yc:resize-y yc:transition yc:shadow-sm"
						/>

					<!-- Single checkbox -->
					<div v-else-if="field.fieldtype === 'checkbox'" class="yc:flex yc:items-center yc:gap-2">
						<Checkbox v-model="form[field.name]" :id="field.name" />
						<Label :for="field.name">
						{{ field.label }}
						<span v-if="field.required" class="yc:text-red-500">*</span>
						</Label>
					</div>

					<!-- Checkbox group (multi-select) -->
					<div
							v-else-if="field.fieldtype === 'checkbox-group'"
							role="group"
							:aria-labelledby="field.name"
							class="yc:space-y-2 yc:pt-1"
							>
							<label
									v-for="option in field.options"
									:key="option.value"
									class="yc:flex yc:items-start yc:gap-3 yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:cursor-pointer yc:hover:border-red-400 yc:hover:bg-red-50/40 yc:transition yc:duration-150"
									>
									<input
											type="checkbox"
											:name="field.name"
											:value="option.value"
											:checked="form[field.name].includes(option.value)"
											@change="toggleInGroup(field.name, option.value, ($event.target as HTMLInputElement).checked)"
											class="yc:mt-0.5 yc:h-5 yc:w-5 yc:shrink-0 yc:accent-red-600 yc:cursor-pointer"
											/>
									<span class="yc:text-slate-700 yc:leading-snug">{{ option.label }}</span>
							</label>
					</div>

					<!-- Radio group (single select) -->
					<div
							v-else-if="field.fieldtype === 'radio'"
							role="radiogroup"
							:aria-labelledby="field.name"
							class="yc:space-y-2 yc:pt-1"
							>
							<label
									v-for="option in field.options"
									:key="option.value"
									class="yc:flex yc:items-start yc:gap-3 yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:cursor-pointer yc:hover:border-red-400 yc:hover:bg-red-50/40 yc:transition yc:duration-150"
									>
									<input
											type="radio"
											:name="field.name"
											:value="option.value"
											v-model="form[field.name]"
											class="yc:mt-0.5 yc:h-5 yc:w-5 yc:shrink-0 yc:accent-red-600 yc:cursor-pointer"
											/>
									<span class="yc:text-slate-700 yc:leading-snug">{{ option.label }}</span>
							</label>
					</div>

					<!-- Dropdown -->
					<div v-else-if="field.fieldtype === 'dropdown'" class="yc:relative yc:w-full">
						<select
								v-model="form[field.name]"
								:id="field.name"
								class="yc:w-full yc:p-3 yc:pr-10 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition yc:duration-200 yc:shadow-sm yc:appearance-none yc:bg-white yc:text-slate-700 yc:leading-normal yc:min-h-[48px]"
								>
								<option value="">{{ field.placeholder ?? 'Select an option' }}</option>
								<option v-for="option in field.options" :key="option.value" :value="option.value">
								{{ option.label }}
								</option>
						</select>
						<ChevronDown
								class="yc:pointer-events-none yc:absolute yc:right-3 yc:top-1/2 yc:-translate-y-1/2 yc:h-5 yc:w-5 yc:text-slate-400"
								aria-hidden="true"
								/>
					</div>

					<!-- File input -->
					<div v-else-if="field.fieldtype === 'file'" class="yc:mb-4">
						<Input
								type="file"
								:id="field.name"
								:multiple="field.multiple"
								@change="onFileChange($event, field.name, field.multiple)"
								:class="fieldClass"
								/>
						<ul v-if="form[field.name] && form[field.name].length" class="yc:mt-2 yc:list-disc yc:pl-5 yc:text-sm yc:text-gray-600">
							<li v-for="(file, index) in form[field.name]" :key="file.name" class="yc:mb-1">
								{{ file.name }}
								<div v-if="uploadProgress[field.name]?.[index] !== undefined" class="yc:h-2 yc:mt-1 yc:bg-gray-200 yc:rounded">
									<div class="yc:h-2 yc:bg-red-500 yc:rounded" :style="{ width: uploadProgress[field.name][index] + '%' }"></div>
								</div>
							</li>
						</ul>
					</div>

					<p v-if="field.helptext" class="yc:text-xs yc:text-slate-500">{{ field.helptext }}</p>
			</div>
		</template>

		<div class="yc:pt-3 yc:mt-3">
			<button
					type="submit"
					:disabled="loading"
					class="yc:w-full yc:bg-red-600 yc:hover:bg-red-700 yc:disabled:opacity-60 yc:text-white yc:font-semibold yc:py-3 yc:rounded-xl yc:shadow-md yc:transition yc:active:scale-[.98]"
					>
					{{ loading ? 'Submitting...' : (props.submitLabel ?? 'Submit') }}
			</button>
		</div>

		<p v-if="error" class="yc:text-red-600 yc:mt-2">{{ error }}</p>
	</form>
</template>
