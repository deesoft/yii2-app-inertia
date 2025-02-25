<script setup>
import { URL } from '@/composables/url';

const props = defineProps({
    columns: Array,
    data: [Object, Array],
    rowClass: [Function, Array, String],
    reload: Boolean,
});
const items = computed(() => props.data.items ? props.data.items : props.data);
const meta = computed(() => props.data.meta ? props.data.meta : null);
const emit = defineEmits(['reload']);

function doReload(param) {
    if (props.reload) {
        URL.reload(param, { preserveScroll: true, preserveState: true });
    } else {
        emit('reload', param);
    }
}
const sort = computed(() => {
    let s = URL.queryParams.sort;
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

const pageNumber = computed({
    get() {
        return meta.value ? meta.value.currentPage : null;
    },
    set(val) {
        doReload({ page: val });
    }
});

const pageSize = computed({
    get() {
        return meta.value ? meta.value.perPage : null;
    },
    set(val) {
        doReload({ 'per-page': val });
    }
});

function calcRowClass(row, i) {
    if (props.rowClass instanceof Function) {
        return props.rowClass(row, i)
    }
    return props.rowClass
}
function lineNo(i) {
    return meta.value ? (pageNumber.value - 1) * pageSize.value + i + 1 : i + 1;
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
                        <slot :name="'d-' + column.field" v-bind="row" v-bind:_line="i" v-bind:_no="lineNo(i)">
                            {{ row[column.field] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </v-table>
        <v-divider v-if="meta"></v-divider>
        <v-card-actions class="text-center" v-if="meta">
            <v-pagination v-model="pageNumber" :length="meta.pageCount" density="compact"
                :total-visible="7"></v-pagination>
            <v-spacer></v-spacer>
            <v-select v-model="pageSize" style="max-width: 100px;" hide-details density="compact"
                variant="solo" :items="[5, 10, 20, 25, 30, 40, 50, 100, 500]"></v-select>
        </v-card-actions>
    </v-card>
</template>