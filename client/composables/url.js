import { stringify, parse } from "qs";
import { router } from "@inertiajs/vue3";

if(typeof window.stringify === 'undefined'){
    window.stringify = stringify;
}

export const toUrl = window.toUrl;
export const URL = reactive({
    home:computed(()=>window.toUrl('')),
    current: computed(() => usePage().url),
    path: computed(() => usePage().url.split('?')[0]),
    queryParams: computed(() => {
        let s = usePage().url.split('?')[1];
        return s ? parse(s) : {};
    }),
    /**
     * Alias to Build url
     * @param {string} path 
     * @param {object} params 
     * @param {string} method 
     * @returns 
     */
    to(path, params, method) {
        return window.toUrl(path, params, method);
    },
    /**
     * 
     * @param {object} params 
     * @param {object} options 
     */
    reload(params, options) {
        const query = stringify({ ...this.queryParams, ...(params || {}) });
        let url = this.path + (query ? '?' + query : '');
        return router.get(url, {}, options || {});
    },
    back() {
        return window.history.back();
    },
    remote(v, format) {
        const REGEX = /^[a-z0-9]{1,16}$/
        if (REGEX.test(v)) {
            return '@base/file/' + v + (format ? '/' + format : '')
        }
        return v
    }
});

export function route(path, params, method) {
    return window.toUrl(path, params, method);
}