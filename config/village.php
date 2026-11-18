<?php

return [
    'media_driver' => env('MEDIA_DRIVER', 'database'),
    'media_disk' => env('MEDIA_DISK', 'public'),
    'media_max_kb' => (int) env('MEDIA_MAX_KB', 2048),
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin Desa'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
