<script setup>
import MainLayout from '@/layouts/Main.vue';
const { yiiUrl } = window;
const props = defineProps({
    model: Object,
});
const form = useForm({
    password: props.model.password,
    new_password: props.model.new_password,
});
const state = reactive({
    show_pass: false,
    show_pass_confirm: false,
    password_confirm: null,
});

const password_confirm_error = computed(() => {
    return form.new_password == state.password_confirm ? '' : 'Password confirm not match';
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
                            <Link :href="yiiUrl.home"><v-img height="40" :src="yiiUrl.public('icon/icon.jpeg')"></v-img></Link>
                            <h4 class="text-center text-h6">Ganti Password</h4>
                        </v-card-text>
                        <v-card-text>
                            <form @submit.prevent="form.post($page.url)" class="space-y-6">
                                <v-text-field v-model="form.password" required density="compact" clearable
                                    type="password" label="Old Password" variant="outlined"
                                    :error-messages="form.errors.password" @input="form.clearErrors('password')">
                                </v-text-field>
                                <v-text-field v-model="form.new_password" required density="compact"
                                    label="New Password" :type="state.show_pass ? 'text' : 'password'"
                                    variant="outlined" autocomplete="on"
                                    :append-inner-icon="state.show_pass ? 'mdi-eye-off' : 'mdi-eye'"
                                    :error-messages="form.errors.new_password"
                                    @click:append-inner="state.show_pass = !state.show_pass"
                                    @input="form.clearErrors('new_password')">
                                </v-text-field>

                                <v-text-field v-model="state.password_confirm" required density="compact"
                                    label="Password Confirmation" :type="state.show_pass_confirm ? 'text' : 'password'"
                                    variant="outlined" autocomplete="on" :error-messages="password_confirm_error"
                                    :append-inner-icon="state.show_pass_confirm ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="state.show_pass_confirm = !state.show_pass_confirm">
                                </v-text-field>
                                <div>
                                    <v-btn block color="primary" type="submit" class="mb-4" :loading="form.processing"
                                        :disabled="!!password_confirm_error">Submit</v-btn>
                                </div>
                            </form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>