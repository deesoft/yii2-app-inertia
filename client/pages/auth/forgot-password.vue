<script setup>
import MainLayout from '@/layouts/Main.vue';
const { yiiUrl } = window;
const props = defineProps({
    model: Object,
});
const form = useForm({
    email: props.model.email,
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
                            <h4 class="text-center text-h6">Request Reset Password</h4>
                        </v-card-text>
                        <v-card-text>
                            <form @submit.prevent="form.post($page.url)" class="space-y-6">
                                <v-text-field type="email" v-model="form.email" required density="compact" label="Email"
                                    variant="outlined" :error-messages="form.errors.email"
                                    @input="form.clearErrors('email')">
                                </v-text-field>
                                <div>
                                    <v-btn block color="primary" type="submit" class="mb-4"
                                        :loading="form.processing">Submit</v-btn>
                                </div>
                            </form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>