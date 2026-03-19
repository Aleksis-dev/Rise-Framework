<?php

header('Content-Type: application/json');

require_once dirname(__DIR__) . "/App/autoload/Autoloader.php";

use App\Config\RouteLinker;
use App\Config\MiddlewareLinker;
use App\Rise\Core\Routing\Router;
use App\Rise\Core\Authorization\Middleware;
use App\Rise\Core\Requests\Request;
use App\Rise\Core\Helpers\Responses\Response;

RouteLinker::linkRoutes();
MiddlewareLinker::linkMiddleware();

$finalizedRoute;

$routeOutput = [];

$URICheck = explode("/", $_SERVER["REQUEST_URI"]);

foreach($URICheck as $key => $uriSplit) {
    if (!isset($uriSplit) || strlen($uriSplit) >= 0 && !isset($uriSplit[0]) || $uriSplit[0] !== "[") {continue;}
    $subSplit = substr($uriSplit, 1, -1);
    if (is_numeric($subSplit)) {
        $URICheck[$key] = "[int]";
        $routeOutput[] = (int) $subSplit;
    } else {
        $URICheck[$key] = "[str]";
        $routeOutput[] = $subSplit;
    }
}

$routeURI = Router::$routes[implode("/", $URICheck)] ?? null;

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

    $baseType = basename($type);

    if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
        continue;
    }

    if ($baseType === "Request") {
        $body = file_get_contents('php://input');
        $body = json_decode($body);

        $routeOutput[$key] = new Request((array) $body);
        continue;
    }

    $className = $type->getName();

    $reflection = new ReflectionClass($className);
    $constructor = $reflection->getConstructor();

    if (!isset($constructor)) {continue;}

    $routeOutput[$key] = new $className($routeOutput[$key]);
}

$middlewares = $finalizedRoute["middleware"];
$token;

if ($middlewares !== []) {
    if (!isset(getallheaders()["Authorization"])) {
        http_response_code(401);

        echo json_encode([
            "message" => "Unauthorized!"
        ]);

        exit(1);
    }
    $token = explode(" ", getallheaders()["Authorization"])[1];
}

function response(array $data, int $responseCode = 200) {
    return new Response($data, $responseCode);
}

foreach ($middlewares as $middleware) {
    Middleware::$middlewares[$middleware]($token);   
}

echo call_user_func([$controller, $method], ...$routeOutput);