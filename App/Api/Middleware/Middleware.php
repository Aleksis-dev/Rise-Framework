<?php

namespace App\Api\Middleware;

use App\Rise\Core\Authorization\Middleware;

Middleware::register("default", function(string $token) {
    $result = Middleware::authorize($token);

    $arr = (array)$result["result"][0];

    if (!reset($arr)) {
        http_response_code(401);

        echo json_encode([
            "message" => "Unauthorized!"
        ]);

        exit(1);
    }
});