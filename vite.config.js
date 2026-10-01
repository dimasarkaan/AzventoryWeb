import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    server: {
        host: '127.0.0.1',
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/print.css', 'resources/js/app.js', 'resources/js/pages/superadmin/inventory/index.js'],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: false, // We handle SW registration manually in app.js
            devOptions: {
                enabled: false // Disable SW in dev mode to avoid precaching issues
            },
            outDir: 'public/build', // Keep everything in public/build so relative paths work

            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg}'],
                additionalManifestEntries: [
                    { url: '/offline', revision: null }
                ],
                // navigateFallback DIHAPUS — agar SW tidak redirect semua navigasi ke /offline
                // Fallback ke /offline hanya terjadi saat network benar-benar gagal (via runtimeCaching)
                navigateFallback: null,
                runtimeCaching: [
                    {
                        // Navigasi halaman: coba network dulu, fallback ke /offline hanya jika gagal
                        urlPattern: ({request}) => request.mode === 'navigate',
                        handler: 'NetworkOnly',
                        options: {
                            plugins: [
                                {
                                    handlerDidError: async () => {
                                        return caches.match('/offline');
                                    }
                                }
                            ]
                        }
                    },
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
                                maxAgeSeconds: 60 * 60 * 24 * 365 // <= 365 days
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
                                maxAgeSeconds: 60 * 60 * 24 * 365 // <= 365 days
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
