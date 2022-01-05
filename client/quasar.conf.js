// Configuration for your app
// https://quasar.dev/quasar-cli/quasar-conf-js
const webpack = require('webpack');

module.exports = function (ctx) {
    return {
        // app boot file (/src/boot)
        // --> boot files are part of "main.js"
        boot: [
            'i18n',
            'axios',
        ],

        css: [
            'app.sass',
        ],

        extras: [
            'roboto-font', // optional, you are not bound to it
            'material-icons', // optional, you are not bound to it
        ],

        framework: {
            // iconSet: 'ionicons-v4',
            lang: 'ru', // Quasar language

            // Quasar plugins
            plugins: [
                'Loading',
                'LocalStorage',
                'Dialog',
                'Notify',
            ],
            config: {
                screen: {
                    bodyClasses: true,
                },
            },
        },

        build: {
            scopeHoisting: true,
            // vueRouterMode: 'history',
            // vueCompiler: true,
            // gzip: true,
            // analyze: true,
            // extractCSS: false,
            extendWebpack(cfg) {
                cfg.plugins.push(
                  new webpack.ProvidePlugin({
                      devlog: 'src/tools/devlog',
                  }),
                );
            },
            env: {
                API: ctx.dev
                  ? 'http://sp.com.ua/'
                  : 'https://server007cargo.net.ua/',
            },
        },

        devServer: {
            // https: true,
            // port: 8080,
            open: false, // opens browser window automatically
        },

        ssr: {
            pwa: false,
        },

        pwa: {
            // workboxPluginMode: 'InjectManifest',
            // workboxOptions: {}, // only for NON InjectManifest
            manifest: {
                // name: 'Quasar App',
                // short_name: 'Quasar App',
                // description: 'A Quasar Framework app',
                display: 'standalone',
                orientation: 'portrait',
                background_color: '#ffffff',
                theme_color: '#027be3',
                icons: [
                    {
                        src: 'icons/icon-128x128.png',
                        sizes: '128x128',
                        type: 'image/png',
                    },
                    {
                        src: 'icons/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: 'icons/icon-256x256.png',
                        sizes: '256x256',
                        type: 'image/png',
                    },
                    {
                        src: 'icons/icon-384x384.png',
                        sizes: '384x384',
                        type: 'image/png',
                    },
                    {
                        src: 'icons/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                ],
            },
        },
    };
};
