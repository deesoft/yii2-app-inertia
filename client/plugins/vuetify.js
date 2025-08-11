import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as labs from 'vuetify/labs/components';
import * as directives from 'vuetify/directives';

export const vuetify = createVuetify({
    components: { ...labs, ...components },
    directives,
    theme: {
        themes: {
            defaultTheme: 'light',
            light: {
                dark: false,
                colors: {
                    primary: '#009D78',
                }
            },
            dark: {
                dark: true,
            }
        }
    }
});

export default vuetify;