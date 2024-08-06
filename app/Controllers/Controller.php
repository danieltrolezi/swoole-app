<?php

namespace App\Controllers;

use App\Servers\HttpServer;

abstract class Controller
{
    protected HttpServer $application;

    public function __construct()
    {
        $this->application = HttpServer::getInstance();
    }
}