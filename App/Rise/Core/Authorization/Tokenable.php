<?php

namespace App\Rise\Core\Authorization;

trait Tokenable {

    protected $plainTextToken;

    public function createToken() {
        $this->plainTextToken = bin2hex(random_bytes(100));
        return $this;
    }

    public function token() {
        return $this->plainTextToken;
    }
}