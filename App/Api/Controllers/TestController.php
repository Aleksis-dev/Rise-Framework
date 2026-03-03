<?php

namespace App\Api\Controllers;

class TestController {
    public function index() {
        return json_encode([
            "Hello, World!"
        ]);
    }

    public function info() {
        return json_encode([
            "This is my info!"
        ]);
    }
}