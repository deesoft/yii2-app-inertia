import path from 'node:path'

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import AutoImport from 'unplugin-auto-import/vite';
import dotenv from 'dotenv';

export default defineConfig(() => {
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
                extensions: ['vue',],
                include: [/\.vue$/, /\.vue\?vue/],
                dts: 'client/components.d.ts',
                dirs: [
                    'client/components',
                ],
            }),
        ],
        resolve: {
            alias: {
                '@/': `${path.resolve(__dirname, 'client')}/`,
            }
        },
        build: {
            rollupOptions: {
                input: [
                    './client/app.js',
                ],
            },
            manifest: true,
            outDir: 'client/dist',
            sourcemap: true,
        },
        server: {
            strictPort: true,
            port: port,
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
