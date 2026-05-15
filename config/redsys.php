<?php

if ( env('REDSYS_ENVIROMENT','test') == "live") {
    return [
        'key'                   => env('REDSYS_LIVE_KEY',''),
        'url_notification'      => env('REDSYS_LIVE_URL_NOTIFICATION',''),
        'url_ok'                => env('REDSYS_LIVE_URL_OK',''),
        'url_ko'                => env('REDSYS_LIVE_URL_KO',''),
        'merchantcode'          => env('REDSYS_LIVE_MERCHANT_CODE',''),
        'terminal'              => '1',
        'environment'           => 'live',
    ];
} else {
    return [
        'key'                   => env('REDSYS_TEST_KEY',''),
        'url_notification'      => env('REDSYS_TEST_URL_NOTIFICATION',''),
        'url_ok'                => env('REDSYS_TEST_URL_OK',''),
        'url_ko'                => env('REDSYS_TEST_URL_KO',''),
        'merchantcode'          => env('REDSYS_TEST_MERCHANT_CODE',''),
        'terminal'              => '1',
        'environment'           => 'test',
    ];
}
