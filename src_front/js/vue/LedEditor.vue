<script setup>
import { ref } from 'vue'

const count = ref(0)

console.log("LedEditor")
</script>

<template>
<div class="led-editor">
	<h2>Coucou</h2>
	<p>Compteur: {{ count }}</p>
	<button @click="count++">+1</button>
</div>
</template>