import { Link } from "@inertiajs/vue3"
import { vMaska } from "maska/vue";

export default {
    install(app) {
        app.directive('maska', vMaska);
        app.component('Link', Link);
        app.component("RouterLink", {
            useLink(props) {
                const href = props.to.value || props.href;
                const currentUrl = computed(() => usePage().url);
                return {
                    route: computed(() => ({ href })),
                    isActive: computed(() => currentUrl.value.startsWith(href)),
                    isExactActive: computed(() => href === currentUrl.value),
                    navigate(e) {
                        if (e.shiftKey || e.metaKey || e.ctrlKey) {
                            return;
                        }
                        e.preventDefault();
                        router.visit(href);
                    },
                }
            },
        });
    },
}