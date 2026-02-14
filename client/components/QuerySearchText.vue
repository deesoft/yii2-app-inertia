<script setup>
import { URL } from '../composables/url';
import { watch, ref } from 'vue';

const props = defineProps({
    reload: { type: Boolean, default: true },
});

const emit = defineEmits(['search']);
const q = ref(URL.params.q);

watch(() => URL.params.q, (val) => {
    q.value = val;
});

function doSearch() {
    if (props.reload) {
        URL.reload({ q: q.value });
    } else {
        emit('search', { q: q.value });
    }
}
</script>
<template>
    <v-text-field v-model="q" hide-details prepend-append-icon="mdi-magnify"
        @click:append-inner="doSearch" @change="doSearch"></v-text-field>
</template>