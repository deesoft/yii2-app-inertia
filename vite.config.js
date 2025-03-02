import path from 'node:path'

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import AutoImport from 'unplugin-auto-import/vite';
import dotenv from 'dotenv';
import { spawn } from 'node:child_process';

function phpServe() {
    const port = process.env.PHP_PORT || 8080;
    const server = `localhost:${port}`;
    const doc_root = 'web';
    const php = spawn('php', ['-S', server, '-t', doc_root]);
}

export default defineConfig(({ command, mode }) => {
    if (command == 'serve' && mode == 'development' && process.env.VITE_PHP) {
        phpServe();
    }
    dotenv.config({ path: __dirname + '/.env' });
    const port = process.env.VITE_PORT;
    const origin = `${process.env.VITE_ORIGIN}:${port}`;
    return {
        plugins: [
            vue(),
            AutoImport({
                imports: [
                    'vue',
                    {
                        '@inertiajs/vue3': [
                            'usePage',
                            'router',
                            'Head',
                            'useForm',
                            'InertiaForm',
                            'useRemember',
                        ],
                        'axios': [
                            ['default', 'axios'],
                        ],
                    },
                ],
                dts: 'client/auto-imports.d.ts',
                dirs: [
                    'client/composables',
                ],
                vueTemplate: true,
            }),

            // https://github.com/antfu/unplugin-vue-components
            Components({
                // allow auto load markdown components under `./src/components/`
                extensions: ['vue',],
                // allow auto import and register components used in markdown
                include: [/\.vue$/, /\.vue\?vue/],
                dts: 'client/components.d.ts',
                dirs: ['client/components'],
            }),
        ],
        resolve: {
            alias: {
                '@/': `${path.resolve(__dirname, 'client')}/`,
            }
        },
        build: {
            rollupOptions: {
                input: './client/app.js',
            },
            manifest: true,
            outDir: 'web/dist',
            sourcemap: true,
        },
        server: {
            // force to use the port from the .env file
            strictPort: true,
            port: port,

            // define source of the images
            origin: origin,
            hmr: {
                host: 'localhost',
            },
        },
        css: {
            preprocessorOptions: {
                sass: {
                    api: 'modern-compiler' // or "modern"
                }
            }
        }
    };
});
