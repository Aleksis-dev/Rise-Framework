<?php

namespace App\Api\Controllers;

use App\Api\Models\User;

class TestController {
    public function index() {
        return json_encode([
            "Hello, World!"
        ]);
    }

    public function user(User $user) {
        return json_encode([
            "username" => $user->username,
            "password" => $user->password
        ]);
    }

    public function info() {
        return json_encode([
            "This is my info!"
        ]);
    }
}