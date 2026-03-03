<?php

namespace App\Rise\Core\Routing;

class Router {
    public static array $routes = [];

    public static function register(string $method, array $args) {
        self::$routes[$args[0]][strtoupper($method)] = $args[1];
    }

    public static function __callStatic(string $name, array $args) {
        self::register($name, $args);
    }
}