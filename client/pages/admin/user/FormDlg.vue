<template>
    <v-dialog v-model="state.show" @keydown.esc="state.show = false" max-width="450">
        <v-card>
            <v-toolbar class="gradient-orange" density="compact" flat>
                <v-toolbar-title class="white--text">
                    {{ state.id ? `Edit User '${state.id}'` : 'New User' }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-row>
                    <v-col class="py-1" cols="12">
                        <v-text-field type="text" v-model="form.username" label="Username" variant="outlined"
                            density="compact" required :error-messages="form.errors.username"
                            @input="form.clearErrors('username')"></v-text-field>
                    </v-col>
                    <v-col class="py-1" cols="12" v-if="!state.id">
                        <v-text-field :type="state.passwordOpen ? 'text' : 'password'" v-model="form.password"
                            label="Password" variant="outlined" density="compact" required
                            :error-messages="form.errors.password" append-inner-icon="mdi-eye"
                            @click:append-inner="state.passwordOpen = !state.passwordOpen"
                            @input="form.clearErrors('password')"></v-text-field>
                    </v-col>
                    <v-col class="py-1" cols="12">
                        <v-text-field type="text" v-model="form.email" label="Email" variant="outlined"
                            density="compact" required :error-messages="form.errors.email"
                            @input="form.clearErrors('email')"></v-text-field>
                    </v-col>
                    <v-col class="py-1" cols="12">
                        <v-text-field type="text" v-model="form.phone" label="Phone" variant="outlined"
                            density="compact" :error-messages="form.errors.phone"
                            @input="form.clearErrors('phone')"></v-text-field>
                    </v-col>
                    <v-col class="py-1" cols="12">
                        <v-text-field type="text" v-model="form.fullname" label="Fullname" variant="outlined"
                            density="compact" :error-messages="form.errors.fullname"
                            @input="form.clearErrors('fullname')"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions class="pt-0">
                <v-spacer></v-spacer>
                <v-btn color="green" text @click.native="state.show = false">Close</v-btn>
                <v-btn dark color="error darken-1" text @click.native="save">Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
import { URL } from '@/composables/url';

const { yiiUrl } = window;
const state = reactive({
    show: false,
    passwordOpen: false,
    id: null,
});

const form = useForm({
    username: '',
    password: '',
    email: '',
    phone: '',
    fullname: '',
});

function open(row) {
    form.reset();
    form.clearErrors();
    form.password = '';
    if (row) {
        state.id = row.id;
        form.username = row.username;
        form.email = row.email;
        form.phone = row.phone;
        form.fullname = row.fullname;
    } else {
        state.id = null;
        form.username = '';
        form.email = '';
        form.phone = '';
        form.fullname = '';
    }
    state.show = true;
}

function save() {
    let url = state.id ? yiiUrl.post('/admin/user/update', { id: state.id }) : yiiUrl.post('/admin/user/create');
    axios.post(url, form.data()).then(r => {
        state.show = false;
        URL.reload();
    }).catch(error => {
        form.setError(error.response.data);
    });
}

defineExpose({ open });
</script>