import { stringify } from "qs";
import { computed, ref, onMounted, onUnmounted } from "vue";

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

/**
 * 
 * @param {string} message 
 * @returns {Promise}
 */
export function confirm(message){
    return new Promise((resolve, reject)=>{
        if(window.confirm(message)){
            resolve(true);
        }else{
            reject(false);
        }
    });
}