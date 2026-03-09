<?php

namespace App\Api\Controllers;

use App\Api\Models\User;

class TestController {
    public function index() {
        return json_encode([
            "users" => User::all()
        ]);
    }

    public function user() {

        $user = User::create([
            "name" => "Aleksis",
            "password" => "very_secret_password",
            "coins" => 0
        ]);

        $user->createToken();

        return json_encode([
            "user" => $user,
            "token" => $user->token()
        ]);
    }

    public function userInfo(User $user, int $postId) {
        return json_encode([
            "user" => $user,
            "post_id" => $postId,
            "token" => $user->token()
        ]);
    }

    public function info() {
        return json_encode([
            "This is my info!"
        ]);
    }
}