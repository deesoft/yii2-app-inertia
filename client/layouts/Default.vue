<template>
    <Main>
        <v-navigation-drawer v-model="state.drawer" :mini-variant="false" :clipped="state.clipped" fixed app>
            <v-list>
                <v-list-item prepend-avatar="/icon/icon.jpeg"
                    subtitle="Dee Application" title="Dee App"></v-list-item>
            </v-list>
            <v-divider></v-divider>
            <SideMenu />
        </v-navigation-drawer>
        <v-app-bar :clipped-left="state.clipped" fixed app>
            <v-app-bar-nav-icon @click.stop="state.drawer = !state.drawer" />
            <v-spacer></v-spacer>
            <v-btn @click.stop="darkMode = !darkMode" icon>
                <v-icon>{{ darkMode ? 'mdi-brightness-4':'mdi-white-balance-sunny' }}</v-icon>
            </v-btn>
        </v-app-bar>
        <v-main>
            <slot></slot>
        </v-main>
        <v-footer :absolute="false" app>
            <span><a href="https://github.com/mdmunir">@mdmunir</a> &copy; {{ new Date().getFullYear() }}</span>
        </v-footer>
    </Main>
</template>

<script setup>
import { darkMode } from '@/composables/layout';
import SideMenu from './SideMenu.vue';
import Main from './Main.vue';

const state = reactive({
    clipped: false,
    drawer: true,
    fixed: false,
    title: "Test"
});
</script>
