<script setup>
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
	conversation: Object,
	persona: Object,
	endpoint: String, // POST PHP : un tour de chat (le system prompt est assemblé côté serveur)
})

// Historique persisté rechargé au montage ; on n'envoie plus le contexte, le serveur le tient.
const messages = ref((props.conversation.messages || []).map(m => ({ role: m.role, content: m.content })))
const input = ref('')
const loading = ref(false)
const error = ref('')
const threadRef = ref(null)

const formatTime = () => new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })

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

	// Bulle assistant vide (animation '…') le temps de la réponse
	const assistant = { role: 'assistant', content: '', time: '', duration: '' }
	messages.value.push(assistant)
	await scrollDown()
	const startedAt = Date.now()

	try {
		// CSRF (X-CSRF-Token) injecté automatiquement par le wrapper fetch de app.js
		const response = await fetch(props.endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ message: text }),
		})

		const data = await response.json().catch(() => ({}))
		if (!response.ok) {
			throw new Error(data.error || ('HTTP ' + response.status))
		}

		assistant.content = data.message?.content || '(réponse vide)'
		assistant.time = formatTime()
		assistant.duration = ((Date.now() - startedAt) / 1000).toFixed(1)
	} catch (e) {
		error.value = 'Réponse impossible (' + e.message + ')'
		const idx = messages.value.indexOf(assistant)
		if (idx !== -1) {
			messages.value.splice(idx, 1)
		}
	} finally {
		loading.value = false
		await scrollDown()
	}
}

onMounted(scrollDown)
</script>

<template>
<div class="persona-chat card">
	<header class="card-header persona-chat-header">
		<h3 class="rainbow-underline" :data-text="props.persona?.name">{{ props.persona?.name }}</h3>
	</header>

	<div class="persona-chat-thread" ref="threadRef">
		<div v-if="messages.length === 0" class="persona-chat-empty">
			<i class="icon-message-circle"></i>
			<p>Démarre la conversation avec <strong>{{ props.persona?.name }}</strong></p>
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
			</div>
			<div v-if="message.time" class="persona-chat-meta">
				<i class="icon-clock"></i> {{ message.time }}
				<span v-if="message.duration"> · {{ message.duration }}s</span>
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
