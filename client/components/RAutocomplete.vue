<script setup>
import debounce from "debounce";
import { reactive, watch } from "vue";
import axios from 'axios';

const props = defineProps({
    url: { type: String, required: true },
    itemUrl: { type: String },
    itemTitle: { type: String, default: 'name' },
    itemValue: { type: String, default: 'id' },
});
const emit = defineEmits(['changed']);

const modelRaw = defineModel('raw');
const model = defineModel();
const state = reactive({
    items: [],
    loading: false,
});

watch(model, async (val) => {
    if (props.itemUrl && val && (!modelRaw.value || modelRaw.value[props.itemValue] != val)) {
        var res = await axios.get(props.itemUrl, { params: { id: val } });
        modelRaw.value = res.data;
    }
});

function changed(value) {
    model.value = value ? value[props.itemValue] : null;
    emit('changed', value);
}

function doSearch(value) {
    return new Promise((resolve, reject) => {
        if (!state.loading) {
            state.loading = true;
            axios.get(props.url, { params: { q: value } }).then(res => {
                state.items = res.data;
                state.loading = false;
                resolve(true);
            }).catch((res) => {
                state.loading = false;
                reject(res);
            });
        }
    });
}

const search = debounce(async (value) => {
    await doSearch(value);
}, 250);

</script>
<template>
    <v-autocomplete v-model="modelRaw" :items="state.items" :loading="state.loading" :item-value="itemValue"
        :item-title="itemTitle" return-object @update:search="search" @update:model-value="changed"></v-autocomplete>
</template>