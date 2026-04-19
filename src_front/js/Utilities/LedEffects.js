// ===== HELPERS =====

export function hexToRgb(hex) {
	if (!hex || hex === '') return { r: 0, g: 0, b: 0 }
	const h = hex.replace('#', '')
	return {
		r: parseInt(h.substring(0, 2), 16),
		g: parseInt(h.substring(2, 4), 16),
		b: parseInt(h.substring(4, 6), 16),
	}
}

export function rgbToHex(r, g, b) {
	return '#' + [r, g, b].map(v => Math.max(0, Math.min(255, Math.round(v))).toString(16).padStart(2, '0')).join('')
}

function wheel(pos) {
	pos = pos & 255
	if (pos < 85)  return { r: pos * 3,       g: 255 - pos * 3, b: 0 }
	if (pos < 170) return { r: 255 - (pos - 85) * 3, g: 0, b: (pos - 85) * 3 }
	return { r: 0, g: (pos - 170) * 3, b: 255 - (pos - 170) * 3 }
}

function blend(c1, c2, t) {
	return {
		r: Math.round(c1.r + (c2.r - c1.r) * t),
		g: Math.round(c1.g + (c2.g - c1.g) * t),
		b: Math.round(c1.b + (c2.b - c1.b) * t),
	}
}

function dim(c, f) {
	return { r: Math.round(c.r * f), g: Math.round(c.g * f), b: Math.round(c.b * f) }
}

const BLACK = { r: 0, g: 0, b: 0 }
const WHITE = { r: 255, g: 255, b: 255 }

function randomColor() {
	return { r: Math.floor(Math.random() * 256), g: Math.floor(Math.random() * 256), b: Math.floor(Math.random() * 256) }
}

function seededRandom(seed) {
	const x = Math.sin(seed + 1) * 10000
	return x - Math.floor(x)
}

// ===== EFFECTS =====
// render(leds, t_ms, c1, c2, c3, speed, state)
// leds  : array of { r, g, b } à remplir — length = nb_leds
// t_ms  : timestamp en ms
// c1/c2/c3 : objets { r, g, b }
// speed : ms par step (100–5000), plus petit = plus rapide
// state : objet persistant par effet (initialisé vide {})

const effects = [

	// 0 Static
	(leds, t, c1) => {
		leds.fill(c1)
	},

	// 1 Blink
	(leds, t, c1, c2, c3, speed) => {
		const on = Math.floor(t / speed) % 2 === 0
		leds.fill(on ? c1 : BLACK)
	},

	// 2 Breath
	(leds, t, c1, c2, c3, speed) => {
		const s = (Math.sin((t / (speed * 8)) * Math.PI * 2) + 1) / 2
		leds.fill(blend(BLACK, c1, s))
	},

	// 3 Color Wipe
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % (n * 2)
		for (let i = 0; i < n; i++) {
			leds[i] = i < step % n ? (step < n ? c1 : BLACK) : (step < n ? BLACK : c1)
		}
	},

	// 4 Color Wipe Inverse
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % (n * 2)
		for (let i = 0; i < n; i++) {
			leds[i] = i < step % n ? (step < n ? BLACK : c1) : (step < n ? c1 : BLACK)
		}
	},

	// 5 Color Wipe Reverse
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % (n * 2)
		const pos = step % n
		for (let i = 0; i < n; i++) {
			leds[i] = (n - 1 - i) < pos ? (step < n ? c1 : BLACK) : (step < n ? BLACK : c1)
		}
	},

	// 6 Color Wipe Reverse Inverse
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % (n * 2)
		const pos = step % n
		for (let i = 0; i < n; i++) {
			leds[i] = (n - 1 - i) < pos ? (step < n ? BLACK : c1) : (step < n ? c1 : BLACK)
		}
	},

	// 7 Color Wipe Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const cycle = Math.floor(t / speed)
		const pos = cycle % n
		if (!state.colors) state.colors = Array(n).fill(BLACK)
		if (cycle !== state.lastCycle) {
			if (pos === 0) state.currentColor = { r: Math.floor(seededRandom(cycle) * 256), g: Math.floor(seededRandom(cycle + 1) * 256), b: Math.floor(seededRandom(cycle + 2) * 256) }
			state.colors[pos] = state.currentColor || c1
			state.lastCycle = cycle
		}
		for (let i = 0; i < n; i++) leds[i] = state.colors[i]
	},

	// 8 Random Color
	(leds, t, c1, c2, c3, speed, state) => {
		const step = Math.floor(t / (speed * 10))
		if (step !== state.lastStep) {
			state.color = { r: Math.floor(seededRandom(step) * 256), g: Math.floor(seededRandom(step + 7) * 256), b: Math.floor(seededRandom(step + 13) * 256) }
			state.lastStep = step
		}
		leds.fill(state.color || c1)
	},

	// 9 Single Dynamic
	(leds, t, c1, c2, c3, speed, state) => {
		const step = Math.floor(t / speed)
		const n = leds.length
		if (!state.colors) state.colors = Array.from({ length: n }, (_, i) => wheel(Math.floor(seededRandom(i) * 256)))
		if (step !== state.lastStep) {
			const idx = Math.floor(seededRandom(step) * n)
			state.colors[idx] = wheel(Math.floor(seededRandom(step * 3) * 256))
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) leds[i] = state.colors[i]
	},

	// 10 Multi Dynamic
	(leds, t, c1, c2, c3, speed, state) => {
		const step = Math.floor(t / speed)
		const n = leds.length
		if (step !== state.lastStep) {
			state.colors = Array.from({ length: n }, (_, i) => wheel(Math.floor(seededRandom(step * n + i) * 256)))
			state.lastStep = step
		}
		if (!state.colors) state.colors = Array.from({ length: n }, (_, i) => wheel(i * 10))
		for (let i = 0; i < n; i++) leds[i] = state.colors[i]
	},

	// 11 Rainbow
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / (speed * 0.5)) % 256
		for (let i = 0; i < n; i++) leds[i] = wheel((Math.floor(i * 256 / n) + offset) & 255)
	},

	// 12 Rainbow Cycle
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / (speed * 0.3)) % 256
		for (let i = 0; i < n; i++) leds[i] = wheel((Math.floor(i * 256 * 5 / n) + offset) & 255)
	},

	// 13 Scan
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % (n * 2 - 2)
		const pos = step < n ? step : (n * 2 - 2 - step)
		for (let i = 0; i < n; i++) leds[i] = i === pos ? c1 : BLACK
	},

	// 14 Dual Scan
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % (n * 2 - 2)
		const pos = step < n ? step : (n * 2 - 2 - step)
		for (let i = 0; i < n; i++) leds[i] = (i === pos || i === n - 1 - pos) ? c1 : BLACK
	},

	// 15 Fade
	(leds, t, c1, c2, c3, speed) => {
		const f = (Math.sin((t / (speed * 8)) * Math.PI) + 1) / 2
		leds.fill(blend(c1, c2, f))
	},

	// 16 Theater Chase
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed) % 3
		for (let i = 0; i < n; i++) leds[i] = (i + step) % 3 === 0 ? c1 : BLACK
	},

	// 17 Theater Chase Rainbow
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		const offset = step % 256
		for (let i = 0; i < n; i++) leds[i] = (i + step) % 3 === 0 ? wheel((i + offset) & 255) : BLACK
	},

	// 18 Running Lights
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const pos = (t / speed * 0.5) % n
		for (let i = 0; i < n; i++) {
			const s = (Math.sin((i + pos) * Math.PI * 2 / 10) + 1) / 2
			leds[i] = blend(BLACK, c1, s)
		}
	},

	// 19 Twinkle
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (!state.leds) state.leds = Array(n).fill(null).map(() => BLACK)
			const idx = Math.floor(seededRandom(step) * n)
			state.leds[idx] = c1
			state.lastStep = step
		}
		if (state.leds) for (let i = 0; i < n; i++) leds[i] = state.leds[i]
		else leds.fill(BLACK)
	},

	// 20 Twinkle Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (!state.leds) state.leds = Array(n).fill(null).map(() => BLACK)
			const idx = Math.floor(seededRandom(step) * n)
			state.leds[idx] = wheel(Math.floor(seededRandom(step * 5) * 256))
			state.lastStep = step
		}
		if (state.leds) for (let i = 0; i < n; i++) leds[i] = state.leds[i]
		else leds.fill(BLACK)
	},

	// 21 Twinkle Fade
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		if (!state.leds) state.leds = Array(n).fill(0).map(() => ({ ...BLACK }))
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			const idx = Math.floor(seededRandom(step) * n)
			state.leds[idx] = { ...c1 }
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) {
			leds[i] = { ...state.leds[i] }
			state.leds[i] = dim(state.leds[i], 0.92)
		}
	},

	// 22 Twinkle Fade Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		if (!state.leds) state.leds = Array(n).fill(0).map(() => ({ ...BLACK }))
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			const idx = Math.floor(seededRandom(step) * n)
			state.leds[idx] = wheel(Math.floor(seededRandom(step * 7) * 256))
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) {
			leds[i] = { ...state.leds[i] }
			state.leds[i] = dim(state.leds[i], 0.92)
		}
	},

	// 23 Sparkle
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		leds.fill(c1)
		const step = Math.floor(t / (speed * 0.3))
		if (step !== state.lastStep) {
			state.pos = Math.floor(seededRandom(step) * n)
			state.lastStep = step
		}
		if (state.pos !== undefined) leds[state.pos] = WHITE
	},

	// 24 Flash Sparkle
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		leds.fill(c1)
		const step = Math.floor(t / (speed * 0.2))
		if (step !== state.lastStep) {
			state.positions = Array.from({ length: Math.floor(n / 10) + 1 }, (_, i) => Math.floor(seededRandom(step + i) * n))
			state.lastStep = step
		}
		if (state.positions) state.positions.forEach(p => { if (p < n) leds[p] = WHITE })
	},

	// 25 Hyper Sparkle
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		leds.fill(c1)
		const step = Math.floor(t / (speed * 0.15))
		if (step !== state.lastStep) {
			state.positions = Array.from({ length: Math.floor(n / 5) + 1 }, (_, i) => Math.floor(seededRandom(step + i * 3) * n))
			state.lastStep = step
		}
		if (state.positions) state.positions.forEach(p => { if (p < n) leds[p] = WHITE })
	},

	// 26 Strobe
	(leds, t, c1, c2, c3, speed) => {
		const on = Math.floor(t / (speed * 0.15)) % 5 === 0
		leds.fill(on ? c1 : BLACK)
	},

	// 27 Strobe Rainbow
	(leds, t, c1, c2, c3, speed, state) => {
		const step = Math.floor(t / (speed * 0.15))
		const on = step % 5 === 0
		if (on && step !== state.lastStep) {
			state.color = wheel(Math.floor(seededRandom(step) * 256))
			state.lastStep = step
		}
		leds.fill(on ? (state.color || c1) : BLACK)
	},

	// 28 Multi Strobe
	(leds, t, c1, c2, c3, speed, state) => {
		const step = Math.floor(t / (speed * 0.1))
		const burst = Math.floor(step / 10)
		const pos = step % 10
		const flashes = (burst % 8) + 1
		const on = pos < flashes * 2 && pos % 2 === 0
		if (burst !== state.lastBurst) {
			state.color = wheel(Math.floor(seededRandom(burst) * 256))
			state.lastBurst = burst
		}
		leds.fill(on ? (state.color || c1) : BLACK)
	},

	// 29 Blink Rainbow
	(leds, t, c1, c2, c3, speed, state) => {
		const step = Math.floor(t / speed)
		const on = step % 2 === 0
		if (step !== state.lastStep) {
			state.color = wheel((step * 11) & 255)
			state.lastStep = step
		}
		leds.fill(on ? (state.color || c1) : BLACK)
	},

	// 30 Chase White
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const pos = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? WHITE : c1
	},

	// 31 Chase Color
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const pos = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? c1 : BLACK
	},

	// 32 Chase Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			state.color = wheel(Math.floor(seededRandom(step * 3) * 256))
			state.lastStep = step
		}
		const pos = step % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? (state.color || c1) : BLACK
	},

	// 33 Chase Rainbow
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		const pos = step % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? wheel((step * 7) & 255) : BLACK
	},

	// 34 Chase Flash
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / (speed * 0.4))
		const pos = Math.floor(step / 4) % n
		const flash = step % 4 < 2
		for (let i = 0; i < n; i++) leds[i] = i === pos ? (flash ? WHITE : c1) : c1
	},

	// 35 Chase Flash Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / (speed * 0.4))
		const pos = Math.floor(step / 4) % n
		if (Math.floor(step / 4) !== state.lastGroup) {
			state.color = wheel(Math.floor(seededRandom(step) * 256))
			state.lastGroup = Math.floor(step / 4)
		}
		const flash = step % 4 < 2
		for (let i = 0; i < n; i++) leds[i] = i === pos ? (flash ? WHITE : (state.color || c1)) : BLACK
	},

	// 36 Chase Rainbow White
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		const pos = step % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? WHITE : wheel((i * 256 / n + step * 3) & 255)
	},

	// 37 Chase Blackout
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const pos = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? BLACK : c1
	},

	// 38 Chase Blackout Rainbow
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		const pos = step % n
		for (let i = 0; i < n; i++) leds[i] = i === pos ? BLACK : wheel((i * 256 / n + step * 3) & 255)
	},

	// 39 Color Sweep Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		const pos = step % n
		if (step < n && state.lastPos > pos) {
			state.color = wheel(Math.floor(seededRandom(Math.floor(step / n)) * 256))
		}
		state.lastPos = pos
		for (let i = 0; i < n; i++) leds[i] = i <= pos ? (state.color || c1) : BLACK
	},

	// 40 Running Color
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = (i + offset) % 4 < 2 ? c1 : c2
	},

	// 41 Running Red Blue
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = (i + offset) % 4 < 2 ? { r: 255, g: 0, b: 0 } : { r: 0, g: 0, b: 255 }
	},

	// 42 Running Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (!state.leds) state.leds = Array.from({ length: n }, (_, i) => wheel(Math.floor(seededRandom(i) * 256)))
		if (step !== state.lastStep) {
			state.leds.unshift(wheel(Math.floor(seededRandom(step * 17) * 256)))
			state.leds.pop()
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) leds[i] = state.leds[i]
	},

	// 43 Larson Scanner
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const cycle = n * 2 - 2
		const pos = Math.floor(t / speed) % cycle
		const center = pos < n ? pos : cycle - pos
		for (let i = 0; i < n; i++) {
			const dist = Math.abs(i - center)
			if (dist === 0) leds[i] = c1
			else if (dist === 1) leds[i] = dim(c1, 0.5)
			else if (dist === 2) leds[i] = dim(c1, 0.2)
			else leds[i] = BLACK
		}
	},

	// 44 Comet
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const tail = Math.floor(n / 4)
		const pos = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) {
			const dist = (pos - i + n) % n
			if (dist === 0) leds[i] = c1
			else if (dist < tail) leds[i] = dim(c1, 1 - dist / tail)
			else leds[i] = BLACK
		}
	},

	// 45 Fireworks
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		if (!state.sparks) state.sparks = []
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (Math.random() < 0.3) {
				const center = Math.floor(seededRandom(step * 3) * n)
				for (let i = -3; i <= 3; i++) {
					const idx = center + i
					if (idx >= 0 && idx < n) {
						if (!state.brightMap) state.brightMap = Array(n).fill(0)
						state.brightMap[idx] = 1 - Math.abs(i) / 4
					}
				}
			}
			state.lastStep = step
		}
		if (!state.brightMap) state.brightMap = Array(n).fill(0)
		for (let i = 0; i < n; i++) {
			leds[i] = dim(c1, state.brightMap[i])
			state.brightMap[i] = Math.max(0, state.brightMap[i] - 0.05)
		}
	},

	// 46 Fireworks Random
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (!state.brightMap) state.brightMap = Array(n).fill(0)
		if (!state.colorMap) state.colorMap = Array(n).fill(null).map(() => BLACK)
		if (step !== state.lastStep) {
			if (seededRandom(step) < 0.3) {
				const center = Math.floor(seededRandom(step * 3) * n)
				const c = wheel(Math.floor(seededRandom(step * 7) * 256))
				for (let i = -3; i <= 3; i++) {
					const idx = center + i
					if (idx >= 0 && idx < n) {
						state.brightMap[idx] = 1 - Math.abs(i) / 4
						state.colorMap[idx] = c
					}
				}
			}
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) {
			leds[i] = dim(state.colorMap[i] || c1, state.brightMap[i])
			state.brightMap[i] = Math.max(0, state.brightMap[i] - 0.05)
		}
	},

	// 47 Merry Christmas
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = (i + offset) % 4 < 2 ? { r: 255, g: 0, b: 0 } : { r: 0, g: 128, b: 0 }
	},

	// 48 Fire Flicker
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			state.vals = Array.from({ length: n }, (_, i) => 0.4 + seededRandom(step * n + i) * 0.6)
			state.lastStep = step
		}
		const vals = state.vals || []
		for (let i = 0; i < n; i++) leds[i] = dim({ r: 255, g: 80, b: 0 }, vals[i] ?? 0.7)
	},

	// 49 Fire Flicker (soft)
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / (speed * 2))
		if (step !== state.lastStep) {
			state.vals = Array.from({ length: n }, (_, i) => 0.6 + seededRandom(step * n + i) * 0.4)
			state.lastStep = step
		}
		const vals = state.vals || []
		for (let i = 0; i < n; i++) leds[i] = dim({ r: 255, g: 60, b: 0 }, vals[i] ?? 0.8)
	},

	// 50 Fire Flicker (intense)
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / (speed * 0.5))
		if (step !== state.lastStep) {
			state.vals = Array.from({ length: n }, (_, i) => seededRandom(step * n + i))
			state.lastStep = step
		}
		const vals = state.vals || []
		for (let i = 0; i < n; i++) leds[i] = dim({ r: 255, g: 100, b: 10 }, vals[i] ?? 0.5)
	},

	// 51 Circus Combustus
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		const offset = step % 3
		const colors = [{ r: 255, g: 0, b: 0 }, WHITE, { r: 0, g: 0, b: 0 }]
		for (let i = 0; i < n; i++) leds[i] = colors[(i + offset) % 3]
	},

	// 52 Halloween
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = (i + offset) % 4 < 2 ? { r: 255, g: 80, b: 0 } : { r: 128, g: 0, b: 128 }
	},

	// 53 Bicolor Chase
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / speed) % n
		for (let i = 0; i < n; i++) leds[i] = (i + offset) % 3 === 0 ? c1 : c2
	},

	// 54 Tricolor Chase
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const offset = Math.floor(t / speed) % n
		const colors = [c1, c2, c3]
		for (let i = 0; i < n; i++) leds[i] = colors[(i + offset) % 3]
	},

	// 55 TwinkleFOX
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		for (let i = 0; i < n; i++) {
			const s = (Math.sin(t / (speed * 2) + i * 0.7) + 1) / 2
			leds[i] = blend(c2, c1, s * s)
		}
	},

	// 56 Rain
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		if (!state.drops) state.drops = []
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (!state.bright) state.bright = Array(n).fill(0)
			if (seededRandom(step) < 0.3) {
				state.bright[Math.floor(seededRandom(step * 7) * n)] = 1
			}
			for (let i = 0; i < n; i++) state.bright[i] = Math.max(0, state.bright[i] - 0.15)
			state.lastStep = step
		}
		const bright = state.bright || []
		for (let i = 0; i < n; i++) leds[i] = dim(c1, bright[i] || 0)
	},

	// 57 Block Dissolve
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (!state.mask || step !== state.lastStep) {
			if (!state.mask) {
				state.mask = Array(n).fill(false)
				state.current = c1
				state.next = c2
			}
			const idx = Math.floor(seededRandom(step) * n)
			if (!state.mask[idx]) {
				state.mask[idx] = true
				if (state.mask.every(Boolean)) {
					state.mask = Array(n).fill(false)
					const tmp = state.current
					state.current = state.next
					state.next = tmp
				}
			}
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) leds[i] = state.mask[i] ? state.next : state.current
	},

	// 58 ICU
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const half = Math.floor(n / 2)
		const cycle = half * 2 - 2
		const step = Math.floor(t / speed) % cycle
		const pos = step < half ? step : cycle - step
		for (let i = 0; i < n; i++) leds[i] = BLACK
		leds[pos] = c1
		leds[n - 1 - pos] = c1
		if (pos > 0) { leds[pos - 1] = dim(c1, 0.3); leds[n - pos] = dim(c1, 0.3) }
	},

	// 59 Dual Larson
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const cycle = n * 2 - 2
		const step = Math.floor(t / speed)
		const p1 = step % cycle
		const p2 = (step + Math.floor(cycle / 2)) % cycle
		const center1 = p1 < n ? p1 : cycle - p1
		const center2 = p2 < n ? p2 : cycle - p2
		for (let i = 0; i < n; i++) {
			const d1 = Math.abs(i - center1)
			const d2 = Math.abs(i - center2)
			const b1 = d1 === 0 ? 1 : d1 === 1 ? 0.5 : d1 === 2 ? 0.2 : 0
			const b2 = d2 === 0 ? 1 : d2 === 1 ? 0.5 : d2 === 2 ? 0.2 : 0
			leds[i] = blend(dim(c2, b2), dim(c1, b1), b1 > 0 ? 0 : 1)
			if (b1 > 0 && b2 > 0) leds[i] = blend(dim(c1, b1), dim(c2, b2), 0.5)
			else if (b1 > 0) leds[i] = dim(c1, b1)
			else leds[i] = dim(c2, b2)
		}
	},

	// 60 Running Random2
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (!state.leds) state.leds = Array.from({ length: n }, (_, i) => ({ r: 0, g: 0, b: 0 }))
		if (step !== state.lastStep) {
			state.leds.pop()
			const r = seededRandom(step * 13)
			const c = r < 0.33 ? c1 : r < 0.66 ? c2 : c3
			state.leds.unshift({ ...c })
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) leds[i] = state.leds[i] || BLACK
	},

	// 61 Filler Up
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (!state.filled) state.filled = 0
		if (step !== state.lastStep) {
			state.filled = (state.filled + 1) % (n + 1)
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) leds[i] = (n - 1 - i) < state.filled ? c1 : BLACK
	},

	// 62 Rainbow Larson
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const cycle = n * 2 - 2
		const step = Math.floor(t / speed)
		const pos = step % cycle
		const center = pos < n ? pos : cycle - pos
		for (let i = 0; i < n; i++) {
			const dist = Math.abs(i - center)
			const c = wheel(((i * 256 / n) + step * 3) & 255)
			if (dist === 0) leds[i] = c
			else if (dist === 1) leds[i] = dim(c, 0.5)
			else if (dist === 2) leds[i] = dim(c, 0.2)
			else leds[i] = BLACK
		}
	},

	// 63 Rainbow Fireworks
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		if (!state.bright) state.bright = Array(n).fill(0)
		if (!state.colors) state.colors = Array(n).fill(null).map(() => BLACK)
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (seededRandom(step) < 0.25) {
				const center = Math.floor(seededRandom(step * 5) * n)
				const c = wheel(Math.floor(seededRandom(step * 11) * 256))
				for (let i = Math.max(0, center - 3); i <= Math.min(n - 1, center + 3); i++) {
					state.bright[i] = 1 - Math.abs(i - center) / 4
					state.colors[i] = c
				}
			}
			for (let i = 0; i < n; i++) state.bright[i] = Math.max(0, state.bright[i] - 0.05)
			state.lastStep = step
		}
		for (let i = 0; i < n; i++) leds[i] = dim(state.colors[i] || BLACK, state.bright[i])
	},

	// 64 Trifade
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const period = speed * 12
		const phase = (t % period) / period
		let color
		if (phase < 0.33) color = blend(c1, c2, phase / 0.33)
		else if (phase < 0.66) color = blend(c2, c3, (phase - 0.33) / 0.33)
		else color = blend(c3, c1, (phase - 0.66) / 0.34)
		leds.fill(color)
	},

	// 65 VU Meter
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const level = (Math.sin(t / (speed * 3)) + 1) / 2
		const lit = Math.floor(level * n)
		for (let i = 0; i < n; i++) {
			if (i >= n - lit) {
				const f = i / n
				leds[i] = f > 0.8 ? { r: 255, g: 0, b: 0 } : f > 0.6 ? { r: 255, g: 165, b: 0 } : { r: 0, g: 255, b: 0 }
			} else {
				leds[i] = BLACK
			}
		}
	},

	// 66 Heartbeat
	(leds, t, c1, c2, c3, speed) => {
		const period = speed * 8
		const p = (t % period) / period
		let brightness
		if (p < 0.1) brightness = p / 0.1
		else if (p < 0.2) brightness = 1 - (p - 0.1) / 0.1 * 0.6
		else if (p < 0.3) brightness = 0.4 + (p - 0.2) / 0.1 * 0.6
		else if (p < 0.45) brightness = 1 - (p - 0.3) / 0.15
		else brightness = 0
		leds.fill(dim(c1, brightness))
	},

	// 67 Bits
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (!state.bits) state.bits = Array(n).fill(false)
			const idx = Math.floor(seededRandom(step * 19) * n)
			state.bits[idx] = !state.bits[idx]
			state.lastStep = step
		}
		const bits = state.bits || []
		for (let i = 0; i < n; i++) leds[i] = bits[i] ? c1 : BLACK
	},

	// 68 Multi Comet
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		const tail = Math.floor(n / 6)
		const numComets = 3
		for (let i = 0; i < n; i++) leds[i] = BLACK
		const colors = [c1, c2, c3]
		for (let c = 0; c < numComets; c++) {
			const offset = Math.floor(n / numComets) * c
			const pos = (Math.floor(t / speed) + offset) % n
			for (let j = 0; j < tail; j++) {
				const idx = (pos - j + n) % n
				const existing = leds[idx]
				const newC = dim(colors[c % colors.length], 1 - j / tail)
				leds[idx] = { r: Math.min(255, existing.r + newC.r), g: Math.min(255, existing.g + newC.g), b: Math.min(255, existing.b + newC.b) }
			}
		}
	},

	// 69 Flipbook
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			if (!state.frame) state.frame = Array(n).fill(null).map(() => ({ ...BLACK }))
			for (let i = 0; i < n; i++) {
				state.frame[i] = seededRandom(step * n + i) > 0.5 ? { ...c1 } : { ...BLACK }
			}
			state.lastStep = step
		}
		if (state.frame) for (let i = 0; i < n; i++) leds[i] = state.frame[i]
		else leds.fill(BLACK)
	},

	// 70 Popcorn
	(leds, t, c1, c2, c3, speed, state) => {
		const n = leds.length
		if (!state.bright) state.bright = Array(n).fill(0)
		const step = Math.floor(t / speed)
		if (step !== state.lastStep) {
			for (let i = 0; i < n; i++) state.bright[i] = Math.max(0, state.bright[i] - 0.1)
			if (seededRandom(step) < 0.2) {
				state.bright[Math.floor(seededRandom(step * 11) * n)] = 1
			}
			state.lastStep = step
		}
		const colors = [c1, c2, c3]
		for (let i = 0; i < n; i++) leds[i] = dim(colors[i % 3], state.bright[i])
	},

	// 71 Oscillator
	(leds, t, c1, c2, c3, speed) => {
		const n = leds.length
		for (let i = 0; i < n; i++) {
			const s1 = (Math.sin(t / (speed * 4) + i * 0.3) + 1) / 2
			const s2 = (Math.sin(t / (speed * 6) + i * 0.5 + 1.5) + 1) / 2
			const r = Math.round(c1.r * s1 + c2.r * s2 * 0.5)
			const g = Math.round(c1.g * s1 + c2.g * s2 * 0.5)
			const b = Math.round(c1.b * s1 + c2.b * s2 * 0.5)
			leds[i] = { r: Math.min(255, r), g: Math.min(255, g), b: Math.min(255, b) }
		}
	},
]

export default effects
