<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import effects, { hexToRgb, rgbToHex } from '../Utilities/LedEffects.js'

const props = defineProps({
	effects: Object, // { 0: { name: 'Static', category: 'Simple' }, ... }
})

const NB_LEDS   = 30
const GLOW      = 8

const canvas      = ref(null)
const search      = ref('')
const activeId    = ref(0)
const color1      = ref('#ff0000')
const color2      = ref('#0000ff')
const color3      = ref('#00ff00')
const speed       = ref(200)

let rafId   = null
let state   = {}
let startT  = null

const categories = ['Simple', 'Sweep', 'Wipe', 'Special']

// Lookup plat { id: name } pour le header
const effectById = Object.fromEntries(
	Object.values(props.effects).flat().map(e => [e.id, e.name])
)

const filtered = (cat) => {
	const q = search.value.toLowerCase()
	return (props.effects[cat] || []).filter(e =>
		e.name.toLowerCase().includes(q) || String(e.id).includes(q)
	)
}

const selectEffect = (id) => {
	activeId.value = id
	state = {}
}

const drawLeds = (ledColors) => {
	const ctx = canvas.value?.getContext('2d')
	if (!ctx) return

	const dpr      = window.devicePixelRatio || 1
	const cssW     = canvas.value.clientWidth
	const cssH     = canvas.value.clientHeight
	const LED_GAP  = Math.max(2, Math.floor(cssW / NB_LEDS * 0.2))
	const LED_SIZE = Math.floor((cssW - LED_GAP * (NB_LEDS - 1) - GLOW * 2) / NB_LEDS)

	canvas.value.width  = cssW * dpr
	canvas.value.height = cssH * dpr
	ctx.setTransform(dpr, 0, 0, dpr, 0, 0)

	ctx.clearRect(0, 0, cssW, cssH)
	ctx.fillStyle = '#050510'
	ctx.fillRect(0, 0, cssW, cssH)

	for (let i = 0; i < NB_LEDS; i++) {
		const c = ledColors[i] || { r: 0, g: 0, b: 0 }
		const x = GLOW + i * (LED_SIZE + LED_GAP)
		const y = GLOW
		const hex = rgbToHex(c.r, c.g, c.b)
		const brightness = (c.r + c.g + c.b) / 765

		if (brightness > 0.01) {
			const grd = ctx.createRadialGradient(
				x + LED_SIZE / 2, y + LED_SIZE / 2, 0,
				x + LED_SIZE / 2, y + LED_SIZE / 2, LED_SIZE * 1.5
			)
			grd.addColorStop(0, `rgba(${c.r},${c.g},${c.b},${Math.min(1, brightness * 0.8)})`)
			grd.addColorStop(1, 'rgba(0,0,0,0)')
			ctx.fillStyle = grd
			ctx.beginPath()
			ctx.arc(x + LED_SIZE / 2, y + LED_SIZE / 2, LED_SIZE * 1.5, 0, Math.PI * 2)
			ctx.fill()
		}

		ctx.beginPath()
		ctx.arc(x + LED_SIZE / 2, y + LED_SIZE / 2, LED_SIZE / 2, 0, Math.PI * 2)
		ctx.fillStyle = hex
		ctx.fill()
	}
}

const frame = (ts) => {
	if (!startT) startT = ts
	const t = ts - startT

	const c1 = hexToRgb(color1.value)
	const c2 = hexToRgb(color2.value)
	const c3 = hexToRgb(color3.value)

	const leds = Array(NB_LEDS).fill(null).map(() => ({ r: 0, g: 0, b: 0 }))
	const fn = effects[activeId.value]
	if (fn) fn(leds, t, c1, c2, c3, speed.value, state)

	drawLeds(leds)
	rafId = requestAnimationFrame(frame)
}

watch([color1, color2, color3, activeId], () => {
	state = {}
})

onMounted(() => {
	rafId = requestAnimationFrame(frame)
})

onBeforeUnmount(() => {
	if (rafId) cancelAnimationFrame(rafId)
})
</script>

<template>
<div class="effect-preview-layout">

	<!-- Sidebar liste -->
	<div class="effect-preview-sidebar card">
		<div class="effect-preview-search">
			<input type="text" class="form-input" v-model="search" placeholder="Rechercher…">
		</div>
		<div class="effect-preview-list scrollable">
			<template v-for="cat in categories" :key="cat">
				<div v-if="filtered(cat).length" class="effect-preview-cat">
					<div class="effect-preview-cat-label">{{ cat }}</div>
					<div
						v-for="e in filtered(cat)"
						:key="e.id"
						class="effect-preview-item"
						:class="{ active: activeId === e.id }"
						@click="selectEffect(e.id)"
					>
						<span class="effect-id">{{ e.id }}</span>
						<span class="effect-name">{{ e.name }}</span>
					</div>
				</div>
			</template>
		</div>
	</div>

	<!-- Panneau preview -->
	<div class="effect-preview-main">
		<div class="card">
			<div class="card-header">
				<h3 class="glitch" :data-text="effectById[activeId]">{{ effectById[activeId] }}</h3>
				<span class="effect-id-badge">{{ activeId }}</span>
			</div>
			<div class="card-body">
				<canvas ref="canvas" class="effect-preview-canvas"></canvas>
			</div>
		</div>

		<div class="card mt-m">
			<div class="card-body">
				<div class="effect-preview-controls">
					<div class="form-group">
						<label class="form-label">Couleur 1</label>
						<input type="color" v-model="color1" class="color-input">
					</div>
					<div class="form-group">
						<label class="form-label">Couleur 2</label>
						<input type="color" v-model="color2" class="color-input">
					</div>
					<div class="form-group">
						<label class="form-label">Couleur 3</label>
						<input type="color" v-model="color3" class="color-input">
					</div>
					<div class="form-group">
						<label class="form-label">Speed (ms) {{ speed }}</label>
						<input type="range" min="50" max="2000" step="50" v-model.number="speed" class="effect-preview-range">
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
</template>
