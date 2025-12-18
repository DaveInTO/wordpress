<script setup lang="ts">
	import { reactive,ref, computed } from 'vue'
import { Label } from '@/components/ui/label'
import type { FieldOption,FieldDefinition } from '@/types'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { useSubmitForm } from '@/composables/useSubmitForm'

const props = defineProps<{
  fields: FieldDefinition[],
  title: string
}>()

const form = ref<Record<string, any>>({})
const submitted = ref(false)
const uploadProgress = reactive<Record<string, number[]>>({})

const { submitForm, loading, error } = useSubmitForm()


// initialize form model
props.fields.forEach(field => {
	switch (field.fieldtype) {
		case 'checkbox':
			form.value[field.name] = false
		break
		case 'file':
			form.value[field.name] = []
		break
		default:
			form.value[field.name] = ''
	}
})

const handleSubmit = async () => {
  submitted.value = false
  try {
  form.value.formType=props.title;
    await submitForm(form.value)
    submitted.value = true
    Object.keys(form.value).forEach(key => {
      form.value[key] = typeof form.value[key] === 'boolean' ? false : ''
    })
  } catch (err) {
    console.error(err)
  }
}
const onFileChange = (event: Event, fieldName: string, multiple?: boolean) => {
  const target = event.target as HTMLInputElement
  if (!target.files) return

  const files = multiple ? Array.from(target.files) : [target.files[0]]
  form.value[fieldName] = files

  // initialize progress array for this field
  uploadProgress[fieldName] = files.map(() => 0)
}


</script>

<template>
	<form @submit.prevent="handleSubmit" class="yc:space-y-5 yc:w-full">
		<div v-for="field in fields" :key="field.name" class="yc:space-y-1">
			<Label
					:for="field.name"
					class="yc:block yc:text-sm yc:font-medium yc:text-slate-700"
					>
					{{ field.label }}
					<span v-if="field.required" class="yc:text-red-500">*</span>
			</Label>

			<!-- Text, Email, Number, URL, Date -->
			<Input
					v-if="['text','email','number','url','date'].includes(field.fieldtype)"
					v-model="form[field.name]"
					:id="field.name"
					:type="field.fieldtype"
					:required="field.required"
					:autocomplete="field.autocomplete"
					:placeholder="field.placeholder"
					class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm"
					/>
			<!-- Date Input -->
			<div v-else-if="field.fieldtype === 'date'">
				<Label :for="field.name" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
					{{ field.label }}
				</Label>
				<Input
						type="date"
						:id="field.name"
						v-model="form[field.name]"
						class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm"
						/>
			</div>

			<Textarea
					v-else-if="field.fieldtype === 'textarea'"
					v-model="form[field.name]"
					:id="field.name"
					:required="field.required"
					:placeholder="field.placeholder"
					class="yc:w-full yc:rounded-xl yc:border yc:border-slate-300 yc:focus:ring-2 yc:focus:ring-red-500 yc:focus:border-red-500 yc:p-3 yc:outline-none yc:resize-none yc:transition"
					/>

				<!-- Checkbox -->
				<div v-else-if="field.fieldtype === 'checkbox'" class="yc:flex yc:items-center yc:gap-2">
					<Checkbox v-model="form[field.name]" :id="field.name" />
					<Label :for="field.name">{{ field.label }}</Label>
				</div>

				<!-- Dropdown -->
				<div v-else-if="field.fieldtype === 'dropdown'" class="yc:w-full">
					<select v-model="form[field.name]" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition yc:duration-200 yc:shadow-sm yc:appearance-none yc:bg-white yc:text-slate-700 yc:leading-normal yc:min-h-[48px]" >
						<option value="">Select {{field.label}}</option>
						<option v-for="option in field.options" :key="option.value" :value="option.value" >
						{{ option.label }}
						</option>
					</select>
				</div>

				<!-- Help text -->
				<!-- File Input -->
				<div v-else-if="field.fieldtype === 'file'" class="yc:mb-4">

					<Input type="file" :id="field.name" @change="onFileChange($event, field.name, field.multiple)" :multiple="field.multiple" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" />

					<ul v-if="form[field.name] && form[field.name].length" class="yc:mt-2 yc:list-disc yc:pl-5 yc:text-sm yc:text-gray-600">
						<li v-for="(file, index) in form[field.name]" :key="file.name" class="yc:mb-1">
							{{ file.name }}
							<div v-if="uploadProgress[field.name]?.[index] !== undefined" class="yc:h-2 yc:mt-1 yc:bg-gray-200 yc:rounded">
								<div
										class="yc:h-2 yc:bg-red-500 yc:rounded"
										:style="{ width: uploadProgress[field.name][index] + '%' }"
										></div>
							</div>
						</li>
					</ul>
				</div>
				<p v-if="field.helptext" class="yc:text-xs yc:text-slate-500">{{ field.helptext }}</p>
		</div>

		<div class="yc:pt-3 yc:mt-3">
			<button
					type="submit"
					:disabled="loading"
					class="yc:w-full yc:bg-red-600 yc:hover:bg-red-700 yc:text-white yc:font-semibold yc:py-3 yc:rounded-xl yc:shadow-md yc:transition yc:active:scale-[.98]"
					>
					{{ loading ? 'Submitting...' : 'Submit' }}
			</button>
		</div>

		<p v-if="error" class="yc:text-red-600 yc:mt-2">{{ error }}</p>
		<p v-if="submitted" class="yc:text-green-600 yc:text-center yc:mt-4 yc:font-medium yc:animate-fade-in">
		✅ Form submitted successfully!
		</p>
	</form>
</template>

