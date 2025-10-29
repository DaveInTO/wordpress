<script setup lang="ts">
	import { useContactState } from '../composables/useContactState'

// Pull in your Vue state
const { setAudience  } = useContactState()

// Tiles data: add image path instead of emoji
const tiles = [
  { id: 'schools', label: 'Childcare & Schools', desc: 'Snacktastic menus, allergy-smart systems, right-on-time deliveries.', cta: 'Get a Quote', icon: '/assets/gfx/ym-childcare.png' },
  { id: 'camps', label: 'Camps & Programs', desc: 'Summer fuel that survives buses, sun, and schedule chaos.', cta: 'Plan My Season', icon: '/assets/gfx/ym-camps.png' },
  { id: 'facilities', label: 'Facilities / RFPs', desc: 'Specs, site tours, compliance docs—served hot.', cta: 'Submit RFP / Book Tour', icon: '/assets/gfx/ym-facilities.png' },
  { id: 'careers', label: 'Careers', desc: 'Make food little humans love.', cta: 'See Open Roles', icon: '/assets/gfx/ym-careers.png' },
  { id: 'media', label: 'Media / Vendors', desc: 'Press, partnerships, and procurement.', cta: 'Contact Us', icon: '/assets/gfx/ym-media.png' },
  { id: 'general', label: 'General', desc: 'Not sure yet? We’ll point you right.', cta: 'Say Hello', icon: '/assets/gfx/ym-general.png' },
]
</script>

<template>
	<div class="grid grid-cols-6 justify-between ">
		<div  v-for="tile in tiles"   @click="setAudience(tile.id)">
			<div>
				<img :src="'/wp-content/plugins/contact-us/'+tile.icon" :alt="tile.label" class="w-8 h-8 object-contain" />
			</div>
			<h3 class="text-lg font-semibold text-gray-800 mb-2 text-center">{{ tile.label }}</h3>
			<p class="text-sm text-gray-600 text-center mb-4">{{ tile.desc }}</p>
			<span class="text-amber-700 font-medium underline">{{ tile.cta }}</span>
		</div>
	</div>

</template>

