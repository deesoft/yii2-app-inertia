<script setup>
import { darkMode } from '@/composables/layout';
import Toast from './dialogs/Toast.vue';
import Confirm from './dialogs/Confirm.vue';
import UploadImage from './dialogs/UploadImage.vue';
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