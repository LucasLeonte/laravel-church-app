<?php

namespace App\Http\Controllers;

use RuntimeException;

class PostController extends Controller
{
    public function __construct()
    {
        throw new RuntimeException('PostController has been removed. Use NewsController or ResourcesController instead.');
    }
}
