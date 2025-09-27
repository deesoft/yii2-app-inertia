<script setup>
import { URL } from '@/composables/url';
import Pagination from './Pagination.vue';
import { computed } from 'vue';

const props = defineProps({
    columns: Array,
    data: { type: [Object, Array], required: true },
    rowClass: [Function, Array, String],
    reload: { type: Boolean, default: true },
});
const items = computed(() => props.data.items ? props.data.items : props.data);
const emit = defineEmits(['reload']);

function doReload(param) {
    if (props.reload) {
        URL.reload(param, { preserveScroll: true, preserveState: true });
    } else {
        emit('reload', param);
    }
}
const sort = computed(() => {
    let s = URL.params.sort;
    if (s) {
        return s.split(',').map(v => {
            if (v.charAt(0) == '-') {
                return { key: v.substring(1), direction: 2 };
            } else {
                return { key: v, direction: 1 };
            }
        })
    }
    return [];
});

function calcRowClass(row, i) {
    if (props.rowClass instanceof Function) {
        return props.rowClass(row, i)
    }
    return props.rowClass
}
function lineNo(i) {
    if (props.data.meta) {
        return (props.data.meta.currentPage - 1) * props.data.meta.perPage + i + 1;
    }
    return i + 1;
}

function sorting(key) {
    if (!key) {
        return
    }
    const result = []
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
    })

    doReload({ sort: result.join(',') });
}

function isSorted(key) {
    if (key) {
        const v = sort.value.find(v => v.key == key)
        return v ? v.direction : false
    }
    return false
}

</script>
<template>
    <v-card>
        <slot></slot>
        <v-table density="compact" fixed-header>
            <thead>
                <tr style="background:#eee">
                    <th class="pb-1" valign="top" v-for="(column, idx) in columns" :class="column.headerClass"
                        :data-field="column.field" :key="idx">
                        <span v-if="column.sort" class="cursor-pointer" @click="sorting(column.sort)">
                            <slot :name="'h-' + column.field" v-bind="column">{{ column.title || column.field }} </slot>
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
            </thead>
            <tbody class="text-caption">
                <tr v-if="items.length == 0">
                    <td :colspan="columns.length">No Data Available</td>
                </tr>
                <tr v-for="(row, i) in items" :key="i" :class="calcRowClass(row, i)">
                    <td valign="top" v-for="(column, idx) in columns" :key="idx" :class="column.dataClass">
                        <slot :name="'d-' + column.field" v-bind="{...row,$index:i,$no:lineNo(i)}">
                            {{ row[column.field] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </v-table>
        <v-divider v-if="data.meta"></v-divider>
        <Pagination v-if="data.meta" :meta="data.meta" :links="data.links" :reload="reload" @reload="doReload"></Pagination>
    </v-card>
</template>