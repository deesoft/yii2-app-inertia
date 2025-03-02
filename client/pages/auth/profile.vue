<script setup>
import MainLayout from '@/layouts/Main.vue';
import { auth } from '@/composables/auth';
import { uploadImage } from '@/composables/global';
const { toUrl } = window;

function uploadProcess(formData) {
    return axios.post(toUrl.post('/auth/edit-avatar'), formData).then(res => {
        auth.avatar = res.data.avatar;
        return res.data;
    });
}

function editImage() {
    uploadImage({ aspectRatio: 1.0, process: uploadProcess });
}
defineOptions({
    layout: MainLayout,
});
</script>

<template>
    <div class="bg-grey-lighten-3">
        <v-container class="d-flex" style="min-height:100vh">
            <v-row class="d-flex align-center">
                <v-col class="d-flex align-center justify-center">
                    <v-card class="pa-6" max-width="400" elevation="6" min-width="350">
                        <v-card-text>
                            <Link :href="toUrl.home"><v-img height="40" :src="toUrl.public('icon/icon.jpeg')"></v-img></Link>
                        </v-card-text>
                        <v-img class="align-end" :src="auth.avatarLink" alt="Avatar" cover>
                            <v-card-title>
                                <v-btn icon="mdi-pencil" size="small" @click="editImage"></v-btn>
                            </v-card-title>
                        </v-img>
                        <v-list>
                            <v-list-item :title="`${auth.fullname}(${auth.username})`">
                                <template v-slot:prepend>
                                    <v-icon>mdi-account</v-icon>
                                </template>
                            </v-list-item>
                            <v-list-item :title="auth.email">
                                <template v-slot:prepend>
                                    <v-icon>mdi-email</v-icon>
                                </template>
                            </v-list-item>
                            <v-list-item :to="toUrl('auth/change-password')" title="Change Password">
                                <template v-slot:prepend>
                                    <v-icon>mdi-lock</v-icon>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>