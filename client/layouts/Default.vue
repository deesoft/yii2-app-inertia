<script setup>
import { darkMode } from '@/composables/global';
import { reactive } from 'vue';
import Main from './Main.vue';
import SideMenu from './SideMenu.vue';
import UserToolbar from './UserToolbar.vue';

const state = reactive({
    clipped: false,
    drawer: true,
    fixed: false,
    title: "Test"
});
const menus = window.menus || [];
</script>
<template>
    <Main>
        <v-navigation-drawer v-model="state.drawer" :mini-variant="false" :clipped="state.clipped" fixed app>
            <v-list>
                <v-list-item prepend-avatar="/icon/icon.jpeg" subtitle="Dee Application" title="Dee App"></v-list-item>
            </v-list>
            <v-divider></v-divider>
            <SideMenu :menus="menus"/>
        </v-navigation-drawer>
        <v-app-bar :clipped-left="state.clipped" fixed app>
            <v-app-bar-nav-icon @click.stop="state.drawer = !state.drawer" />
            <v-spacer></v-spacer>
            <UserToolbar></UserToolbar>
            <v-btn @click.stop="darkMode = !darkMode"
                :icon="darkMode ? 'mdi-brightness-4' : 'mdi-white-balance-sunny'"></v-btn>
        </v-app-bar>
        <v-main>
            <slot></slot>
        </v-main>
        <v-footer :absolute="false" app>
            <span><a href="https://github.com/mdmunir">@mdmunir</a> &copy; {{ new Date().getFullYear() }}</span>
        </v-footer>
    </Main>
</template>