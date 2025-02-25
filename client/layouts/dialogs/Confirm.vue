<template>
    <v-dialog dense v-model="state.dialog" :max-width="state.width" :style="{ zIndex: state.zIndex }"
        @keydown.esc="close(false)">
        <v-card>
            <v-toolbar dark :color="state.color" class="gradient-orange" density="compact" flat>
                <v-toolbar-title class="white--text">
                    {{ state.title || 'Konfirmasi' }}
                </v-toolbar-title>
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
</template>
<script setup>

const local = reactive({
    callbackTrue: null,
    callbackFalse: null,
    resolve: null,
    reject: null,
});

const state = reactive({
    dialog: false,
    message: null,
    title: 'Konfirmasi',
    color: 'error',
    width: 300,
    zIndex: 9999,
});

function close(result) {
    if (result) {
        if (local.callbackTrue) {
            local.callbackTrue(result);
        }
        local.resolve(true);
    } else {
        if (local.callbackFalse) {
            local.callbackFalse(result);
        }
        local.reject(false);
    }
    local.callbackTrue = null;
    local.callbackFalse = null;
    local.resolve = null;
    local.reject = null;
    state.dialog = false;
}

function open(options, callbackTrue, callbackFalse) {
    state.dialog = true;
    var opt = (typeof options == 'string') ? { message: options } : (options || {});

    state.message = opt.message;
    state.title = opt.title || 'Konfirmasi';
    state.color = opt.color || 'error';
    state.width = opt.width || 300;
    state.zIndex = opt.zIndex || 9999;

    local.callbackTrue = callbackTrue;
    local.callbackFalse = callbackFalse;

    return new Promise((resolve, reject) => {
        local.resolve = resolve;
        local.reject = reject;
    });
}

onMounted(() => {
    applyDialog('confirm', open);
});
defineExpose({
    open
});
</script>