<?php

header('Content-Type: application/json');

require_once dirname(__DIR__) . "/App/autoload/Autoloader.php";

use App\Config\RouteLinker;
use App\Rise\Core\Routing\Router;

RouteLinker::linkRoutes();

$finalizedRoute;

$routeURI = Router::$routes[$_SERVER["REQUEST_URI"]] ?? null;

if ($routeURI) {
    $finalizedRoute = $routeURI[$_SERVER["REQUEST_METHOD"]] ?? null;
} else {
    echo json_encode("404 Not Found");
    http_response_code(404);
    exit(1);
}

if (!$finalizedRoute) {
    echo json_encode([
        "error" => "Method Not Allowed!",
        "allowed_methods" => array_keys($routeURI)
    ]);

    http_response_code(405);
    exit(1);
}

$controller = new $finalizedRoute[0];
$method = $finalizedRoute[1];

echo call_user_func([$controller, $method]);