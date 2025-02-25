<script setup>
import {URL} from '@/composables/url';

const props = defineProps({
    reload: { type: Boolean, default: false },
    placeholder: { type: String, default: 'Search ... ' },
});

const emit = defineEmits(['search']);
const q = ref(URL.queryParams.q);

watch(() => URL.queryParams.q, (val) => {
    q.value = val;
});

function doSearch() {
    if (props.reload) {
        URL.reload({ q: q.value }, { preserveScroll: true, preserveState: true });
    } else {
        emit('search', { q: q.value });
    }
}
</script>
<template>
    <v-text-field v-model="q" :placeholder="placeholder" hide-details prepend-inner-icon="mdi-magnify"
        @click:prepend-inner="doSearch" @change="doSearch"></v-text-field>
</template>