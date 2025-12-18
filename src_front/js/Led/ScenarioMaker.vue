<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import ColorPicker from '../Component/ColorPicker.vue';
import IconPicker from '../Component/IconPicker.vue';
import ModuleBridge from '../Utilities/ModuleBridge.js';


const props = defineProps({
	module: Object,
	scenarioOrigin: Object,
	icons: Array,
	effects: Array,
	links: Object,
})


let bridge = null;

const currentColor = ref("#0000FF");
const searchEffect = ref('');
const scenario = ref(props.scenarioOrigin);
const isConnected = ref('error')
const result = ref({
	status : '',
	message : '',
	url : '',
	isNew : false,
	display : false,
});


const colorAddPref = () => {
	if (!scenario.value.preference.colors.includes(currentColor.value)) {
		scenario.value.preference.colors.push(currentColor.value)
	}
}
const effectAddPref = (key) => {
	if (!scenario.value.preference.effects.includes(key)) {
		scenario.value.preference.effects.push(key)
	}
}

const removePrefColor = (key,event) => {
	event.stopPropagation();
	scenario.value.preference.colors.splice(key, 1)
}

const removePrefEffect = (key, event) => {
	event.stopPropagation()
	scenario.value.preference.effects.splice(key, 1)
}

const matchSearch = (name) => {
	if (searchEffect.value.length == 0) {
		return true;
	}
	const escapedSearch = searchEffect.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	const regex = new RegExp(escapedSearch, 'i');
	return regex.test(name);
}


const setColor = (color) => {
	console.log('setColor',color );
}

const setEffect = (effect) => {
	console.log('setEffect', effect)
}


const save = async  () => {
	let body = await fetch(props.links.save, {
		method: 'POST',
		 headers: {
			'Content-Type': 'application/json',
			'Accept': 'application/json'
		},
		body: JSON.stringify(scenario.value)
	}).then( (response) => {
		return response.json();
	})

	scenario.value = structuredClone(body.scenario);
	result.value =  structuredClone(body.result);

	if (result.value.isNew) {
		window.history.pushState({}, null, result.value.url)
	}

	setTimeout(() => {
		result.value.display = false
	}, 2000)
}

const addStep = (lineIndex) => {
	scenario.value.lines[lineIndex].steps.push({
		duration: 5,
		segments: [{
			first: 0,
			last: props.module.nb_leds - 1,
			effect: 0,
			colors: [currentColor.value,'',''],
			speed: 50,
			reverse: false
		}]
	})
}

const removeStep = (lineIndex, stepIndex) => {
	scenario.value.lines[lineIndex].steps.splice(stepIndex, 1)
}


onMounted( () => {
	bridge = new ModuleBridge(props.module)


	bridge.on('module-message', (data) => {
    	console.log('📨 Message reçu:', data)
	})

	bridge.on('connected', () => {
    	isConnected.value = 'success';
  	})
  
	bridge.on('disconnected', () => {
		isConnected.value = 'warning';
	})
  
	bridge.on('error', () => {
		isConnected.value = 'error';
	})

	if (!scenario.value.lines || scenario.value.lines.length === 0) {
		scenario.value.lines = []

		// Créer autant de lignes que le module en a
		for (let i = 0; i < props.module.nb_line; i++) {
			scenario.value.lines.push({
				steps: []
			});
			addStep(i);
		}
	}
});

onBeforeUnmount(() => {
	if (bridge) {
		bridge.disconnect()
	}
})

</script>

<template>
<section class="scenario-layout">
	<div class="scenario-side-bar accordion">
		<details class="accordion-item card card-primary mb-s">
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
				<div v-for="(prefColor,key) in scenario.preference.colors" class="pref-color" :style="{'background-color':prefColor}" @click="setColor(prefColor)">
					<div class="pref-color-trash" @click="removePrefColor(key,$event)"><i class="icon-trash"></i></div>
				</div>
			</div>
		</div>

		<details class="accordion-item card card-primary mb-s">
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
				<div v-for="(prefEffect,key) in scenario.preference.effects" class="pref-effect" @click="setEffect(prefEffect)">
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
		<div class="card mb-s">
			<header class="card-header">
				<h3 class="glitch" data-text="Info">Info</h3>
			</header>
			<div class="scenario-info card-body form-inline">
				<div class="form-group">
					<label for="Name" class="form-label required">Icon</label>
					<IconPicker 
						v-model="scenario.icon"
						:icons="props.icons"
					></IconPicker>
				</div>
				<div class="form-group">
					<label for="Name" class="form-label required">Name</label>
					<input type="text" id="Name" name="name" required class="form-input" v-model="scenario.name">
				</div>
				<div class="form-group"><button type="button" class="btn-success" @click="save"><i class="icon-save"></i> SAVE</button></div>
			</div>
		</div>

		<template v-for="(line,keyLine) in scenario.lines" >
		<details class="accordion-item card card-primary mb-s">
			<summary class="accordion-header">
				<h3 class="corner-accent">Ligne {{ keyLine }}</h3>
			</summary>
			<div class="accordion-content card-body">
				<h4>line</h4>
				<template v-for="(step,keySteps) in line.steps">
					<h5>step</h5>
					ici duration + createur de segment <br>	
					<template v-for="(segment,keySegment) in step.segments">
						<h6>segment</h6>
						ici les info <br>
						first - last <br>
						effect - colors <br>

						speed -	reverse <br>
					</template>
				</template>
			</div>
		</details>
		</template>
	</div>
	<template v-if="result.display">
		<div class="scenario-flash">
			<div :class="'flash-message flash-'+result.status" @click="result.display = false">
				<i :class="'icon-'+result.status"></i>{{result.message}}
			</div>
		</div>
	</template>
	<div :class="'scenario-is-conneced '">
		<i :class="'icon-'+isConnected+' neon-'+isConnected+'-light'"></i>	
	</div>
</section>
</template>
