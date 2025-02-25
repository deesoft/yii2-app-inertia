<template>
	<v-snackbar :color="state.color" :timeout="state.timer" v-model="state.showSnackbar" bottom right class="text-white"
		style="z-index:99999">
		<v-icon left>{{ state.icon }}</v-icon> {{ state.message }}
	</v-snackbar>
</template>
<script setup>
const state = reactive({
	showSnackbar: false,
	message: '',
	color: 'success',
	icon: 'mdi-information',
	timer: 3000
});

function show(data = {}) {
	state.message = data.message || 'missing "message".';
	state.color = data.color || 'success';
	state.timer = data.timer || 1000;
	state.icon = data.icon || 'mdi-information';
	state.showSnackbar = true;
}

$bus.on('show-snackbar', data =>{
	show(data);
});
onMounted(()=>{
    applyDialog('toast', show);
});
defineExpose({
    show
});
</script>