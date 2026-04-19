<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import mqtt from 'mqtt'

const brokerUrl = 'ws://skankyhome.local:8083/mqtt'
const topic = 'skankyhome/#'

const messages  = ref([])
const isConnected = ref(false)
const isPaused  = ref(false)
const filter    = ref('')

let client = null

const connect = () => {
	client = mqtt.connect(brokerUrl, {
		clientId: `web_monitor_${Math.random().toString(16).substr(2, 8)}`,
		clean: true,
		reconnectPeriod: 3000,
	})

	client.on('connect', () => {
		isConnected.value = true
		client.subscribe(topic)
	})

	client.on('error', () => {
		isConnected.value = false
	})

	client.on('close', () => {
		isConnected.value = false
	})

	client.on('message', (topic, payload) => {
		if (isPaused.value) return

		let data = null
		try {
			data = JSON.parse(payload.toString())
		} catch {
			data = payload.toString()
		}

		messages.value.unshift({
			id: Date.now() + Math.random(),
			time: new Date().toLocaleTimeString('fr-FR'),
			topic,
			data,
		})

		if (messages.value.length > 200) {
			messages.value.pop()
		}
	})
}

const clear = () => {
	messages.value = []
}

const filteredMessages = () => {
	if (!filter.value) return messages.value
	const q = filter.value.toLowerCase()
	return messages.value.filter(m =>
		m.topic.toLowerCase().includes(q) ||
		JSON.stringify(m.data).toLowerCase().includes(q)
	)
}

onMounted(connect)

onBeforeUnmount(() => {
	if (client) client.end()
})
</script>

<template>
<div class="mqtt-monitor">
	<div class="mqtt-monitor-toolbar">
		<div class="mqtt-monitor-status">
			<i :class="'icon-' + (isConnected ? 'success' : 'error') + ' neon-' + (isConnected ? 'success' : 'error') + '-light'"></i>
			<span>{{ isConnected ? 'Connecté' : 'Déconnecté' }}</span>
		</div>
		<input type="text" class="form-input" v-model="filter" placeholder="Filtrer topic / payload…">
		<div class="mqtt-monitor-actions">
			<button class="btn-mini btn-warning" @click="isPaused = !isPaused" :data-tooltip="isPaused ? 'Reprendre' : 'Pause'">
				<i :class="'icon-' + (isPaused ? 'play' : 'pause')"></i>
			</button>
			<button class="btn-mini btn-error" @click="clear" data-tooltip="Vider">
				<i class="icon-trash"></i>
			</button>
		</div>
	</div>

	<div class="mqtt-monitor-list">
		<div v-if="filteredMessages().length === 0" class="mqtt-monitor-empty">
			Aucun message…
		</div>
		<div v-for="msg in filteredMessages()" :key="msg.id" class="mqtt-monitor-item">
			<span class="mqtt-monitor-time">{{ msg.time }}</span>
			<span class="mqtt-monitor-topic">{{ msg.topic }}</span>
			<pre class="mqtt-monitor-payload">{{ typeof msg.data === 'string' ? msg.data : JSON.stringify(msg.data, null, 2) }}</pre>
		</div>
	</div>
</div>
</template>
