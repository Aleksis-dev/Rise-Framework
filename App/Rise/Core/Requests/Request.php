<?php

namespace App\Rise\Core\Requests;

class Request {
    public function __get($name) {
        return "error";
    }

    public function __construct(array $data) {
        foreach ($data as $key => $data) {
            $this->{$key} = $data;
        }
    }
}