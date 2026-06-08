<script setup>
import { ref, nextTick, onBeforeUnmount } from 'vue';

const props = defineProps({
	persona: Object,
	llamaUrl: String,
})

// Endpoint compatible OpenAI exposé par llama-server
const endpoint = props.llamaUrl.replace(/\/+$/, '') + '/v1/chat/completions'

// Fil de discussion affiché (sans le system prompt, qui est implicite)
const messages = ref([])
const input = ref('')
const loading = ref(false)
const error = ref('')
const threadRef = ref(null)

// Paramètres d'inférence par défaut (à remonter dans le persona plus tard si besoin)
const params = {
	temperature: 0.7,
	max_tokens: 1024,
}

const formatTime = () => new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })

const buildPayload = () => {
	return {
		model: 'local',
		messages: [
			{ role: 'system', content: props.persona.content },
			...messages.value.map(m => ({ role: m.role, content: m.content })),
		],
		stream: true,
		...params,
	}
}

const scrollDown = async () => {
	await nextTick()
	if (threadRef.value) {
		threadRef.value.scrollTop = threadRef.value.scrollHeight
	}
}

const send = async () => {
	const text = input.value.trim()
	if (!text || loading.value) {
		return
	}

	error.value = ''
	messages.value.push({ role: 'user', content: text, time: formatTime() })
	input.value = ''
	loading.value = true

	// Payload construit AVANT la bulle vide (sinon on enverrait un message assistant vide)
	const body = JSON.stringify(buildPayload())
	const startedAt = Date.now()

	// Bulle assistant qu'on remplit au fil du flux ('…' tant qu'aucun token n'est arrivé)
	messages.value.push({ role: 'assistant', content: '', truncated: false, time: '', duration: '' })
	const assistant = messages.value[messages.value.length - 1]
	await scrollDown()

	try {
		const response = await fetch(endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body,
		})

		if (!response.ok) {
			throw new Error('HTTP ' + response.status)
		}

		// Lecture du flux SSE : suite de lignes "data: {…}" séparées par des sauts de ligne,
		// chaque chunk apporte un bout de texte dans choices[0].delta.content, fin = "data: [DONE]"
		const reader = response.body.getReader()
		const decoder = new TextDecoder('utf-8')
		let buffer = ''

		while (true) {
			const { done, value } = await reader.read()
			if (done) {
				break
			}

			buffer += decoder.decode(value, { stream: true })
			const lines = buffer.split('\n')
			buffer = lines.pop() // dernière ligne potentiellement incomplète

			for (const line of lines) {
				const trimmed = line.trim()
				if (!trimmed.startsWith('data:')) {
					continue
				}

				const payload = trimmed.slice(5).trim()
				if (payload === '' || payload === '[DONE]') {
					continue
				}

				try {
					const choice = JSON.parse(payload).choices?.[0]
					if (choice?.delta?.content) {
						assistant.content += choice.delta.content
						scrollDown()
					}
					if (choice?.finish_reason) {
						console.log('🦙 finish_reason:', choice.finish_reason)
						if (choice.finish_reason === 'length') {
							assistant.truncated = true
						}
					}
				} catch (err) {
					console.warn('Chunk SSE non parsable:', payload)
				}
			}
		}

		if (assistant.content === '') {
			assistant.content = '(réponse vide)'
		}
		assistant.time = formatTime()
		assistant.duration = ((Date.now() - startedAt) / 1000).toFixed(1)
	} catch (e) {
		error.value = 'Connexion au serveur llama impossible (' + e.message + ')'
		// On retire la bulle restée vide
		const idx = messages.value.indexOf(assistant)
		if (idx !== -1 && assistant.content === '') {
			messages.value.splice(idx, 1)
		}
	} finally {
		loading.value = false
		await scrollDown()
	}
}

const reset = () => {
	messages.value = []
	error.value = ''
	stopSpeech()
}

// ===== Synthèse vocale (TTS) via Kokoro (kokoro-js, tourne dans le navigateur) =====
const KOKORO_MODEL = 'onnx-community/Kokoro-82M-v1.0-ONNX'
const KOKORO_VOICE = 'ff_siwis' // voix française

const ttsSupported = typeof window !== 'undefined' && typeof WebAssembly === 'object'
const speakingKey = ref(null)  // clé du message en cours de lecture
const ttsLoading = ref(false)  // chargement du modèle ou génération en cours

// Instance Kokoro chargée paresseusement (au 1er clic) puis mise en cache
let ttsInstance = null
let ttsLoadingPromise = null
let currentAudio = null

// Retire les emojis du texte à lire (l'affichage garde les emojis, c'est juste pour la voix)
const stripEmoji = (text) => text
	.replace(/\p{Extended_Pictographic}/gu, '')   // pictogrammes (😊, 💙, 🦙…)
	.replace(/[\u{1F1E6}-\u{1F1FF}]/gu, '')        // drapeaux
	.replace(/[\u{FE00}-\u{FE0F}\u{200D}]/gu, '')  // sélecteurs de variante + liant ZWJ
	.replace(/\s{2,}/g, ' ')                        // espaces multiples laissés par les emojis
	.trim()

// Charge le modèle Kokoro une seule fois (import dynamique => pas dans le bundle de la page)
const getTts = async () => {
	if (ttsInstance) {
		return ttsInstance
	}
	if (!ttsLoadingPromise) {
		ttsLoadingPromise = (async () => {
			const { KokoroTTS } = await import('kokoro-js')
			const device = (typeof navigator !== 'undefined' && navigator.gpu) ? 'webgpu' : 'wasm'
			console.log('🦜 Chargement de Kokoro sur', device, '…')
			ttsInstance = await KokoroTTS.from_pretrained(KOKORO_MODEL, { dtype: 'q8', device })
			return ttsInstance
		})()
	}
	return ttsLoadingPromise
}

const stopSpeech = () => {
	if (currentAudio) {
		currentAudio.pause()
		currentAudio.src = ''
		currentAudio = null
	}
	speakingKey.value = null
}

const speak = async (message, key) => {
	if (!ttsSupported) {
		return
	}

	// Re-clic sur le message en cours → on arrête
	if (speakingKey.value === key) {
		stopSpeech()
		return
	}

	stopSpeech()          // coupe une éventuelle lecture précédente
	speakingKey.value = key
	ttsLoading.value = true

	try {
		const tts = await getTts()
		// L'utilisateur a pu cliquer ailleurs / stopper pendant le chargement
		if (speakingKey.value !== key) {
			return
		}

		const audio = await tts.generate(stripEmoji(message.content), { voice: KOKORO_VOICE })
		if (speakingKey.value !== key) {
			return
		}

		const url = URL.createObjectURL(audio.toBlob())
		const el = new Audio(url)
		currentAudio = el
		el.onended = () => {
			URL.revokeObjectURL(url)
			if (currentAudio === el) currentAudio = null
			if (speakingKey.value === key) speakingKey.value = null
		}
		el.onerror = () => {
			URL.revokeObjectURL(url)
			if (speakingKey.value === key) speakingKey.value = null
		}
		await el.play()
	} catch (e) {
		console.error('Kokoro TTS:', e)
		error.value = 'Synthèse vocale (Kokoro) impossible : ' + e.message
		speakingKey.value = null
	} finally {
		ttsLoading.value = false
	}
}

onBeforeUnmount(() => {
	stopSpeech()
})
</script>

<template>
<div class="persona-chat card">
	<header class="card-header persona-chat-header">
		<h3 class="glitch" :data-text="props.persona.name">{{ props.persona.name }}</h3>
		<button type="button" class="btn-mini btn-error" @click="reset" data-tooltip="Réinitialiser la conversation">
			<i class="icon-trash"></i>
		</button>
	</header>

	<div class="persona-chat-thread" ref="threadRef">
		<div v-if="messages.length === 0" class="persona-chat-empty">
			<i class="icon-message-circle"></i>
			<p>Démarre la conversation avec <strong>{{ props.persona.name }}</strong></p>
		</div>

		<div
			v-for="(message, key) in messages"
			:key="key"
			class="persona-chat-message"
			:class="'is-' + message.role"
		>
			<div class="persona-chat-bubble">
				<template v-if="message.content">{{ message.content }}</template>
				<span v-else class="persona-chat-typing">
					<span class="dot"></span>
					<span class="dot"></span>
					<span class="dot"></span>
				</span>
				<span v-if="message.truncated" class="persona-chat-truncated" data-tooltip="Réponse coupée : plafond de tokens atteint">
					<i class="icon-alert-triangle"></i> tronqué
				</span>
			</div>
			<div v-if="message.time" class="persona-chat-meta">
				<i class="icon-clock"></i> {{ message.time }}
				<span v-if="message.duration"> · {{ message.duration }}s</span>
				<button
					v-if="ttsSupported && message.role === 'assistant' && message.content"
					type="button"
					class="persona-chat-speak"
					:class="{ 'is-speaking': speakingKey === key }"
					@click="speak(message, key)"
					:data-tooltip="speakingKey === key ? (ttsLoading ? 'Chargement…' : 'Arrêter') : 'Écouter'"
				>
					<i v-if="speakingKey === key && ttsLoading" class="icon-loader persona-chat-spin"></i>
					<i v-else-if="speakingKey === key" class="icon-stop-circle"></i>
					<i v-else class="icon-volume-2"></i>
				</button>
			</div>
		</div>
	</div>

	<div v-if="error" class="persona-chat-error flash-error">
		<i class="icon-error"></i> {{ error }}
	</div>

	<form class="persona-chat-input" @submit.prevent="send">
		<textarea
			class="form-input"
			v-model="input"
			placeholder="Écris un message…  (Entrée pour envoyer, Maj+Entrée pour un saut de ligne)"
			rows="2"
			:disabled="loading"
			@keydown.enter.exact.prevent="send"
		></textarea>
		<button type="submit" class="btn-success" :disabled="loading || input.trim().length === 0">
			<i class="icon-send"></i>
		</button>
	</form>
</div>
</template>
