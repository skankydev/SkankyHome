<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import ColorPicker from '../Component/ColorPicker.vue';
import IconPicker from '../Component/IconPicker.vue';
import QuickSelectColor from '../Component/QuickSelectColor.vue';
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

const dragState = ref({
	isDragging: false,
	cursorIndex: null,
	lineIndex: null,
	stepIndex: null
})

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
		cursors:[0,props.module.nb_led - 1],
		segments: [{
			first: 0,
			last: props.module.nb_led - 1,
			effect: 0,
			colors: ['','',''],
			speed: 1000,
			reverse: false
		}]
	})
}
const duplicateStep = (lineIndex, stepIndex) => {
	const step = scenario.value.lines[lineIndex].steps[stepIndex];

	// Clone profond du step
	const newStep = {
		duration: step.duration,
		cursors: [...step.cursors],
		segments: step.segments.map(seg => ({
			first: seg.first,
			last: seg.last,
			effect: seg.effect,
			colors: [...seg.colors],
			speed: seg.speed,
			reverse: seg.reverse
		}))
	};

	// Insérer juste après le step actuel
	scenario.value.lines[lineIndex].steps.splice(stepIndex + 1, 0, newStep);
}

const removeStep = (lineIndex, stepIndex) => {
	scenario.value.lines[lineIndex].steps.splice(stepIndex, 1)
}

const addCursor = (lineIndex,stepIndex,position) => {
	let cursors = scenario.value.lines[lineIndex].steps[stepIndex].cursors;
	if(cursors.length >= 5){
		return;
	}
	if (!cursors.includes(position)) {
		cursors.push(position)
		cursors.sort((a, b) => a - b)
	}
	scenario.value.lines[lineIndex].steps[stepIndex].cursors = cursors;
	splitSegment(lineIndex,stepIndex);
}

const removeCursor = (lineIndex, stepIndex, cursorIndex) => {
	let cursors = scenario.value.lines[lineIndex].steps[stepIndex].cursors
	cursors = JSON.parse(JSON.stringify(cursors))
	if (cursorIndex === 0 || cursorIndex === cursors.length - 1) {
		return
	}

	cursors.splice(cursorIndex, 1)
	scenario.value.lines[lineIndex].steps[stepIndex].cursors = cursors;
	splitSegment(lineIndex, stepIndex)
}

const splitSegment = (lineIndex,stepIndex) => {
	let cursors = scenario.value.lines[lineIndex].steps[stepIndex].cursors;
	let segments = scenario.value.lines[lineIndex].steps[stepIndex].segments;
	let newSegments = [];
  
	for (let i = 0; i < cursors.length - 1; i++) {
	
		let template = segments[i] || segments[i - 1] || {};

		newSegments.push({
			first: cursors[i],
			last: cursors[i + 1] - 1,
			effect: template?.effect ?? 0,
			colors: [...(template?.colors ?? ['','',''])],
			speed: template?.speed ?? 1000,
			reverse: template?.reverse ?? false
		});
	}

	newSegments[newSegments.length - 1].last = props.module.nb_led - 1;
	scenario.value.lines[lineIndex].steps[stepIndex].segments = newSegments
}

const startDrag = (lineIndex, stepIndex, cursorIndex, event) => {
	const cursors = scenario.value.lines[lineIndex].steps[stepIndex].cursors

	if (cursorIndex === 0 || cursorIndex === cursors.length - 1) {
		return
	}

	dragState.value = {
		isDragging: true,
		cursorIndex,
		lineIndex,
		stepIndex
	}

	event.preventDefault()
}

// Pendant le drag
const onDrag = (lineIndex, stepIndex, ledPosition, event) => {
	if (!dragState.value.isDragging) return
	if (dragState.value.lineIndex !== lineIndex) return
	if (dragState.value.stepIndex !== stepIndex) return

	const cursors = scenario.value.lines[lineIndex].steps[stepIndex].cursors
	const cursorIndex = dragState.value.cursorIndex

	// Vérifier que la nouvelle position est valide
	const prevCursor = cursors[cursorIndex - 1]
	const nextCursor = cursors[cursorIndex + 1]

	// Ne pas dépasser les curseurs voisins
	if (ledPosition <= prevCursor || ledPosition >= nextCursor) {
		return
	}

	// Mettre à jour la position
	cursors[cursorIndex] = ledPosition
	splitSegment(lineIndex, stepIndex)
}

// Arrêter le drag
const stopDrag = () => {
	dragState.value.isDragging = false
}

const divideLeds = (lineIndex, stepIndex, parts) => {
	const nbLeds = props.module.nb_led;
	const step = Math.floor(nbLeds / parts);

	// Créer les nouveaux curseurs
	let newCursors = [0];
	for (let i = 1; i < parts; i++) {
		newCursors.push(i * step);
	}
	newCursors.push(nbLeds - 1);

	scenario.value.lines[lineIndex].steps[stepIndex].cursors = newCursors;
	splitSegment(lineIndex, stepIndex);
}

const resetCursors = (lineIndex, stepIndex) => {
	scenario.value.lines[lineIndex].steps[stepIndex].cursors = [0, props.module.nb_led - 1];
	splitSegment(lineIndex, stepIndex);
}

const previewSegments = (segments) => {
	let message = {segments:segments};
	bridge.send(message);
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
	window.addEventListener('mouseup', stopDrag)
	window.addEventListener('mouseleave', stopDrag)
});

onBeforeUnmount(() => {
	if (bridge) {
		bridge.disconnect()
	}
	window.removeEventListener('mouseup', stopDrag)
	window.removeEventListener('mouseleave', stopDrag)
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
				<ColorPicker v-model="currentColor" />
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
		<div class="scenario-title card mb-s">
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
		<details class="accordion-item card card-primary mb-s" open>
			<summary class="accordion-header ">
				<div><h2 class="rainbow-underline">Ligne {{ keyLine+1 }}</h2></div>
			</summary>
			<div class="accordion-content card-body">
				<details v-for="(step,keySteps) in line.steps">
					<summary class="accordion-header">
						<div><h3>Step {{ keySteps+1 }}</h3></div>
					</summary>
					<div class="card-header-action">
						<div class="form-group">
							<label for="Speed" class="form-label">Durée (sec)</label>
							<input type="number" name="first" class="form-input" v-model="step.duration">
						</div>
						<div>
							<div class="pb-m">
								<div class="btn-mini btn-info mh-s" @click="addStep(keyLine)"><i class="icon-add"></i></div>
								<div class="btn-mini btn-warning mh-s" @click="duplicateStep(keyLine, keySteps)" title="Dupliquer step"><i class="icon-copy"></i></div>
								<div v-if=" line.steps.length > 1" class="btn-mini btn-error mh-s" @click="removeStep(keyLine,keySteps)"><i class="icon-trash"></i></div>
								<div class="btn-mini btn-success mh-s" @click="previewSegments(step.segments)"><i class="icon-upload"></i></div>
							</div>
							<div class="flex">
								<div class="btn-mini mh-s" @click="divideLeds(keyLine,keySteps,2)">2</div>
								<div class="btn-mini mh-s" @click="divideLeds(keyLine,keySteps,3)">3</div>
								<div class="btn-mini mh-s" @click="divideLeds(keyLine,keySteps,4)">4</div>
								<div class="btn-mini btn-error mh-s" @click="resetCursors(keyLine, keySteps)" title="Reset"><i class="icon-refresh-cw"></i></div>
							</div>
						</div>
					</div>
					<div class="led-visualizer">
						<label>line</label>
						<div class="led-bar-container">
							<div class="led-bar" :style="{ gridTemplateColumns: `repeat(${props.module.nb_led}, 1fr)` }">
								<div 
									v-for="i in props.module.nb_led" 
									:key="i"
									class="led-point"
									:class="{ 'led-cursor-here': step.cursors.includes(i - 1) }"
									@click="addCursor(keyLine, keySteps, i - 1)"
									@mouseenter="onDrag(keyLine, keySteps, i - 1, $event)"
									:data-tooltip="'LED '+(i - 1)" data-variant="warning"
								></div>
							</div>
							<div class="cursors-layer" :style="{ gridTemplateColumns: `repeat(${props.module.nb_led}, 1fr)` }">
								<div 
									v-for="(pos, index) in step.cursors"
									:key="'cursor-' + index"
									class="led-cursor"
									 :class="{ 
										'cursor-locked': index === 0 || index === step.cursors.length - 1,
										'cursor-dragging': dragState.isDragging && 
											dragState.cursorIndex === index && 
											dragState.lineIndex === keyLine && 
											dragState.stepIndex === keySteps
									}"
									:style="{ gridColumn: pos + 1 }"
									@mousedown="startDrag(keyLine, keySteps, index, $event)"
									@click.stop="removeCursor(keyLine, keySteps, index)"
								>
									<span class="cursor-label">{{ pos }}</span>
								</div>
							</div>
						</div>
					</div>
					
					<template v-for="(segment,keySegment) in step.segments">
					<div class="segment-form">
						<div class="form-group">
							<label for="Speed" class="form-label">Led</label>
							<div class="segment-form-leds">
								<input type="number" name="first" class="form-input" disabled v-model="segment.first">
								<input type="number" name="last" class="form-input" disabled v-model="segment.last">
							</div>
						</div>
						<div class="form-group">
							<label for="Effect" class="form-label">Effect</label>
							<select name="effect" id=""  v-model="segment.effect">
								<option value=""></option>
								<option v-for="(prefEffect,keyEffect) in scenario.preference.effects" :value="prefEffect">
									{{prefEffect}} => {{ props.effects[prefEffect] }} 
								</option>
							</select>
						</div>
						<div class="form-group">
							<label for="Effect" class="form-label">Color</label>
							<div class="segment-form-color">
								<template v-for="(color, keyColor) in segment.colors">
									<QuickSelectColor
										v-model="segment.colors[keyColor]"
										:list="scenario.preference.colors"
									></QuickSelectColor>
								</template>
							</div>
						</div>
						<div class="form-group">
							<label for="Speed" class="form-label">Speed</label>
							<input type="number" id="Speed" name="speed" class="form-input" v-model="segment.speed">
						</div>
						<div class="form-group">
							<input type="checkbox" :id="'reverse'+keyLine+'_'+keySteps+'_'+keySegment" name="reverse" class="form-input" v-model="segment.reverse">
							<label :for="'reverse'+keyLine+'_'+keySteps+'_'+keySegment" class="form-label">reverse</label>
						</div>
					</div>
					</template>
				</details>
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
