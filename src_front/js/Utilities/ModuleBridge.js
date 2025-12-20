
import mqtt from 'mqtt'


class ModuleBridge extends EventTarget {
	
	constructor(module) {
		super()
		
		this.module = module
		this.client = null
		this.connected = false
		
		// Config MQTT
		this.brokerUrl = 'ws://skankyhome.local:8083/mqtt'
		this.clientId = `web_${module.name}_${this._randomId()}`
		
		// Auto-connect
		this.connect()
	}
	
	/**
	 * Connexion au broker MQTT
	 */
	connect() {
		this.client = mqtt.connect(this.brokerUrl, {
			clientId: this.clientId,
			clean: true,
			reconnectPeriod: 1000
		})
		
		this.client.on('connect', () => {
			console.log(`✅ ModuleBridge connected: ${this.module.name}`)
			this.connected = true
			
			// S'abonner au topic de messages de l'ESP32
			this.client.subscribe(this.module.topic_message)
			
			// Event de connexion
			this.dispatchEvent(new CustomEvent('connected', {
				detail: { module: this.module }
			}))
		})
		
		this.client.on('error', (err) => {
			console.error(`❌ ModuleBridge error: ${err.message}`)
			this.connected = false
			
			this.dispatchEvent(new CustomEvent('error', {
				detail: { error: err.message }
			}))
		})
		
		this.client.on('message', (topic, message) => {
			if (topic === this.module.topic_message) {
				try {
					const data = JSON.parse(message.toString())
					
					// Event principal avec les données
					this.dispatchEvent(new CustomEvent('module-message', {
						detail: data
					}))
					
				} catch (err) {
					console.error('Failed to parse MQTT message:', err)
				}
			}
		})
		
		this.client.on('close', () => {
			console.log(`🔌 ModuleBridge disconnected: ${this.module.name}`)
			this.connected = false
			
			this.dispatchEvent(new CustomEvent('disconnected'))
		})
	}
	
	/**
	 * Envoyer une commande à l'ESP32
	 * @param {Object} payload - Commande à envoyer
	 */
	send(payload) {
		if (!this.isConnected()) {
			console.warn('ModuleBridge: Not connected, cannot send')
			return false
		}
		
		this.client.publish(
			this.module.topic_cmd,
			JSON.stringify(payload),
			{ qos: 1 }
		)
		
		console.log(`📤 Sent to ${this.module.name}:`, payload)
		return true
	}
	
	/**
	 * Vérifier si connecté
	 * @returns {boolean}
	 */
	isConnected() {
		return this.connected
	}
	
	/**
	 * Déconnexion propre
	 */
	disconnect() {
		if (this.client) {
			this.client.end()
			this.connected = false
		}
	}
	
	/**
	 * Générer un ID aléatoire
	 * @private
	 */
	_randomId() {
		return Math.random().toString(16).substr(2, 8)
	}
	
	/**
	 * Raccourci pour addEventListener
	 */
	on(event, callback) {
		this.addEventListener(event, (e) => callback(e.detail))
	}
	
	/**
	 * Raccourci pour removeEventListener
	 */
	off(event, callback) {
		this.removeEventListener(event, callback)
	}
}

export default ModuleBridge
