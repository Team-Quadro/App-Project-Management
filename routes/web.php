<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__ . '/superadmin.php';
require __DIR__ . '/tenant.php';
require __DIR__ . '/user.php';

require __DIR__ . '/auth.php';