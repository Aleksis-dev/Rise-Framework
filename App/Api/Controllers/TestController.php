<?php

namespace App\Api\Controllers;

use App\Api\Models\User;

class TestController {
    public function index() {
        return json_encode([
            "users" => User::all()
        ]);
    }

    public function user(User $user) {
        return json_encode([
            "user" => $user
        ]);
    }

    public function info() {
        return json_encode([
            "This is my info!"
        ]);
    }
}