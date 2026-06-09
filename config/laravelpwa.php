<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name'             => env('APP_NAME', 'SIPRT'),
        'short_name'       => 'SIPRT',
        'start_url'        => '/',
        'background_color' => '#f4f6fb',
        'theme_color'      => '#1565C0',
        'display'          => 'standalone',
        'orientation'      => 'portrait',
        'status_bar'       => 'black-translucent',
        'icons' => [
            '72x72' => [
                'path'    => '/favicon/favicon-32x32.png',
                'purpose' => 'any',
            ],
            '96x96' => [
                'path'    => '/favicon/favicon-32x32.png',
                'purpose' => 'any',
            ],
            '128x128' => [
                'path'    => '/favicon/favicon-32x32.png',
                'purpose' => 'any',
            ],
            '144x144' => [
                'path'    => '/favicon/favicon-32x32.png',
                'purpose' => 'any',
            ],
            '152x152' => [
                'path'    => '/favicon/apple-touch-icon.png',
                'purpose' => 'any',
            ],
            '192x192' => [
                'path'    => '/favicon/android-chrome-192x192.png',
                'purpose' => 'any maskable',
            ],
            '384x384' => [
                'path'    => '/favicon/android-chrome-512x512.png',
                'purpose' => 'any maskable',
            ],
            '512x512' => [
                'path'    => '/favicon/android-chrome-512x512.png',
                'purpose' => 'any maskable',
            ],
        ],
        'splash' => [
            '640x1136'  => '/images/icons/splash-640x1136.png',
            '750x1334'  => '/images/icons/splash-750x1334.png',
            '828x1792'  => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name'        => 'Dashboard Teknisi',
                'description' => 'Lihat tugas aktif saya',
                'url'         => '/dashboard/teknisi/my',
                'icons'       => [
                    'src'     => '/favicon/favicon-32x32.png',
                    'purpose' => 'any',
                ],
            ],
            [
                'name'        => 'Buat Laporan',
                'description' => 'Buat laporan kerja baru',
                'url'         => '/laporan/create',
                'icons'       => [
                    'src'     => '/favicon/favicon-32x32.png',
                    'purpose' => 'any',
                ],
            ],
        ],
        'custom' => [],
    ],
];
