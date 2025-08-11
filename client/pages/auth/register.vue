<script setup>
import MainLayout from '@/layouts/Main.vue';
const {yiiUrl} = window;
const props = defineProps({
    model: Object,
});
const form = useForm({
    username: props.model.username,
    password: props.model.password,
    email: props.model.email,
    phone: props.model.email,
});
const state = reactive({
    show_pass: false,
    show_pass_confirm: false,
    policy: false,
    password_confirm: null,
});

const password_confirm_error = computed(() => {
    return form.password == state.password_confirm ? '' : 'Password confirm not match';
});

defineOptions({
    layout: MainLayout,
})
</script>

<template>
    <div class="bg-grey-lighten-3">
        <v-container class="d-flex" style="min-height:100vh">
            <v-row class="d-flex align-center">
                <v-col class="d-flex align-center justify-center">
                    <v-card flat class="align-center" style="min-width:320px">
                        <v-card-text class="text-center">
                            <Link :href="yiiUrl.home"><v-img height="40" :src="yiiUrl.public('icon/icon.jpeg')"></v-img></Link>
                            <h1 class="mb-4">Daftar Sekarang</h1>
                            <p>Sudah punya akun ? <Link :href="yiiUrl('/site/login')">Masuk</Link></p>
                        </v-card-text>
                        <v-card-text>
                            <form @submit.prevent="form.submit($page.url)">
                                <v-text-field v-model="form.username" required density="compact" label="Username"
                                    variant="outlined" :error-messages="form.errors.username"
                                    @input="form.clearErrors('username')">
                                </v-text-field>

                                <v-text-field type="email" v-model="form.email" required density="compact" label="Email"
                                    variant="outlined" :error-messages="form.errors.email"
                                    @input="form.clearErrors('email')">
                                </v-text-field>

                                <v-text-field type="phone" v-model="form.phone" density="compact" label="Phone"
                                    variant="outlined" :error-messages="form.errors.phone"
                                    @input="form.clearErrors('phone')">
                                </v-text-field>

                                <v-text-field v-model="form.password" required density="compact" label="Password"
                                    :type="state.show_pass ? 'text' : 'password'" variant="outlined" autocomplete="on"
                                    :append-inner-icon="state.show_pass ? 'mdi-eye-off' : 'mdi-eye'"
                                    :error-messages="form.errors.password"
                                    @click:append-inner="state.show_pass = !state.show_pass"
                                    @input="form.clearErrors('password')">
                                </v-text-field>

                                <v-text-field v-model="state.password_confirm" required density="compact"
                                    label="Password Confirmation" :type="state.show_pass_confirm ? 'text' : 'password'"
                                    variant="outlined" autocomplete="on" :error-messages="password_confirm_error"
                                    :append-inner-icon="state.show_pass_confirm ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="state.show_pass_confirm = !state.show_pass_confirm">
                                </v-text-field>

                                <input type="checkbox" v-model="state.policy" class="mr-0 pr-0" />
                                <label class="pt-4">
                                    I accept the
                                    <a href="/policy" target="_blank"
                                        class="text-sm text-blue-600 hover:underline">Terms
                                        and Conditions</a>
                                </label>
                                <div class="my-3">
                                    <v-btn type="submit" :loading="state.loading" color="primary" block
                                        :disabled="!state.policy && !!password_confirm_error">
                                        Register
                                    </v-btn>
                                </div>
                            </form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>