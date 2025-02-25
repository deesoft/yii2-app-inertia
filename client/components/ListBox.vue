<template>
    <select v-model="model" class="w-100">
        <template v-for="(item, i) in filteredItems" :key="i">
            <optgroup v-if="item.items && item.items.length" :label="item.label">
                <option v-for="(sub, j) in item.items" :key="j" :value="sub.id" class="ml-3">{{ sub.label }}</option>
            </optgroup>
            <option v-if="!item.items" :value="item.id">{{ item.label }}</option>
        </template>
    </select>
</template>
<script setup>
const props = defineProps({
    items: Array,
    search: String,
});
const model = defineModel();

const lowerSearch = computed(() => (props.search || '').toLowerCase());
function filter(row){
    return  row.items ? row.items.length > 0 : row.label.toLowerCase().indexOf(lowerSearch.value) >= 0;
}
const filteredItems = computed(() => {
    if (!props.search) {
        return props.items;
    }
    return props.items.map(row => {
        if (row.items) {
            return {
                ...row, items: row.items.filter(filter)
            };
        } else {
            return row;
        }
    }).filter(filter);
});
</script>