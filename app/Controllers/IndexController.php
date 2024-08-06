<?php

namespace App\Controllers;

class IndexController extends Controller
{
    public function home(): string
    {
       return 'Hello Swoole. #' . rand(1000, 9999);
    }
}