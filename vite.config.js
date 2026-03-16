import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    server: {
        host: '127.0.0.1',
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/pages/superadmin/inventory/index.js'],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            devOptions: {
                enabled: true // Allow testing service worker in dev mode
            },
            outDir: 'public/build', // Laravel specific: output manifest to public/build so it's accessible

            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg}'],
                additionalManifestEntries: [
                    { url: '/offline', revision: null }
                ],
                navigateFallback: '/offline',
                navigateFallbackDenylist: [/^\/api/, /^\/broadcasting/], // Only exclude API and Echo calls
                runtimeCaching: [
                    {
                        urlPattern: /\/offline$/,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'offline-page-cache',
                            expiration: {
                                maxEntries: 1,
                                maxAgeSeconds: 60 * 60 * 24 * 30 // 30 days
                            }
                        }
                    },
                    {
                        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'google-fonts-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365 // <== 365 days
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    {
                        urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'gstatic-fonts-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365 // <== 365 days
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    }
                ]
            },
            manifest: {
                name: 'Azventory',
                short_name: 'Azventory',
                description: 'Sistem Manajemen Inventaris Pintar',
                theme_color: '#2563eb',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/',
                scope: '/',
                icons: [
                    {
                        src: '/logo.png',
                        sizes: '192x192 512x512',
                        type: 'image/png',
                        purpose: 'any'
                    }
                ],
                shortcuts: [
                    {
                        name: 'Scan QR',
                        short_name: 'Scan',
                        description: 'Scan QR Code Barang',
                        url: '/inventory/scan-qr',
                        icons: [{ src: '/logo.png', sizes: '192x192' }]
                    },
                    {
                        name: 'Tambah Barang',
                        short_name: 'Tambah',
                        description: 'Input Barang Baru',
                        url: '/inventory/create',
                        icons: [{ src: '/logo.png', sizes: '192x192' }]
                    }
                ]
            }
        }),
    ],
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                }
            }
        }
    }
});
