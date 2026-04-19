<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import effects, { hexToRgb, rgbToHex } from '../Utilities/LedEffects.js'

const props = defineProps({
	effectId: { type: Number, default: null },
})

const NB_LEDS = 20
const LED_R   = 9   // rayon du cercle
const LED_GAP = 4
const GLOW    = LED_R

const C1 = hexToRgb('#ff0000')
const C2 = hexToRgb('#0000ff')
const C3 = hexToRgb('#00ff00')
const SPEED = 200

const canvas = ref(null)
let rafId  = null
let state  = {}
let startT = null

const drawLeds = (ledColors) => {
	const ctx = canvas.value?.getContext('2d')
	if (!ctx) return

	const dpr   = window.devicePixelRatio || 1
	const cssW  = canvas.value.clientWidth 
	const cssH  = canvas.value.clientHeight

	canvas.value.width  = cssW * dpr
	canvas.value.height = cssH * dpr
	ctx.setTransform(dpr, 0, 0, dpr, 0, 0)

	ctx.clearRect(0, 0, cssW, cssH)

	const totalW    = NB_LEDS * (LED_R * 2 + LED_GAP) - LED_GAP
	const offsetX   = (cssW - totalW) / 2
	const centerY   = cssH / 2

	for (let i = 0; i < NB_LEDS; i++) {
		const c = ledColors[i] || { r: 0, g: 0, b: 0 }
		const cx = offsetX + i * (LED_R * 2 + LED_GAP) + LED_R
		const brightness = (c.r + c.g + c.b) / 765

		// Halo
		if (brightness > 0.01) {
			const grd = ctx.createRadialGradient(cx, centerY, 0, cx, centerY, LED_R * 2.5)
			grd.addColorStop(0, `rgba(${c.r},${c.g},${c.b},${Math.min(1, brightness * 0.7)})`)
			grd.addColorStop(1, 'rgba(0,0,0,0)')
			ctx.fillStyle = grd
			ctx.beginPath()
			ctx.arc(cx, centerY, LED_R * 2.5, 0, Math.PI * 2)
			ctx.fill()
		}

		// LED
		ctx.beginPath()
		ctx.arc(cx, centerY, LED_R, 0, Math.PI * 2)
		ctx.fillStyle = rgbToHex(c.r, c.g, c.b)
		ctx.fill()
	}
}

const frame = (ts) => {
	if (!startT) startT = ts
	const t = ts - startT

	const leds = Array(NB_LEDS).fill(null).map(() => ({ r: 0, g: 0, b: 0 }))
	const fn = effects[props.effectId]
	if (fn) fn(leds, t, C1, C2, C3, SPEED, state)

	drawLeds(leds)
	rafId = requestAnimationFrame(frame)
}

const start = () => {
	if (rafId) cancelAnimationFrame(rafId)
	state  = {}
	startT = null
	rafId  = requestAnimationFrame(frame)
}

const stop = () => {
	if (rafId) cancelAnimationFrame(rafId)
	rafId = null
}

watch(() => props.effectId, (val) => {
	if (val !== null) start()
	else stop()
})

onMounted(() => {
	if (props.effectId !== null) start()
})

onBeforeUnmount(stop)
</script>

<template>
<div class="mini-effect-preview">
	<canvas ref="canvas" class="mini-effect-canvas"></canvas>
</div>
</template>
