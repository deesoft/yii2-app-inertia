<script setup>
import { URL } from '@/composables/url';
import Pagination from './Pagination.vue';
import { computed } from 'vue';

const props = defineProps({
    columns: {type: Array, required: true},
    data: { type: [Object, Array], required: true },
    rowClass: [Function, Array, String],
    reload: { type: Boolean, default: true },
    filter: { type: Boolean, default: true },
    filters: { type: Object },
});
const items = computed(() => props.data.items ? props.data.items : props.data);
const emit = defineEmits(['reload']);

function doReload(param) {
    if (props.reload) {
        URL.reload(param);
    } else {
        emit('reload', param);
    }
}
const sort = computed(() => {
    let s = URL.params.sort;
    if (s) {
        return s.split(',').map(v => (v.charAt(0) == '-' ? { key: v.substring(1), direction: 2 }: { key: v, direction: 1 }));
    }
    return [];
});

function calcRowClass(row, i) {
    if (props.rowClass instanceof Function) {
        return props.rowClass(row, i);
    }
    return props.rowClass;
}
function lineNo(i) {
    if (props.data.meta) {
        return (props.data.meta.currentPage - 1) * props.data.meta.perPage + i + 1;
    }
    return i + 1;
}

function sorting(key) {
    if (!key) {
        return;
    }
    const result = [];
    let part = sort.value.find(v => v.key == key);
    if (part && part.direction == 1) {
        result.push('-' + key);
    } else if (!part) {
        result.push(key);
    }

    sort.value.forEach(v => {
        if (v.key != key) {
            result.push((v.direction == 1 ? '' : '-') + v.key);
        }
    });

    doReload({ sort: result.join(',') });
}

function isSorted(key) {
    if (key) {
        const v = sort.value.find(v => v.key == key)
        return v ? v.direction : false;
    }
    return false;
}

const filterState = computed(() => props.filters ? {...props.filters} : {...URL.params});
function doFilter(field){
    return value => doReload({[field]: value});
}
</script>
<template>
    <v-table>
        <thead>
            <tr>
                <th class="pb-1" valign="top" v-for="(column, idx) in columns" :class="column.headerClass"
                    :data-field="column.field" :key="idx">
                    <span v-if="column.sort" class="cursor-pointer" @click="sorting(column.sort)">
                        <slot :name="'h-' + column.field" v-bind="{column}">{{ column.title || column.field }} </slot>
                        <v-chip size="x-small" v-if="isSorted(column.sort)"
                            :color="isSorted(column.sort) == 1 ? 'green' : 'blue'">
                            <v-icon end v-if="isSorted(column.sort) == 1">mdi-arrow-up</v-icon>
                            <v-icon end v-else>mdi-arrow-down</v-icon>
                        </v-chip>
                    </span>
                    <span v-else>
                        <slot :name="'h-' + column.field" v-bind="column">{{ column.title || column.field }} </slot>
                    </span>
                </th>
            </tr>
            <tr v-if="filter">
                <th class="pb-1" valign="top" v-for="(column, idx) in columns" :class="column.headerClass"
                    :data-field="column.field" :key="idx">
                    <template v-if="column.filter !== false">
                        <slot :name="'filter-' + column.field" v-bind="{column, value: filterState[column.filter || column.field], doFilter: doFilter(column.filter || column.field), doFilters: doReload}">
                            <v-select v-if="column.filterItems && Array.isArray(column.filterItems)" :items="column.filterItems" density="compact" hide-details v-model="filterState[column.filter || column.field]"
                                @update:modelValue="doReload({[column.filter || column.field]: $event})"></v-select>
                            <v-text-field v-else density="compact" hide-details v-model="filterState[column.filter || column.field]"
                                @change="doReload({[column.filter || column.field]: $event.target.value})"></v-text-field>
                        </slot>
                    </template>
                </th>
            </tr>
        </thead>
        <tbody class="text-caption">
            <tr v-if="items.length == 0">
                <td :colspan="columns.length">No Data Available</td>
            </tr>
            <tr v-for="(row, i) in items" :key="i" :class="calcRowClass(row, i)">
                <td valign="top" v-for="(column, idx) in columns" :key="idx" :class="column.dataClass" :style="{align: column.align || 'left'}">
                    <slot :name="'d-' + column.field" v-bind="{row, index:i, line:lineNo(i)}">
                        {{ row[column.field] }}
                    </slot>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="data.meta">
            <tr>
                <td :colspan="columns.length">
                    <Pagination :meta="data.meta" :links="data.links" :reload="reload" @reload="doReload"></Pagination>
                </td>
            </tr>
        </tfoot>
    </v-table>
</template>