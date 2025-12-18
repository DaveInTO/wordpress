<script setup lang="ts">
	import {ref} from 'vue';
import {InstagramIcon,CircleFadingPlusIcon}  from 'lucide-vue-next';
import { tiles, forms } from '@/data/'
import DynamicForm from '@/components/DynamicForm.vue';
import Tiles from '@/components/Tiles.vue';
import {Label} from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle, } from '@/components/ui/card'
import {Input} from '@/components/ui/input';
const selectedTab=ref(tiles[5]);
const selected=ref(false);
const form = ref<Record<string,string>>({});
const submitted = ref(false);


</script>

<template>
	<main class="yc:min-h-screen">
		<template v-if="selected==false">
			<h1 class="yc:text-xl">Let’s feed tiny legends.</h1>
			<p>Tell us who you are and what you need—we’ll match you with the right Yummy human. We reply within 1 business day.</p>
			<section class="yc:max-w-6xl yc:mx-auto">
				<div class="yc:grid yc:grid-cols-3 yc:gap-4">
					<template v-for="tile in tiles">
						<div v-if="typeof tile.link == 'undefined'" @click="selectedTab=tile;selected=true" class="yc:flex yc:flex-col yc:justify-start yc:items-center">
							<div>
								<img :src="'/wp-content/plugins/contact-us/'+tile.icon" :alt="tile.label" class="yc:object-contain m-3" />
							</div>
							<h3 class="yc:text-lg yc:font-semibold yc:text-gray-800 yc:mb-2 yc:text-center">{{ tile.label }}</h3>
							<div class="yc:inline-block yc:px-6 yc:py-3 yc:bg-red-500 text-sm yc:text-white yc:font-semibold yc:rounded-lg yc:shadow-md yc:hover:bg-red-600 yc:hover:scale-105 yc:transform yc:transition yc:duration-200 yc:cursor-pointer yc:text-center">{{ tile.cta }}</div>
						</div>
						<div v-else class="yc:flex yc:flex-col yc:justify-start yc:items-center">
							<a :href="tile.link" class="yc:text-center"> <div>
									<img :src="'/wp-content/plugins/contact-us/'+tile.icon" :alt="tile.label" class="yc:object-contain m-3" />
								</div>
								<h3 class="yc:text-lg yc:font-semibold yc:text-gray-800 yc:mb-2 yc:text-center">{{ tile.label }}</h3>
								<div class="yc:inline-block yc:px-6 yc:py-3 yc:bg-red-500 text-sm yc:text-white yc:font-semibold yc:rounded-lg yc:shadow-md yc:hover:bg-red-600 yc:hover:scale-105 yc:transform yc:transition yc:duration-200 yc:cursor-pointer yc:text-center">{{ tile.cta }}</div>

							</a>
						</div>
					</template>
				</div>
			</section>
			<section name="contactinfo yc:mt-4">
				<div class="yc:flex  yc:w-full yc:justify-center yc:mt-10 yc:p-5">
					<div class="yc:grid yc:grid-cols-2 gap-4">
						<div>
							<div class="yc:font-semibold">Adresss:</div>
						</div>
						<div class="yc:ml-3">
							<address>
								Yummy Catering<br>
								25 Sheffield Street<br>
								North York, ON M6M 3E5
							</address>

						</div>
						<div>
							<div class="yc:font-semibold">Phone:</div>
							<div class="yc:mt-2 yc:font-semibold">Follow us:</div>
							<div class="yc:mt-2 yc:font-semibold">General Email:</div>
							<div class="yc:mt-2 yc:font-semibold">Sales/Partnerships:</div>
							<div class="yc:mt-2 yc:font-semibold">Media/Press:</div>
							<div class="yc:mt-2 yc:font-semibold">Billing & e-Transfers (customers):</div>
							<div class="yc:mt-2 yc:font-semibold">Receiving:</div>
						</div>
						<div class="yc:ml-3">
							416-532-5250
							<div class="yc:mt-2 yc:flex yc:justify-start yc:space-x-2">
								<a href="https://www.facebook.com/yummycateringcanada" target="_new"> <CircleFadingPlusIcon class="yc:text-red-600"/> </a>
								<a href="https://www.instagram.com/yummycatering" target="_new"><InstagramIcon class="yc:text-red-600"/></a>
							</div>
							<div class="yc:mt-2 "><a href="mailto:info@yummycatering.ca" target="_new">info@yummycatering.ca</a></div>
							<div class="yc:mt-2 "><a href="mailto:info@yummycatering.ca" target="_new">info@yummycatering.ca</a></div>
							<div class="yc:mt-2 "><a href="mailto:press@yummycatering.ca" target="_new">press@yummycatering.ca</a></div>
							<div class="yc:mt-2 "><a href="mailto:accounting@yummycatering.ca" target="_new">accounting@yummycatering.ca</a></div>
							<div class="yc:mt-2 "><a href="mailto:receiving@yummycatering.ca" target="_new">receiving@yummycatering.ca</a></div>

						</div>
					</div>

				</div>
			</section>
		</template>
		<template v-else>
			<section class="yc:w-full yc:mx-auto" id="contact">
				<Card class="yc:p-3">
				<CardHeader>
				<div class="yc:flex yc:justify-between yc:w-full yc:items-center">
					<div>
						<h2 class="yc:text-2xl yc:font-semibold yc:text-slate-800 yc:mb-2">{{selectedTab!.label}}</h2>
						<p class="yc:text-slate-500 yc:mb-6">{{selectedTab!.desc}}</p>
					</div>
					<div class="yc:inline-block yc:px-6 yc:py-3 yc:bg-red-500 text-sm yc:text-white yc:font-semibold yc:rounded-lg yc:shadow-md yc:hover:bg-red-600 yc:hover:scale-105 yc:transform yc:transition yc:duration-200 yc:cursor-pointer yc:text-center" @click.prevent="selected=false">Back</div>
				</div>
				</CardHeader>
				<CardContent>
				<DynamicForm :fields="forms[selectedTab.id].fields" :title="selectedTab!.label"/>
				</CardContent>
				</Card>
			</section>
		</template>
	</main>

</template>

<style scoped>
</style>
