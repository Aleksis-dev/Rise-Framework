<?php

namespace App\Rise\Core\Routing;

class Router {
    public static array $routes = [];
    public static array $middleware = [];
    public static string $controller;

    public static function register(string $method, array $args) {
        $arg1;
        $arg2;

        if (isset(self::$controller)) {
            $arg1 = self::$controller;
            $arg2 = $args[1][0];
        } else {
            $arg1 = $args[1][0];
            $arg2 = $args[1][1];
        }

        self::$routes[$args[0]][strtoupper($method)] = [$arg1, $arg2];
        self::$routes[$args[0]][strtoupper($method)]["middleware"] = self::$middleware;
    }

    public static function __callStatic(string $name, array $args) {
        self::register($name, $args);
    }

    public static function middleware(array $middleware) {
        self::$middleware = $middleware;
        return new self();
    }

    public static function controller(string $controller) {
        self::$controller = $controller;
        return self;
    }

    public function group(callable $callback) {
        $callback();
    }


}