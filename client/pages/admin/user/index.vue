<script setup>
import { URL } from '@/composables/url';
import { confirm } from '@/composables/global';
import FormDlg from './FormDlg.vue';
import GrandDlg from './GrandDlg.vue';
const { yiiUrl } = window;

const props = defineProps({
    data: Object,
});
const columns = [
    { field: 'no', title: 'NO' },
    { field: 'id', title: 'Id', sort: 'id' },
    { field: 'username', title: 'Username', sort: 'username' },
    //{field: 'auth_key', title: 'Auth Key', sort: 'auth_key'},
    //{field: 'password_hash', title: 'Password Hash', sort: 'password_hash'},
    //{field: 'password_reset_token', title: 'Password Reset Token', sort: 'password_reset_token'},
    { field: 'email', title: 'Email', sort: 'email' },
    { field: 'phone', title: 'Phone', sort: 'phone' },
    { field: 'fullname', title: 'Fullname', sort: 'fullname' },
    // {field: 'avatar', title: 'Avatar', sort: 'avatar'},
    // {field: 'active', title: 'Active', sort: 'active'},
    { field: 'action', title: 'Action' },
];

function deleteRow(row) {
    confirm('Yakin akan menghapus data ini?').then(() => {
        axios.post(yiiUrl.post('/admin/user/delete', { id: row.id })).then(() => URL.reload());
    });
}

const formDlg = useTemplateRef('formDlg');
const grandDlg = useTemplateRef('grandDlg');
</script>
<template>
    <v-container fluid>
        <v-row dense>
            <v-col cols="12">
                <p>
                    <Link :href="yiiUrl.home" class="text-decoration-none"><v-icon>mdi-home</v-icon></Link> /
                    <span>List User</span>
                </p>
            </v-col>
            <v-col cols="12">
                <v-card>
                    <v-toolbar density="default">
                        <v-btn density="compact" icon="mdi-reload" @click="reloadPage()">
                        </v-btn>
                        <v-toolbar-title>User</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-toolbar-items>
                            <QuerySearchText reload style="min-width: 250px;"></QuerySearchText>
                        </v-toolbar-items>
                        <v-btn density="compact" icon="mdi-plus" @click="formDlg.open()">
                        </v-btn>
                    </v-toolbar>
                    <v-divider />
                    <GridView :data="data" :columns="columns" reload>
                        <template #d-no="{line}">{{ line }}</template>
                        <template #d-action="{row}">
                            <v-btn density="compact" size="small" icon="mdi-pencil" @click="formDlg.open(row)"></v-btn>
                            <v-btn density="compact" size="small" icon="mdi-cog" @click="grandDlg.open(row)"></v-btn>
                            <v-btn density="compact" size="small" icon="mdi-delete" @click="deleteRow(row)"></v-btn>
                        </template>
                    </GridView>
                </v-card>
            </v-col>
        </v-row>
        <FormDlg ref="formDlg"></FormDlg>
        <GrandDlg ref="grandDlg"></GrandDlg>
    </v-container>
</template>