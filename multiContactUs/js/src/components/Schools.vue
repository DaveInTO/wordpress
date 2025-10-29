<script setup lang="ts">
import { ref,reactive } from 'vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import {Label} from '@/components/ui/label';
import {Input} from '@/components/ui/input';


const form = reactive({
  organization: '',
  address1: '',
  address2: '',
  contractType: '',
  currentLocation: '',
  role: '',
  email: '',
  phone: '',
  city: '',
  sitesEnrolment: '',
  mealsPerDay: '',
  targetStartDate: '',
  deliveryWindow: '',
})
type FormKeys = keyof typeof form;

const missingFields = ref<string[]>([])
const showAlert = ref(false)

const validateForm = () => {
  const required = ['organizationName', 'email', 'phone', 'city', 'address1']
missingFields.value = required.filter(
  (f) => !form[f as FormKeys]?.trim()
);

  if (missingFields.value.length > 0) {
    showAlert.value = true
    return false
  }

  showAlert.value = false
  return true
}

const submitForm = () => {
  if (!validateForm()) return
  // submit logic here
  console.log('Form submitted', form)
}
</script>

<template>
	<form @submit.prevent="submitForm" class="yc:space-y-6 yc:w-full yc:mx-auto yc:p-6 ">
		<Alert v-if="showAlert" variant="destructive" class="yc:mb-4">
		<AlertTitle>Missing Required Fields</AlertTitle>
		<AlertDescription>
		Please fill in:
		<ul class="yc:ml-4 yc:list-disc">
			<li v-for="field in missingFields" :key="field" class="yc:capitalize">
				{{ field.replace(/([A-Z])/g, ' $1') }}
			</li>
		</ul>
		</AlertDescription>
		</Alert>

		<!-- Organization Name -->
		<div>
			<Label for="organization" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Organization Name</Label>
			<Input v-model="form.organization" type="text" id="organization" placeholder="Organization name" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" required />
		</div>

		<!-- Role -->
		<div>
			<Label for="role" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Role</Label>
			<Input v-model="form.role" type="text" id="role" placeholder="Your role" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" />
		</div>

		<!-- Email -->
		<div>
			<Label for="email" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Email Address</Label>
			<Input v-model="form.email" type="email" id="email" placeholder="you@example.com" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" required />
		</div>

		<!-- Phone -->
		<div>
			<Label for="phone" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Phone</Label>
			<Input v-model="form.phone" type="tel" id="phone" placeholder="(555) 123-4567" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" required />
		</div>
		<!-- Address 1 -->
		<div>
			<Label for="address1" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Address Line 1
			</Label>
			<Input v-model="form.address1" type="text" id="address1" placeholder="123 Main Street" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" required />
		</div>

		<!-- Address 2 -->
		<div class="yc:mt-4">
			<Label for="address2" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Address Line 2 <span class="yc:text-slate-400 yc:text-xs">(optional)</span>
			</Label>
			<Input v-model="form.address2" type="text" id="address2" placeholder="Apartment, suite, unit, etc. (optional)" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" />
		</div>
		<!-- City/Area -->
		<div>
			<Label for="city" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">City / Area</Label>
			<Input v-model="form.city" type="text" id="city" placeholder="City or area" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" required/>
		</div>

		<div class="yc:mt-4">
			<Label for="contractType" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Contract Type
			</Label>
			<select v-model="form.contractType" id="contractType" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition yc:duration-200 yc:shadow-sm yc:appearance-none yc:bg-white yc:text-slate-700 yc:leading-normal yc:min-h-[48px]"  >
				<option value="" disabled>Select type</option>
				<option value="temporary">Temporary</option>
				<option value="permanent">Permanent</option>
			</select>
		</div>

		<!-- Current Location -->
		<div class="yc:mt-4">
			<Label for="currentLocation" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Current Location
			</Label>
			<select v-model="form.currentLocation" id="currentLocation" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition yc:duration-200 yc:shadow-sm yc:appearance-none yc:bg-white yc:text-slate-700 yc:leading-normal yc:min-h-[48px]"  >
				<option value="" disabled>Select location type</option>
				<option value="onsite-cook">Onsite Cook</option>
				<option value="catered">Catered</option>
			</select>
		</div>

		<!-- Sites & Daily Enrolment -->
		<div>
			<Label for="sitesEnrolment" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Number of Locations and daily Enrollment</Label>
			<Input v-model="form.sitesEnrolment" type="text" id="sitesEnrolment" placeholder="Number of sites and daily enrolment" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" />
		</div>

		<!-- Meals per Day & Target Start Date -->
		<div>
			<Label for="mealsPerDay" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Meals/Day & Target Start Date</Label>
			<Input v-model="form.mealsPerDay" type="text" id="mealsPerDay" placeholder="Breakfast/Lunch/Snacks – Target start date" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" />
		</div>

		<!-- Delivery Window / Site Constraints -->
		<div>
			<Label for="deliveryWindow" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">Delivery Window / Site Constraints</Label>
			<Input v-model="form.deliveryWindow" type="text" id="deliveryWindow" placeholder="Specify delivery window and constraints" class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-gray-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:transition-shadow yc:duration-200 yc:outline-none yc:shadow-sm" />
		</div>

		<!-- Submit -->
		<div class="yc:text-right">
			<button
					type="submit"
					class="yc:inline-block yc:px-6 yc:py-3 yc:bg-red-500 yc:text-white yc:font-semibold yc:rounded-xl yc:shadow-md yc:hover:bg-red-600 yc:transition yc:duration-200 yc:cursor-pointer"
					>
					Submit
			</button>
		</div>
	</form>
</template>

