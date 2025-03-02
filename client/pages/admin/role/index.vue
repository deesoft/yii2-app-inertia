<script setup>
import { URL } from '@/composables/url';
import FormDlg from './FormDlg.vue';
import GrandDlg from './GrandDlg.vue';
const {toUrl} = window;

const props = defineProps({
    data: Object,
});

const columns = [
    { field: 'no', title: 'NO' },
    { field: 'type', title: 'Type', sort: 'type' },
    { field: 'name', title: 'Name', sort: 'name' },
    { field: 'description', title: 'Description', sort: 'description' },
    { field: 'action', title: 'Action' },
];

const grandDlg = useTemplateRef('grandDlg');
const formDlg = useTemplateRef('formDlg');

const type = computed({
    get() {
        return URL.queryParams.type;
    },
    set(v) {
        URL.reload({ type: v }, { preserveScroll: true, preserveState: true });
    }
});
const types = [
    { value: '', title: 'All' },
    { value: '1', title: 'Role' },
    { value: '2', title: 'Permission' },
];
function deleteRow(row) {
    confirm('Yakin akan menghapus data ini?').then(() => {
        axios.post(toUrl.post('admin/role/delete', { name: row.name })).then(res=>{
            URL.reload();
        });
    });
}

</script>
<template>
    <v-container fluid>
        <v-row dense>
            <v-col cols="12">
                <p>
                    <Link :href="URL.home" class="text-decoration-none"><v-icon>mdi-home</v-icon></Link> /
                    <span>List Role</span>
                </p>
            </v-col>
            <v-col cols="12">
                <v-card>
                    <v-toolbar density="default">
                        <v-btn density="compact" icon="mdi-reload" @click="URL.reload()">
                        </v-btn>
                        <v-toolbar-title>Role</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-toolbar-items>
                            <v-select hide-details v-model="type" :items="types" style="min-width: 160px;"></v-select>
                            <QuerySearchText reload style="min-width: 250px;"></QuerySearchText>
                        </v-toolbar-items>
                        <v-btn density="compact" icon="mdi-plus" @click="formDlg.open()">
                        </v-btn>
                    </v-toolbar>
                    <v-divider />
                    <GridView :data="data" :columns="columns" reload>
                        <template #d-no="row">{{ row._no }}</template>
                        <template #d-type="row">{{ row.type == 1 ? 'Role' : 'Permission' }}</template>
                        <template #d-action="row">                            
                            <v-btn density="compact" size="small" icon="mdi-pencil" @click="formDlg.open(row)"></v-btn>                            
                            <v-btn density="compact" size="small" icon="mdi-cog" @click="grandDlg.open(row.name)"></v-btn>
                            <v-btn density="compact" size="small" icon="mdi-delete" @click="deleteRow(row)"></v-btn>
                        </template>
                    </GridView>
                </v-card>
            </v-col>
        </v-row>
        <GrandDlg ref="grandDlg"></GrandDlg>
        <FormDlg ref="formDlg"></FormDlg>
    </v-container>
</template>