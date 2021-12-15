<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => '141216129759690',
        'client_secret' => 'e29505329924f64930afa347f24ea960',
        'redirect' => ''.env('APP_NAME_URL').'/login/facebook/callback',
    ],
    'google' => [
        'client_id' => '296935629238-5n6ph7ql8ofdk4247k4ve6o8lsvat7jc.apps.googleusercontent.com',
        'client_secret' => 'TWRC2buGPDW9AZieKRnhPX_l',
        'redirect' => ''.env('APP_NAME_URL').'/login/google/callback',
    ],

];
