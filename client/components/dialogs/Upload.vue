<template>
    <v-dialog dense v-model="state.dialog" max-width="640" style="z-index:9999" persistent>
        <v-card id="v-dlg">
            <v-toolbar dark color="info" class="gradient-orange" density="compact" flat>
                <v-toolbar-title>
                    Upload image
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text class="pa-2 overflow-auto">
                <VueCropper ref="cropper" :autoCropArea="1" :autoCrop="state.autoCrop" :aspectRatio="state.aspectRatio"
                    :movable="false" :zoomable="false" :zoomOnTouch="false" :zoomOnWheel="false" :src="state.imgSrc" />
            </v-card-text>
            <v-card-actions class="pt-0">
                <v-spacer></v-spacer>
                <v-btn color="green" text @click.native="close">Cancel</v-btn>
                <v-btn dark color="error darken-1" text @click.native="submit">Yes</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <div style="display: none; visibility: hidden;">
        <input ref="inpFile" type="file" accept="image/*" @change="inpChange">
        <input ref="inpFile2" type="file" :accept="local2.accept" @change="inpChange2">
    </div>
</template>
<script setup>
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import axios from "axios";
const {toUrl} = window;

const local = reactive({
    resolve: null,
    reject: null,
    filename: null,
    filetype: null,
    process: null,
});

const state = reactive({
    dialog: false,
    imgSrc: null,
    aspectRatio: 16 / 9,
    autoCrop: true,
})

const inpFile = useTemplateRef('inpFile');
const inpFile2 = useTemplateRef('inpFile2');
const cropper = useTemplateRef('cropper');

function inpChange(event) {
    var input = event.target;
    if (input.files && input.files.length) {
        state.dialog = true
        var reader = new FileReader();
        reader.onload = function (e) {
            state.imgSrc = e.target.result;
            cropper.value.replace(e.target.result);
        }
        local.filename = input.files[0].name;
        local.filetype = input.files[0].type;
        reader.readAsDataURL(input.files[0]);
    } else {
        local.resolve(false);
    }
}

function defaultProcess(formData) {
    return axios.post(toUrl.post('/file/upload'), formData).then(res => res.data);
}

function open(options) {
    inpFile.value.click();
    const opt = options || {};
    state.aspectRatio = opt.aspectRatio || NaN;
    state.autoCrop = opt.autoCrop === undefined ? true : opt.autoCrop;
    local.process = opt.process || defaultProcess;

    return new Promise((resolve, reject) => {
        local.resolve = resolve;
        local.reject = reject;
    });
}

function close() {
    if (local.resolve) {
        local.resolve(false)
    }
    local.resolve = null;
    local.reject = null;
    state.dialog = false;
}
function submit() {
    cropper.value.getCroppedCanvas().toBlob(blob => {
        var formData = new FormData;
        formData.append('file', new File([blob], local.filename, { type: local.filetype }));
        Promise.resolve(local.process(formData)).then(data => {
            local.resolve(data);
            local.resolve = null;
            local.reject = null;
            state.dialog = false
        }).catch(err => {
            local.reject(err);
            local.resolve = null;
            local.reject = null;
            state.dialog = false
        });
    });
}

const local2 = reactive({
    resolve: null,
    reject: null,
    accept: null,
})
function uploadFile(accept) {
    local2.accept = accept;
    inpFile2.value.click();
    return new Promise((resolve, reject) => {
        local2.resolve = resolve;
        local2.reject = reject;
    });
}

function inpChange2(event) {
    var input = event.target;
    if (input.files && input.files.length) {
        var formData = new FormData;
        formData.append('file', input.files[0]);
        axios.post(toUrl.post('file/upload'), formData).then(res => {
            local2.resolve(res.data);
            local2.resolve = null;
            local2.reject = null;
        }).catch(err => {
            local2.reject(err);
            local2.resolve = null;
            local2.reject = null;
        });
    }
}

onMounted(() => {
    applyDialog('uploadImage', open);
    applyDialog('uploadFile', uploadFile);
});
</script>

<style>
.cropped-image {
    padding: 0 .8rem;
}

.img-cropper {
    max-height: 400px;
    overflow: hidden;
}
</style>