<script setup>
import { auth } from '@/composables/auth';
const { yiiUrl } = window;
</script>
<template>
    <v-btn v-if="!auth.isLoged" :to="yiiUrl('/auth/login')">Login<v-icon>"mdi-login</v-icon></v-btn>
    <v-btn v-else icon>
        <v-avatar v-if="auth.isLoged">
            <v-img v-if="auth.avatar" :src="auth.avatarLink"></v-img>
            <span v-else class="text-h5">{{ auth.initial }}</span>
        </v-avatar>
        <v-menu activator="parent" v-if="auth.isLoged">
            <v-list>
                <v-list-item :to="yiiUrl('auth/profile')" :title="auth.username" :subtitle="auth.fullname">
                    <template v-slot:prepend>
                        <v-icon>mdi-account-circle</v-icon>
                    </template>
                </v-list-item>
                <v-list-item :to="yiiUrl('auth/change-password')" title="Change Password">
                    <template v-slot:prepend>
                        <v-icon>mdi-lock</v-icon>
                    </template>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item>
                    <template v-slot:prepend>
                        <v-icon>mdi-logout</v-icon>
                    </template>
                    <v-list-item-title>
                        <Link :href="yiiUrl.post('auth/logout')" method="post">Logout</Link>
                    </v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-btn>
</template>