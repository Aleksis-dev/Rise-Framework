<?php

namespace App\Rise\Core\Helpers\Responses;

class Response {
    
    private array $data;
    private int $responseCode;
    private array $headers;

    public function __construct(array $data = [], int $responseCode = 200) {
        $this->data = $data;
        $this->responseCode = $responseCode;
    }

    public function __toString() {
        http_response_code($this->responseCode);
        return json_encode($this->data);
    }
}