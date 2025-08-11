<template>
    <v-dialog v-model="state.show" @keydown.esc="state.show = false" max-width="600">
        <v-card>
            <v-toolbar dark :color="state.color" class="gradient-orange" density="compact" flat>
                <v-toolbar-title class="white--text">
                    Assign User '{{ state.username }}'
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-row>
                    <v-col cols="6">
                        <v-card variant="outlined">
                            <v-card-title>
                                <v-text-field append-icon="mdi-chevron-double-right" hide-details density="compact"
                                    variant="outlined" v-model="left.search"
                                    @click:append="doSave('assign')"></v-text-field>
                            </v-card-title>
                            <v-card-text>
                                <ListBox multiple size="16" v-model="left.selected" :items="left.items"
                                    :search="left.search">
                                </ListBox>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="6">
                        <v-card variant="outlined">
                            <v-card-title>
                                <v-text-field prepend-icon="mdi-chevron-double-left" hide-details density="compact"
                                    variant="outlined" v-model="right.search"
                                    @click:prepend="doSave('revoke')"></v-text-field>
                            </v-card-title>
                            <v-card-text>
                                <ListBox multiple size="16" v-model="right.selected" :items="right.items"
                                    :search="right.search">
                                </ListBox>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions class="pt-0">
                <v-spacer></v-spacer>
                <v-btn color="green" text @click.native="state.show = false">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
const { yiiUrl } = window;

const state = reactive({
    show: false,
    id: null,
    username: null,
});

const left = reactive({
    search: '',
    items: [],
    selected: [],
});
const right = reactive({
    search: '',
    items: [],
    selected: [],
});

function open(row) {
    axios.get(yiiUrl('/admin/user/data', { id: row.id })).then(r => {
        state.id = row.id;
        state.username = row.username;
        left.search = '';
        left.selected = [];
        left.items = r.data.available;

        right.search = '';
        right.selected = [];
        right.items = r.data.assigned;

        state.show = true;
    });
}

function doSave(action) {
    let url = yiiUrl.post(`/admin/user/${action}`, { id: state.id });
    const items = action == 'assign' ? left.selected : right.selected;
    if (items.length > 0) {
        axios.post(url, { items }).then(r => {
            left.selected = [];
            left.items = r.data.available;

            right.selected = [];
            right.items = r.data.assigned;
        });
    }
}

defineExpose({ open });
</script>