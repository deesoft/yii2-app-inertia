<script setup>
import Toast from '@/layouts/Toast.vue';
import { darkMode } from '@/composables/global';
</script>
<template>
    <v-app :theme="darkMode ? 'dark' : 'light'">
        <slot></slot>
        <div id="end-page">
            <Toast></Toast>
        </div>
    </v-app>
</template>