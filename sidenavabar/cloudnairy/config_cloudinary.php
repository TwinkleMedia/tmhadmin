<?php
// For debugging: log errors to a file instead of displaying them



require __DIR__ . '/vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => getenv('dh9dpvul4'),
        'api_key'    => getenv('913163688842134'),
        'api_secret' => getenv('FR5RjEj7it70xfBMnT53mgW-uds')
    ],
    'url' => [
        'secure' => true
    ]
]);
