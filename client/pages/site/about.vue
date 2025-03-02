<script setup>
import MainLayout from '@/layouts/Main.vue';

defineOptions({
    layout: MainLayout,
});
</script>
<template>
    <div class="bg-grey-lighten-3">
        <v-container class="d-flex" style="min-height:100vh">
            <v-row class="d-flex align-center">
                <v-col class="d-flex align-center justify-center pa-4">
                    <v-card class="align-center" style="min-width:320px">
                        <v-card-text>
                            <Link :href="toUrl.home"><v-img height="40" src="/icon/icon.jpeg"></v-img></Link>
                            <h4 class="text-center text-h6">About</h4>
                        </v-card-text>
                        <v-card-text>
                            <h2 class="text-center">Yii2 Inertia Template</h2>
                            {{ $page.props }}
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>
