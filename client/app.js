import vuetify from './plugins/vuetify';
import mainPlugin from './plugins/main';
import InitApp from 'virtual:yii2-inertia';

import 'vuetify/lib/styles/main.sass';
import './assets/css/app.css';

InitApp({
    id: 'app',
    setup(app) {
        app.use(mainPlugin)
            .use(vuetify);
    }
});