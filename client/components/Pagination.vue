<script setup>
import { URL } from '@/composables/url';
import { computed } from 'vue';

const props = defineProps({
    meta: { type: Object, required: true },
    links: Array,
    reload: { type: Boolean, default: true },
    sizes: {
        type: Array,
        default() {
            return [5, 10, 20, 25, 30, 40, 50, 100, 500];
        }
    }
});
const emit = defineEmits(['reload']);

function doReload(param) {
    if (props.reload) {
        URL.reload(param);
    } else {
        emit('reload', param);
    }
}
const pageNumber = computed({
    get() {
        return props.meta.currentPage;
    },
    set(v) {
        doReload({ 'page': val });
    }
});
const pageSize = computed({
    get() {
        return props.meta.perPage;
    },
    set(val) {
        doReload({ 'per-page': val });
    }
});
</script>
<template>
    <v-card density="compact" variant="plain">
        <v-card-actions>
            <v-btn-group v-if="reload && links && links.length">
                <v-btn v-for="(link, i) in links" :key="i" :to="link.href" :disabled="link.active">{{ link.label
                    }}</v-btn>
            </v-btn-group>
            <v-pagination v-else v-model="pageNumber" :length="meta.pageCount" density="compact"
                :total-visible="7"></v-pagination>
            <v-spacer></v-spacer>
            <v-select v-if="sizes && sizes.length" v-model="pageSize" style="max-width: 100px;" hide-details
                density="compact" variant="solo" :items="sizes"></v-select>
        </v-card-actions>
    </v-card>
</template>