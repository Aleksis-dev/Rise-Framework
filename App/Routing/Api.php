<?php

namespace App\Routing;

use App\Rise\Core\Routing\Router;
use App\Api\Controllers\TestController;

Router::get("/", [TestController::class, "index"]);
Router::get("/info", [TestController::class, "info"]);
Router::post("/user", [TestController::class, "user"]);