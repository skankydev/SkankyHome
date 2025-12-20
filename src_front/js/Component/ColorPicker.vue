<script setup>

import { computed, ref, onMounted  } from 'vue'

const props = defineProps({
	modelValue: {
		type: String,
		default: '#000000'
	}
});

const emit = defineEmits(['update:modelValue']);

// Computed bidirectionnel pour v-model
const color = computed({
	get: () => props.modelValue,
	set: (value) => emit('update:modelValue', value)
});


const canvas = ref(null)
const isDragging = ref(false)
const pickerX = ref(0)
const pickerY = ref(0)

const red = ref(0);
const green = ref(0);
const blue = ref(0);

const brightness = ref(100);

const drawPicker = () => {
	const ctx = canvas.value.getContext('2d');
	const width = canvas.value.width;
	const height = canvas.value.height;

	 const whiteMix = 1 - (brightness.value / 100)
	
	if (brightness.value < 100) {
		ctx.fillStyle = '#fff'
		ctx.fillRect(0, 0, width, height)
	}

	const gradientH = ctx.createLinearGradient(0, 0, width, 0)
	gradientH.addColorStop(0, `rgba(255, 0, 0, ${brightness.value / 100})`)
	gradientH.addColorStop(0.20, `rgba(255, 255, 0, ${brightness.value / 100})`)
	gradientH.addColorStop(0.40, `rgba(0, 255, 0, ${brightness.value / 100})`)
	gradientH.addColorStop(0.60, `rgba(0, 255, 255, ${brightness.value / 100})`)
	gradientH.addColorStop(0.80, `rgba(0, 0, 255, ${brightness.value / 100})`)
	gradientH.addColorStop(1, `rgba(255, 0, 255, ${brightness.value / 100})`)

	ctx.fillStyle = gradientH
	ctx.fillRect(0, 0, width, height)

	// Dégradé vertical vers le noir
	const gradientV = ctx.createLinearGradient(0, 0, 0, height)
	gradientV.addColorStop(0, 'rgba(0, 0, 0, 0)')
	gradientV.addColorStop(1, '#000')

	ctx.fillStyle = gradientV
	ctx.fillRect(0, 0, width, height)
}

const getColorAtPosition = (x, y) => {
	const ctx = canvas.value.getContext('2d');
	const pixel = ctx.getImageData(x, y, 1, 1).data;

	red.value = pixel[0];
	green.value = pixel[1];
	blue.value = pixel[2];

	let value = `#${((1 << 24) + (pixel[0] << 16) + (pixel[1] << 8) + pixel[2]).toString(16).slice(1)}`;
	return value;
}

const handleCanvasClick = (event) => {
	const rect = canvas.value.getBoundingClientRect();
	const x = event.clientX - rect.left;
	const y = event.clientY - rect.top;

	// Garder dans les limites du canvas
	pickerX.value = Math.max(0, Math.min(x, canvas.value.width));
	pickerY.value = Math.max(0, Math.min(y, canvas.value.height));

	const color = getColorAtPosition(pickerX.value, pickerY.value);
	emit('update:modelValue', color);

	drawCursor();
}

const handleMouseDown = (event) => {
	isDragging.value = true;
	handleCanvasClick(event);
}

const handleMouseMove = (event) => {
	if (isDragging.value) {
		handleCanvasClick(event);
	}
}

const handleMouseUp = () => {
	isDragging.value = false;
}

const changeColor = () => {
	const color = `#${((1 << 24) + (red.value << 16) + (green.value << 8) + blue.value).toString(16).slice(1)}`;
	emit('update:modelValue', color);

	const pos = findPositionForColor(color);
	pickerX.value = pos.x;
	pickerY.value = pos.y;
	drawCursor();
}

const findPositionForColor = (hexColor) => {
	const ctx = canvas.value.getContext('2d')
	const width = canvas.value.width
	const height = canvas.value.height

	// Convertir hex en RGB
	const r = parseInt(hexColor.slice(1, 3), 16)
	const g = parseInt(hexColor.slice(3, 5), 16)
	const b = parseInt(hexColor.slice(5, 7), 16)

	// Parcourir le canvas pour trouver la couleur la plus proche
	const imageData = ctx.getImageData(0, 0, width, height)
	let minDistance = Infinity
	let bestX = 0
	let bestY = 0

	// On ne teste qu'un pixel sur 5 pour la perf
	for (let y = 0; y < height; y += 5) {
		for (let x = 0; x < width; x += 5) {
			const i = (y * width + x) * 4
			const pixelR = imageData.data[i]
			const pixelG = imageData.data[i + 1]
			const pixelB = imageData.data[i + 2]

			// Distance euclidienne
			const distance = Math.sqrt(
				Math.pow(r - pixelR, 2) +
				Math.pow(g - pixelG, 2) +
				Math.pow(b - pixelB, 2)
			)

			if (distance < minDistance) {
				minDistance = distance
				bestX = x
				bestY = y
			}
		}
	}
	return { x: bestX, y: bestY }
}

// Dessine le point de sélection
const drawCursor = () => {
	const ctx = canvas.value.getContext('2d')
	drawPicker()

	ctx.beginPath();
	ctx.arc(pickerX.value, pickerY.value, 3, 0, Math.PI/2 );
	ctx.strokeStyle = '#BEBEBE';
	ctx.lineWidth = 2;
	ctx.stroke();
	
	ctx.beginPath();
	ctx.arc(pickerX.value, pickerY.value, 3, Math.PI / 2, Math.PI);
	ctx.strokeStyle = '#0F0F0F';
	ctx.lineWidth = 2;
	ctx.stroke();

	ctx.beginPath();
	ctx.arc(pickerX.value, pickerY.value, 3, Math.PI , 3*Math.PI/2);
	ctx.strokeStyle = '#BEBEBE';
	ctx.lineWidth = 2;
	ctx.stroke();

	ctx.beginPath();
	ctx.arc(pickerX.value, pickerY.value, 3, 3*Math.PI/2 , 2* Math.PI);
	ctx.strokeStyle = '#0F0F0F';
	ctx.lineWidth = 2;
	ctx.stroke();

}


onMounted(() => {
	drawPicker();

	window.addEventListener('mouseup', handleMouseUp)
})

</script>

<template>
	<div class="color-picker">
	<div class="canva-wrapper">
		<canvas 
			ref="canvas" 
			class="color-picker-canva"
			width="450" 
			height="280"
			@mousedown="handleMouseDown"
			@mousemove="handleMouseMove"
		></canvas>
	</div>
	<div class="color-brightness-slider">
		<label>Luminosité</label>
		<input 
			type="range" 
			min="0" 
			max="100" 
			v-model="brightness"
			@input="drawPicker()"
		>
	</div>
	<div class="color-value form-inline">
		<div class="form-group">
			<label for="" class="form-label">Red</label>
			<input class="form-input" type="number" min="0" max="255" v-model="red" @change="changeColor">
		</div>
		<div class="form-group">
			<label for="" class="form-label">Green</label>
			<input class="form-input" type="number" min="0" max="255" v-model="green" @change="changeColor">
		</div>
		<div class="form-group">
			<label for="" class="form-label">Bleu</label>
			<input class="form-input" type="number" min="0" max="255" v-model="blue" @change="changeColor">
		</div>
	</div>
	
	<input type="hidden" v-model="color" >
	</div>
</template>
