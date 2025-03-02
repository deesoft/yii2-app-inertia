<script setup>
import { darkMode } from '@/composables/global';
import Toast from '@/components/dialogs/Toast.vue';
import Confirm from '@/components/dialogs/Confirm.vue';
import UploadImage from '@/components/dialogs/Upload.vue';
</script>
<template>
    <v-app :theme="darkMode ? 'dark' : 'light'">
        <slot></slot>
        <div id="globalModal">
            <Toast></Toast>
            <Confirm></Confirm>
            <UploadImage></UploadImage>
        </div>
    </v-app>
</template>