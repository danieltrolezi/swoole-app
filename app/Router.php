<?php

namespace App;

use App\Exceptions\NotFoundException;

class Router
{
    public function __construct(
        private array $routes = []
    ){
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function dispatch(string $path): mixed
    {
        if (isset($this->routes[$path])) {
            return call_user_func([
                new $this->routes[$path][0],
                $this->routes[$path][1]
            ]);
        }
        
        throw new NotFoundException($path);
    }
}