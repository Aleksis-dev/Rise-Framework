<?php

header('Content-Type: application/json');

require_once dirname(__DIR__) . "/App/autoload/Autoloader.php";

use App\Config\RouteLinker;
use App\Rise\Core\Routing\Router;

RouteLinker::linkRoutes();

$finalizedRoute;

$routeOutput = [];

$URICheck = explode("/", $_SERVER["REQUEST_URI"]);

unset($URICheck[0]);

foreach($URICheck as $key => $uriSplit) {
    if (!isset($uriSplit) || strlen($uriSplit > 0) && $uriSplit[0] !== "[") {continue;}
    $subSplit = substr($uriSplit, 1, -1);
    if (is_numeric($subSplit)) {
        $URICheck[$key] = "[int]";
        $routeOutput[] = (int) $subSplit;
    } else {
        $URICheck[$key] = "[str]";
        $routeOutput[] = $subSplit;
    }
}

$routeURI = Router::$routes["/" . implode("/", $URICheck)] ?? null;

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

$reflectionMethod = new ReflectionMethod($controller, $method);
$params = $reflectionMethod->getParameters();

foreach ($params as $key => $param) {
    $type = $param->getType();

    if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
        continue;
    }

    $className = $type->getName();

    $reflection = new ReflectionClass($className);
    $constructor = $reflection->getConstructor();

    if (!isset($constructor)) {continue;}

    $routeOutput[$key] = new $className($routeOutput[$key]);
}

$middleware = $finalizedRoute["middleware"];

echo json_encode([
    "middleware" => $middleware
]);

echo call_user_func([$controller, $method], ...$routeOutput);