<template>
    <v-text-field v-model="inpModel" v-maska="option"></v-text-field>
</template>
<script setup>
const model = defineModel();

function formatNumber(val) {
    if (!val) return "";
    let frac = '';
    val = val.toString();
    let p = val.indexOf('.');
    if (p === 0) {
        val = '0' + val;
        p++;
    }
    if (p > 0) {
        frac = val.slice(p, p + 3);
        val = val.slice(0, p);
    }
    return numeral(parseFloat(val)).format('0,0') + frac;
}
const inpModel = computed({
    get() {
        return formatNumber(model.value);
    },
    set(val) {
        model.value = parseFloat(val.replace(/[$,]/g, ""));
    }
});

const option = {
    mask: '9.00',
    preProcess: (val) => {
        return val.replace(/[$,]/g, "");
    },
    postProcess: (val) => {
        return formatNumber(val);
    },
    tokens: {
        0: { pattern: /[0-9]/, optional: true },
        9: { pattern: /[0-9]/, multiple: true },
    }
};
</script>