<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import ColorPicker from '../Component/ColorPicker.vue';

const props = defineProps({
	module: Object,
	scenario: Object,
	icons: Array,
	effects: Array,
	links: Object,
})


const count = ref(0)
const currentColor = ref("#0000FF");
const prefColors = ref([]);
const prefEffects = ref([]);
const searchEffect = ref('');


const colorAddPref = () => {
	if (!prefColors.value.includes(currentColor.value)) {
		prefColors.value.push(currentColor.value)
	}
}
const effectAddPref = (key) => {
	if (!prefEffects.value.includes(key)) {
		prefEffects.value.push(key)
	}
}

const removePrefColor = (key,event) => {
	event.stopPropagation();
	prefColors.value.splice(key, 1)
}

const setColor = (color) => {
	console.log('setColor');
}


const removePrefEffect = (key, event) => {
	event.stopPropagation()
	prefEffects.value.splice(key, 1)
}

const setEffect = (effect) => {
	// Pour plus tard quand t'auras les segments
	console.log('setEffect', effect)
}

const matchSearch = (name) => {
	if (searchEffect.value.length == 0) {
		return true;
	}
	const escapedSearch = searchEffect.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	const regex = new RegExp(escapedSearch, 'i');
	return regex.test(name);
}


onMounted(() => {
	prefColors.value = props.scenario.preference.colors
	prefEffects.value = props.scenario.preference.effects
});


</script>

<template>
<section class="scenario-layout">
	<div class="scenario-side-bar accordion">
		<details class="accordion-item card card-primary mb-s" open>
			<summary class="accordion-header">
				<h3 class="glitch" data-text="Palette">Palette</h3>
			</summary>
			<div class="accordion-content card-body">
				<ColorPicker 
					v-model="currentColor"
				/>
				<div class="text-right">
					<div class="color-result">
						<span class="color-preview" :style="{'background-color':currentColor}"></span>
						<span class="color-text" >{{ currentColor }}</span>
						<span class="btn-mini btn-favorie" @click="colorAddPref"> <i class="icon-heart"></i> </span>
					</div>
				</div>
			</div>
		</details>
		<div class="card mb-s">
			<div class="card-header">
				<h6>Couleur</h6>
			</div>
			<div class="pref-color-wrapper card-body">
				<div v-for="(prefColor,key) in prefColors" class="pref-color" :style="{'background-color':prefColor}" @click="setColor(prefColor)">
					<div class="pref-color-trash" @click="removePrefColor(key,$event)"><i class="icon-trash"></i></div>
				</div>
			</div>
		</div>

		<details class="accordion-item card card-primary mb-s" open>
			<summary class="accordion-header">
				<h3 class="glitch" data-text="Effect">Effect</h3>
			</summary>
			<div class="accordion-content">
				<div class="form-group ph-m">
					<label for="" class="form-label"></label>
					<input type="text" class="form-input" v-model="searchEffect">
				</div>
				<div class="effects-list pb-l">
					<div class="scrollable">
						<template v-for="(name,key) in props.effects">
							<div v-if="matchSearch(name)" class="effect">
								<div class="effect-key">{{key}} </div>
								<div class="color-cyan"> => </div>
								<div class="effect-name"> {{name}} </div>
								<div class="effect-favorie"><span class="btn-mini btn-favorie" @click="effectAddPref(key)"> <i class="icon-heart"></i> </span></div>
							</div>
						</template>
					</div>
				</div>
			</div>
		</details>
		<div class="card mb-l">
			<div class="card-header">
				<h6>Effet</h6>
			</div>
			<div class="pref-effect-wrapper card-body">
				<div v-for="(prefEffect,key) in prefEffects" class="pref-effect" @click="setEffect(prefEffect)">
					<div class="effect-key">{{prefEffect}} </div>
					<div class="color-cyan"> => </div>
					<div class="effect-name"> {{ props.effects[prefEffect] }} </div>
					<div class="effect-trash" @click="removePrefEffect(key,$event)">
						<i class="icon-trash"></i>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="scenario-content">
		<h2>{{value}}</h2>
		<p>Compteur: {{ count }}</p>
		<button @click="count++">+1</button>
	</div>
</section>
</template>
