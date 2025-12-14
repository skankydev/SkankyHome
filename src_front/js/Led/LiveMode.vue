<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import mqtt from 'mqtt';
import ColorPicker from '../Component/ColorPicker.vue';

const props = defineProps({
	effects: Array,
})

const client = ref(null);
const connected = ref(false);
const status = ref('Déconnecté');
const search = ref('');

const currentColor = ref('#FFFFFF');
const currentEffect = ref('none');

const connectMQTT = () => {
	client.value = mqtt.connect('ws://skankyhome.local:8083/mqtt', {
		clientId: 'web_client_' + Math.random().toString(16).substr(2, 8),
		clean: true,
		reconnectPeriod: 1000,
	});
	
	client.value.on('connect', () => {
		console.log('✅ Connected to MQTT');
		connected.value = true;
		status.value = 'Connecté';
		client.value.subscribe('skankyhome/cmd/led_master');
	});
	
	client.value.on('error', (err) => {
		console.error('❌ MQTT Error:', err);
		status.value = 'Erreur: ' + err.message;
	});
	
	client.value.on('message', (topic, message) => {
		console.log('📨 Received:', topic, message.toString());
		if (topic === 'skankyhome/message/led_master') {
			const data = JSON.parse(message.toString());
			console.log('Status:', data);
		}
	});
	
	client.value.on('close', () => {
		connected.value = false;
		status.value = 'Déconnecté';
	});
};

const sendCommand = (command, value) => {
	if (!connected.value) {
		alert('MQTT non connecté !');
		return;
	}
	
	const payload = { [command]: value };
	console.log('📤 Sending:', payload);
	
	client.value.publish(
		'skankyhome/cmd/led_master',
		JSON.stringify(payload),
		{ qos: 1 }
	);
};

const changeColor = (color) => {
	sendCommand('color', color);
};

const changeEffect = (effect) => {
	sendCommand('effect', effect);
};

const matchSearch = (name) => {
	if (search.value.length == 0) {
		return true;
	}
	const escapedSearch = search.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	const regex = new RegExp(escapedSearch, 'i');
	return regex.test(name);
}

onMounted(() => {
	connectMQTT();
});

onBeforeUnmount(() => {
	if (client.value) {
		client.value.end();
	}
});
</script>

<template>
<div class="grid-layout">
	<div class="grid-half card">
		<div class="card-header">
			<h2 class="corner-accent-primary">Color</h2>
		</div>
		<div class="card-body">
			<div class="form-group ">
				<label for="Color" class="form-label">Couleur</label>
				<ColorPicker 
					v-model="currentColor"
					@update:modelValue="changeColor"
				/>
			</div>
		</div>
	</div>
	<div class="grid-half card">
		<div class="card-header">
			<h2 class="corner-accent-primary">Effect</h2>
		</div>
		<div>
			<div class="form-group ph-m">
				<label for="" class="form-label"></label>
				<input type="text" class="form-input" v-model="search">
			</div>
			<div class="effects-list pb-l">
				<div class="scrollable">
					<template v-for="(name,key) in props.effects">
						<div v-if="matchSearch(name)" class="effect" @click="changeEffect(key)">
							<div class="effect-key">{{key}} </div>
							<div class="color-cyan"> => </div>
							<div class="effect-name"> {{name}} </div>
						</div>
					</template>
				</div>
			</div>
		</div>
	</div>
</div>
</template>
