<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

if (!function_exists('isAdmin')) //helper files can be loaded multiple times
{
    function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }
}

