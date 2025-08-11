<script setup>
import MainLayout from '@/layouts/Main.vue';
const { yiiUrl } = window;
const props = defineProps({
    model: Object,
});
const form = useForm({
    username: props.model.username,
    password: props.model.password,
});

const state = reactive({
    show_pass: false,
});

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
                            <Link :href="yiiUrl.home"><v-img height="40" :src="yiiUrl.public('icon/icon.jpeg')"></v-img>
                            </Link>
                            <h4 class="text-center text-h6">Masuk Akun</h4>
                        </v-card-text>
                        <v-card-text>
                            <form @submit.prevent="form.post($page.url)" class="space-y-6">
                                <v-text-field v-model="form.username" required density="compact" clearable
                                    label="Username" variant="outlined">
                                </v-text-field>
                                <v-text-field v-model="form.password" required density="compact" label="Password"
                                    :type="state.show_pass ? 'text' : 'password'" variant="outlined" autocomplete="on"
                                    :error-messages="form.errors.password"
                                    :append-inner-icon="state.show_pass ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="state.show_pass = !state.show_pass">
                                </v-text-field>
                                <v-row no-gutters>
                                    <v-col order="last" class="pt-1 text-right mb-3">
                                        <Link :href="yiiUrl('/auth/forgot-password')">
                                        <span class="text-sm text-blue-600 hover:underline">Lupa Password?</span>
                                        </Link>
                                    </v-col>
                                </v-row>
                                <div>
                                    <v-btn block color="primary" type="submit" class="mb-4"
                                        :loading="form.processing">Login</v-btn>
                                </div>
                            </form>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Kamu belum mempunyai akun ?
                                <Link :href="yiiUrl('/auth/register')" class="text-decoration-none">
                                <v-chip style="cursor:pointer" color="green"><span
                                        class="text-blue-600 hover:underline">Daftar Sekarang</span></v-chip>
                                </Link>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>