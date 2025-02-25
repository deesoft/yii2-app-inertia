<template>
    <v-list nav>
        <template v-for="(item, i) in menus" :key="i">
            <v-list-item v-if="!item.children || item.children.length == 0" :prepend-icon="item.icon || 'mdi-apps'"
                :to="item.to" :title="item.title"></v-list-item>
            <v-list-group v-else>
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props" :prepend-icon="item.icon || 'mdi-apps'"
                        :title="item.title"></v-list-item>
                </template>
                <v-list-item v-for="(child, j) in item.children" :prepend-icon="child.icon || 'mdi-apps'" :to="child.to"
                    :title="child.title" :key="j"></v-list-item>
            </v-list-group>
        </template>
    </v-list>
</template>

<script setup>
const menus = window.menus;
</script>