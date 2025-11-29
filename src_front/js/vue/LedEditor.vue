<template>
	<div class="counter">
		<h2>Coucou</h2>
		<p>Compteur: {{ count }}</p>
		<button @click="count++">+1</button>
	</div>
</template>

<script setup>
import { ref } from 'vue'

const count = ref(0)

</script>