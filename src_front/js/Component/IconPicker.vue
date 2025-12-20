<script setup>
import { ref, computed, onMounted, onBeforeUnmount  } from 'vue';

const props = defineProps({
	modelValue: {
		type: String,
		default: ''
	},
	id: {
		type: String,
		required: true
	},
	name: {
		type: String,
		required: true
	},
	required: {
		type: Boolean,
		default: false
	},
	icons: {
		type: Array,
		default: () => []
	},
	value: {
		type: String,
		default: ''
	}
})

const search = ref('')
const isOpen = ref(false)

const emit = defineEmits(['update:modelValue'])
const internalValue = ref(props.value || props.modelValue)

// v-model bidirectionnel
const selectedIcon = computed({
	get: () => internalValue.value,
	set: (newValue) => {
		if (props.icons.includes(newValue) || newValue === '') {
			internalValue.value = newValue
			emit('update:modelValue', newValue)
		}
	}
})


const matchSearch = (name) => {
	if (search.value.length == 0) {
		return true;
	}
	const escapedSearch = search.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	const regex = new RegExp(escapedSearch, 'i');
	return regex.test(name);
}

const openDropdown = () => {
	isOpen.value = true
}

const closeDropdown = () => {
	isOpen.value = false
}

const handleClickOutside = (event) => {
	if (!event.target.closest('.picker-icon')) {
		closeDropdown()
	}
}

const selectIcon = (icon) => {
	selectedIcon.value = icon
	search.value = icon
	closeDropdown()
}

onMounted(() => {
	search.value = props.value || props.modelValue;
	document.addEventListener('click', handleClickOutside)
});

onBeforeUnmount(() => {
	document.removeEventListener('click', handleClickOutside)
});

</script>

<template>
<div class="picker-icon">
	<div>
	<div class="picker-preview-icon">
		<div class="picker-icon-item" @click="openDropdown">
			<i :class="selectedIcon"></i>
		</div>
		<div v-if="isOpen" class="picker-icon-dropdown">
			<div class="picker-icon-grid">
			<template v-for="(icon,key) in icons" >
				<div v-if="matchSearch(icon)" :key="key" class="picker-icon-item" @click="selectIcon(icon)">
					<i :class="icon"></i>
				</div>
			</template>
			</div>
		</div>
	</div>
	</div>
	<div class="picker-icon-input">
		<input 
			type="text"
			class="form-input"
			v-model="search"
			@focus="openDropdown"
			placeholder="Choisir une icône..."
		>
	</div>

	<input 
      type="hidden"
      :name="name"
      :value="selectedIcon"
      :required="required"
    >
</div>
</template>