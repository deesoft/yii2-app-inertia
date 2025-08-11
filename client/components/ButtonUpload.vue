<template>
    <v-btn @click="inpFile.click()">
        <slot></slot>
        <div style="display: none; visibility: hidden;">
            <input ref="inpFile" type="file" :accept="accept" @change="inpChange">
        </div>
    </v-btn>
</template>
<script setup>
const props = defineProps({
    accept: String,
});
const emit = defineEmits(['uploaded']);

const inpFile = useTemplateRef('inpFile');
function inpChange(event) {
    var input = event.target;
    if (input.files && input.files.length) {
        var formData = new FormData;
        formData.append('file', input.files[0]);
        axios.post(yiiUrl.post('file/upload'), formData).then(res=>{
            emit('uploaded', res.data);
        });
    }
}
</script>