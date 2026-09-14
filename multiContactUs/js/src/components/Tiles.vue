<script setup lang="ts">
import { useContactState } from '../composables/useContactState'
import {Tile} from '@/types';
// Pull in your Vue state
const { setAudience  } = useContactState()
const props = defineProps<{
  tiles: Tile[]
  }>()
const tiles=props.tiles;
</script>
<template>
	<div class="grid grid-cols-6 justify-between gap-4">
		<template v-for="tile in tiles" :key="tile.id">
			<!-- If tile.link exists, render <a> -->
			<a
					v-if="tile.link"
					:href="tile.link"
					class="flex flex-col items-center cursor-pointer hover:opacity-90"
					>
					<img
							:src="'/wp-content/plugins/contact-us/' + tile.icon"
							:alt="tile.label"
							class="w-8 h-8 object-contain"
							/>
					<h3 class="text-lg font-semibold text-gray-800 mb-2 text-center">{{ tile.label }}</h3>
					<p class="text-sm text-gray-600 text-center mb-4">{{ tile.desc }}</p>
					<span class="text-amber-700 font-medium underline">{{ tile.cta }}</span>
			</a>

			<!-- Otherwise, use div and call setAudience -->
			<div
					v-else
					@click="setAudience(tile.id)"
					class="flex flex-col items-center cursor-pointer hover:opacity-90"
					>
					<img
							:src="'/wp-content/plugins/contact-us/' + tile.icon"
							:alt="tile.label"
							class="w-8 h-8 object-contain"
							/>
					<h3 class="text-lg font-semibold text-gray-800 mb-2 text-center">{{ tile.label }}</h3>
					<p class="text-sm text-gray-600 text-center mb-4">{{ tile.desc }}</p>
					<span class="text-amber-700 font-medium underline">{{ tile.cta }}</span>
			</div>
		</template>
	</div>
</template>

