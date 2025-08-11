import { stringify } from "qs";
import { router, usePage } from "@inertiajs/vue3";
import { reactive, computed, ref, onMounted, onUnmounted } from "vue";
const { yiiUrl } = window;

const STORAGE_KEY = '__theme';
const theme = ref(localStorage.getItem(STORAGE_KEY));

export const darkMode = computed({
    get() {
        return theme.value == 'dark';
    },
    set(value) {
        theme.value = value ? 'dark' : 'light';
        localStorage.setItem(STORAGE_KEY, theme.value);
    }
});

if (typeof window.stringify === 'undefined') {
    window.stringify = stringify;
}

export const URL = reactive({
    current: computed(() => usePage().url),
    route: computed(() => usePage().props.$r[0]),
    params: computed(() => usePage().props.$r[1]),
    /**
     * 
     * @param {object} params 
     * @param {object} options 
     */
    reload(params, options) {
        let url = yiiUrl(this.route, { ...this.params, ...(params || {}) });
        return router.get(url, {}, options || {});
    },
});

class Bus {
    constructor() {
        this.events = {};
    }

    /**
     * Register event
     * @param {string} name 
     * @param {Function} fn 
     */
    on(name, fn) {
        const events = this.events;
        onMounted(() => {
            events[name] = events[name] || [];
            events[name].push(fn);
        });
        onUnmounted(() => {
            if (events[name]) {
                events[name] = events[name].filter(f => f !== fn);
            }
        });
    }

    /**
     * Trigger event
     * @param {string} name 
     */
    emit(name) {
        const args = arguments.slice(1);
        var th = this;
        if (this.events[name]) {
            this.events[name].forEach((fn) => fn.apply(th, args));
        }
    }
}

export const $bus = new Bus();
