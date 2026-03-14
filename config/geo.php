<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Geolocation Restriction Settings
    |--------------------------------------------------------------------------
    | Koordinat toko dan radius login admin (meter).
    | GAMPANG DIEDIT: Ubah angka di sini saja!
    |
    */
    
    'store_location' => [
        'lat' => -7.836331653128817,
        'lng' => 110.40218399999999,
    ],
    
'max_radius_meters' => 5000, // 5km for testing - adjust as needed
    
    /*
    |--------------------------------------------------------------------------
    | Roles yang dibatasi geolocation
    |--------------------------------------------------------------------------
    */
    'restricted_roles' => ['admin'], // Owner bypass otomatis
    
    /*
    |--------------------------------------------------------------------------
    | Error Messages (Mudah diterjemahkan)
    |--------------------------------------------------------------------------
    */
    'messages' => [
        'out_of_radius' => 'Login admin hanya diperbolehkan dalam radius 50m dari toko.',
        'location_denied' => 'Izin lokasi ditolak. Admin harus izinkan akses GPS.',
        'location_unavailable' => 'Tidak dapat mendeteksi lokasi. Coba refresh browser.',
        'success' => 'Lokasi diverifikasi. Memproses login...'
    ]
];

