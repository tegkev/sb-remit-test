<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'version' => '1.0.0',
        'resources' => ['blogs']
    ];
});
