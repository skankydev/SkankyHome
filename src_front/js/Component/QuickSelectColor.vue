<script setup>
import { ref, computed, onMounted, onBeforeUnmount  } from 'vue';

const props = defineProps({
	modelValue: {
		type: String,
		default: ''
	},
	list:{
		type: Array,
		default: []
	}
})
const isOpen = ref(false)
const emit = defineEmits(['update:modelValue'])
const internalValue = ref(props.modelValue)

// v-model bidirectionnel
const selectedItem = computed({
	get: () => internalValue.value,
	set: (newValue) => {
		internalValue.value = newValue
		emit('update:modelValue', newValue)
	}
})


const selectItem = (color) => {
	selectedItem.value = color
	closeDropdown()
}

const openDropdown = () => {
	isOpen.value = true
}

const closeDropdown = () => {
	isOpen.value = false
}

const handleClickOutside = (event) => {
	if (!event.target.closest('.quick-select-color')) {
		closeDropdown()
	}
}

onMounted(() => {
	document.addEventListener('click', handleClickOutside)
});

onBeforeUnmount(() => {
	document.removeEventListener('click', handleClickOutside)
});


</script>
<template>
<div class="quick-select-color">
	<div class="picker-preview-icon">
		<div class="picker-icon-item" @click="openDropdown">
			<div class="pref-color" :style="{'background-color':internalValue}"></div>
		</div>
		<div v-if="isOpen" class="picker-icon-dropdown">
			<div class="picker-icon-grid">
				<div v-for="(prefColor,key) in list" class="pref-color" :style="{'background-color':prefColor}" @click="selectItem(prefColor)"></div>
			</div>
		</div>
	</div>
</div>
</template>
