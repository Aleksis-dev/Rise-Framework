<?php

namespace App\Api\Middleware;

use App\Rise\Core\Authorization\Middleware;

Middleware::register("default", function(string $token) {
    $result = Middleware::authorize($token);

    $arr = (array)$result["result"][0];

    if (!reset($arr)) {
        echo response([
            "message" => "Unauthorized!"
        ], 401);

        exit(1);
    }
});