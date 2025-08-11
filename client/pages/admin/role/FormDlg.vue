<template>
    <v-dialog v-model="state.show" @keydown.esc="state.show = false" max-width="450">
        <v-card>
            <v-toolbar class="gradient-orange" density="compact" flat>
                <v-toolbar-title class="white--text">
                    {{ state.name ? `Edit Role '${state.name}'` : 'New Role' }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-row>
                    <v-col class="py-1" cols="12">
                        <v-select v-model="form.type" label="Type" variant="outlined" density="compact" required
                            :error-messages="form.errors.type" :items="types" :readonly="!!state.name"></v-select>
                    </v-col>
                    <v-col class="py-1" cols="12">
                        <v-text-field type="text" v-model="form.name" label="Name" variant="outlined" density="compact"
                            required :rules="[nameValidator]" :error-messages="form.errors.name"
                            @input="form.clearErrors('name')"></v-text-field>
                    </v-col>
                    <v-col class="py-1" cols="12">
                        <v-text-field type="text" v-model="form.description" label="Description" variant="outlined"
                            density="compact" :error-messages="form.errors.description"
                            @input="form.clearErrors('description')"></v-text-field>
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
const {yiiUrl} = window;

const NamePattern = /^\w[\w-]*$/;
const state = reactive({
    show: false,
    name: null,
});

const types = [
    { value: 1, title: 'Role' },
    { value: 2, title: 'Permission' },
];

const form = useForm({
    type: 1,
    name: '',
    description: '',
});

function nameValidator(val) {
    if (!val) {
        return 'Name is required.';
    }
    return NamePattern.test(val) || 'Only latter or - are allowed.'
}

function open(row) {
    form.reset();
    form.clearErrors();
    if (row) {
        state.name = row.name;
        form.type = row.type;
        form.name = row.name;
        form.description = row.description;
    } else {
        state.name = null;
        form.type = 1;
        form.name = '';
        form.description = '';
    }
    state.show = true;
}

const createUrl = yiiUrl.post('admin/role/create');
function save() {
    let url = state.name ? yiiUrl.post('/admin/role/update', { name: state.name }) : createUrl;
    axios.post(url, form.data()).then(r => {
        state.show = false;
        URL.reload();
    }).catch(error => {
        form.setError(error.response.data);
    });
}

defineExpose({ open });
</script>