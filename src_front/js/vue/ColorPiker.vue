<script setup>
import { ref } from 'vue'

const props = defineProps({
	oldColor: String
})


const color = ref('#000000')

const colorChange = () => {
	console.log('color', color.value)
}
console.log("ColorPiker")
</script>

<template>
<div class="led-editor">
	<h2>ColorPiker</h2>
	<input type="color" v-model="color" @change="colorChange">
</div>
</template>