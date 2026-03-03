<?php

namespace App\Config;

use App\Rise\Core\Helpers\Files\Searching\SearchFile;

class RouteLinker {
    public static array $routeLinks = [
        "/Routing/Api.php"
    ];

    public static function linkRoutes() {
        $defaultDir = new SearchFile("")->search();
        foreach (self::$routeLinks as $routeLink) {
            require_once ($defaultDir . $routeLink);
        }
    }
}