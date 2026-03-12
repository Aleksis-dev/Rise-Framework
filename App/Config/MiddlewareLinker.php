<?php

namespace App\Config;

use App\Rise\Core\Helpers\Files\Searching\SearchFile;

class MiddlewareLinker {
    public static array $middlewareLinks = [
        "/Api/Middleware/Middleware.php"
    ];

    public static function linkMiddleware() {
        $defaultDir = new SearchFile("")->search();
        foreach (self::$middlewareLinks as $middlewareLink) {
            require_once ($defaultDir . $middlewareLink);
        }
    }
}