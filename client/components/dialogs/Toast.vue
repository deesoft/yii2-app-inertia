<template>
	<v-snackbar :color="state.color" :timeout="state.timer" v-model="state.show" bottom right class="text-white"
		style="z-index:99999">
		<v-icon left>{{ state.icon }}</v-icon> {{ state.message }}
	</v-snackbar>
</template>
<script setup>
const state = reactive({
	show: false,
	message: '',
	color: 'success',
	icon: 'mdi-information',
	timer: 3000
});

function show(data) {
	const opt = typeof data === 'string' ? { message: data } : data || {};
	state.message = opt.message;
	state.color = opt.color || 'success';
	state.timer = opt.timer || 3000;
	state.icon = opt.icon || 'mdi-information';
	state.show = true;
}

$bus.on('toast', data => {
	show(data);
});
</script>