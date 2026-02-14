<script setup>
import debounce from "debounce";
import { reactive, watch } from "vue";
import axios from 'axios';
const { yiiUrl } = window;

const props = defineProps({
    route: { type: String, required: true },
    params: { type: Object },
    itemRoute: { type: String },
    itemParams: { type: Object },
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

watch(model, (val) => {
    if (val && (!modelRaw.value || modelRaw.value[props.itemValue] != val)) {
        const route = props.itemRoute || props.route;
        const params = {...((props.itemRoute ? props.itemParams : props.params) || {}), [props.itemValue]: val};
        axios.get(yiiUrl(route, params)).then(res => {
            modelRaw.value = Array.isArray(res.data) ? res.data[0] : res.data;
        });
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
            axios.get(yiiUrl(props.route, {...(props.params || {}), q: value})).then(res => {
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