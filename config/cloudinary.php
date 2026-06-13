<?php

/*
 * Configuration Cloudinary pour CauriMarket
 *
 * Le package cloudinary-labs/cloudinary-laravel v3 utilise CLOUDINARY_URL
 * au format : cloudinary://API_KEY:API_SECRET@CLOUD_NAME
 *
 * Sur Render, les variables CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET et
 * CLOUDINARY_CLOUD_NAME sont définies. On les combine ici.
 */

$cloudName   = env('CLOUDINARY_CLOUD_NAME');
$apiKey      = env('CLOUDINARY_API_KEY');
$apiSecret   = env('CLOUDINARY_API_SECRET');

return [

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    | URL de connexion Cloudinary (format officiel)
    | Si CLOUDINARY_URL n'est pas défini, on le construit depuis les variables séparées.
    */
    'cloud_url' => env(
        'CLOUDINARY_URL',
        ($cloudName && $apiKey && $apiSecret)
            ? "cloudinary://{$apiKey}:{$apiSecret}@{$cloudName}"
            : null
    ),

    /*
    | Accès direct aux paramètres (utilisé par notre code via config())
    */
    'cloud_name' => $cloudName,
    'api_key'    => $apiKey,
    'api_secret' => $apiSecret,
    'secure'     => true,

    'upload_preset'  => env('CLOUDINARY_UPLOAD_PRESET'),
    'upload_route'   => env('CLOUDINARY_UPLOAD_ROUTE'),
    'upload_action'  => env('CLOUDINARY_UPLOAD_ACTION'),
];
