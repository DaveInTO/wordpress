<script setup lang="ts">
	import { ref } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

const form = ref({
  campName: '',
  email: '',
  phone: '',
  address1: '',
  address2: '',
  city: '',
  preferredDates: '',
  groupDetails: '',
  campFiles: [] as File[]
})

const handleFiles = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    form.value.campFiles = Array.from(target.files)
  }
}

const submitForm = () => {
  console.log('Camps form:', form.value)
  // handle API submission here
}
</script>

<template>
	<form @submit.prevent="submitForm" class="yc:w-full yc:mx-auto yc:bg-white yc:p-8 yc:space-y-6">

		<!-- Camp / Program Name -->
		<div>
			<Label for="campName" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Camp / Program Name
			</Label>
			<Input
					type="text"
					id="campName"
					v-model="form.campName"
					placeholder="Name of your camp or program"
					class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition-shadow yc:duration-200 yc:shadow-sm"
					/>
		</div>
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
		<!-- Preferred Dates -->
		<div>
			<Label for="preferredDates" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Preferred Dates
			</Label>
			<Input
					type="text"
					id="preferredDates"
					v-model="form.preferredDates"
					placeholder="e.g. July 15–20, 2025"
					class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition-shadow yc:duration-200 yc:shadow-sm"
					/>
		</div>

		<!-- Group Size / Age Range -->
		<div>
			<Label for="groupDetails" class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Group Size / Age Range
			</Label>
			<Textarea
					id="groupDetails"
					v-model="form.groupDetails"
					placeholder="e.g. 40 campers, ages 8–12"
					rows="3"
					class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition-shadow yc:duration-200 yc:shadow-sm"
					/>
		</div>

		<!-- Upload Info Packet or Itinerary -->
		<div>
			<Label class="yc:block yc:text-sm yc:font-medium yc:text-slate-700 yc:mb-1">
				Upload Info Packet / Itinerary (optional)
			</Label>
			<Input
					type="file"
					@change="handleFiles"
					multiple
					class="yc:w-full yc:p-3 yc:rounded-xl yc:border yc:border-red-300 yc:focus:border-red-500 yc:focus:ring-1 yc:focus:ring-red-500 yc:outline-none yc:transition-shadow yc:duration-200 yc:shadow-sm"
					/>
			<ul class="yc:mt-2 yc:list-disc yc:pl-5 yc:text-sm yc:text-gray-600">
				<li v-for="file in form.campFiles" :key="file.name">{{ file.name }}</li>
			</ul>
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

