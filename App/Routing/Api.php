<?php

namespace App\Routing;

use App\Rise\Core\Routing\Router;
use App\Api\Controllers\TestController;

Router::get("/", [TestController::class, "index"]);
Router::get("/info", [TestController::class, "info"]);
Router::post("/user", [TestController::class, "user"]);
Router::get("/user/[int]/post/[int]", [TestController::class, "userInfo"]);
Router::get("/user/info", [TestController::class, "info"]);

Router::middleware(["default"])->group(function () {
    Router::get("/info", [TestController::class, "info"]);
});