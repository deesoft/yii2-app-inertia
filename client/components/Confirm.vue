<script setup>
import { reactive, Teleport } from 'vue';
const props = defineProps({
    width: { type: String, default: 300 },
});

const local = reactive({
    resolve: null,
    reject: null,
});

const state = reactive({
    show: false,
    title: 'Konfirmasi',
    message: null,
    color: 'error',
});

function close(result) {
    if (result) {
        local.resolve(true);
    } else {
        local.reject(false);
    }
    local.resolve = null;
    local.reject = null;
    state.show = false;
}

function open(options) {
    state.dialog = true;
    var opt = (typeof options == 'string') ? { message: options } : (options || {});

    state.message = opt.message;
    state.title = opt.title || 'Konfirmasi';
    state.color = opt.color || 'error';

    return new Promise((resolve, reject) => {
        local.resolve = resolve;
        local.reject = reject;
    });
}

defineExpose({ open });
</script>
<template>
    <Teleport to="#end-page">
        <v-dialog dense v-model="show" :max-width="width" style="z-index: 9999;" @keydown.esc="close(false)">
            <v-card>
                <v-toolbar dark :color="color" class="gradient-orange" density="compact" flat>
                    <v-toolbar-title class="white--text">{{ state.title || 'Konfirmasi' }}</v-toolbar-title>
                </v-toolbar>
                <v-card-text v-show="!!state.message" class="pa-4 text-caption">
                    {{ state.message }}
                </v-card-text>
                <v-card-actions class="pt-0">
                    <v-spacer></v-spacer>
                    <v-btn color="green" text @click.native="close(false)">Cancel</v-btn>
                    <v-btn dark color="error darken-1" text @click.native="close(true)">Yes</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Teleport>
</template>