<script setup>
import MainLayout from '@/layouts/Main.vue';

const router = useRouter()
const data = reactive({
    fm: {
        username: null,
        email: null,
        phone: null,
        password: null,
        password_confir: null,
    },
    errors: {
        password: [],
        password_confirm: [],
    },
    show_pass: false,
    show_pass_confirm: false,
    loading: false,
    policy: false
})

const submit = () => {
    Object.keys(data.errors).forEach(key => { data.errors[key] = [] })
    data.loading = true

    if (data.fm.password != data.fm.password_confirm) {
        data.errors.password_confirm.push('Password Confirmation Not Match')
        return;
    }

    $axios('api/auth/signup', {
        method: 'POST',
        body: {
            username: data.fm.username,
            email: data.fm.email,
            phone: data.fm.phone,
            password: data.fm.password
        }
    }).then((res) => {
        data.loading = false
        auth.login(res.token, res)
        appTrigger('show-snackbar', { message: 'Signup succesfully, redirect ...' })
        router.replace('/login')
    }).catch((e) => {
        data.loading = false
        appTrigger('show-snackbar', { message: e, color: 'error' })
        if (Array.isArray(e.response._data)) {
            e.response._data.forEach((item) => {
                data.errors[item.field].push(item.message)
            })
        }
    })
}


defineOptions({
    layout: MainLayout,
})
</script>

<template>
    <v-container class="d-flex" style="min-height:100vh">
        <v-icon style="position:absolute" @click="$router.back()">mdi-arrow-left</v-icon>
        <v-row class="d-flex align-center">
            <v-col cols="6" v-if="!$vuetify.display.mobile" class="text-center">
                <router-link to="/" class="text-decoration-none">
                    <v-img style="max-width:200px" class="mx-auto mb-3" :aspect-ratio="1"
                        src="/icon/logo-splash.png"></v-img>
                </router-link>
                <p class="text-center text-subtitle">Belanjar Grosir Murah hanya di grosirland.id</p>

            </v-col>
            <v-col class="d-flex align-center justify-center">
                <v-card flat class="align-center" style="min-width:320px">
                    <v-card-text class="text-center">
                        <h1 class="mb-4">Daftar Sekarang</h1>
                        <p>Sudah punya akun ? <router-link to="/login">Masuk</router-link></p>
                    </v-card-text>
                    <v-card-text>
                        <form @submit.prevent="submit()">
                            <v-text-field v-model="data.fm.username" required density="compact" label="Username"
                                variant="outlined">
                            </v-text-field>

                            <v-text-field type="email" v-model="data.fm.email" required density="compact" label="Email"
                                variant="outlined">
                            </v-text-field>

                            <v-text-field type="phone" v-model="data.fm.phone" density="compact" label="Phone"
                                variant="outlined">
                            </v-text-field>

                            <v-text-field v-model="data.fm.password" required density="compact" label="Password"
                                :type="data.show_pass ? 'text' : 'password'" variant="outlined" autocomplete="on"
                                :append-inner-icon="data.show_pass ? 'mdi-eye-off' : 'mdi-eye'"
                                @click:append-inner="data.show_pass = !data.show_pass">
                            </v-text-field>

                            <v-text-field v-model="data.fm.password_confirm" required density="compact"
                                label="Password Confirmation" :type="data.show_pass_confirm ? 'text' : 'password'"
                                variant="outlined" autocomplete="on" :error-messages="data.errors.password_confirm"
                                :append-inner-icon="data.show_pass_confirm ? 'mdi-eye-off' : 'mdi-eye'"
                                @click:append-inner="data.show_pass_confirm = !data.show_pass_confirm">
                            </v-text-field>

                            <input type="checkbox" v-model="data.policy" class="mr-0 pr-0" />
                            <label class="pt-4">
                                I accept the
                                <a href="/policy" target="_blank" class="text-sm text-blue-600 hover:underline">Terms
                                    and Conditions</a>
                            </label>
                            <div class="my-3">
                                <v-btn type="submit" :loading="data.loading" color="primary" block
                                    :disabled="!data.policy">
                                    Register
                                </v-btn>
                            </div>
                        </form>

                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>