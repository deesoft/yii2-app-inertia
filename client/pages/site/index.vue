<template>
    <v-container fluid>
        <v-row dense>
            <v-col cols="12">
                <p class="text-overline">
                    <Link :href="yiiUrl.home" class="text-decoration-none"><v-icon>mdi-home</v-icon></Link> / Dashboard
                </p>
            </v-col>
            <v-col cols="12">    
                
            </v-col>
        </v-row>
    </v-container>
</template>
<script setup>
const {yiiUrl} = window;
</script>