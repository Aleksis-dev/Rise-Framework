<?php

namespace App\Api\Models;
use App\Rise\Core\Database\Execution\Model\Model;

class User extends Model {
    protected $fillable = [
        "name",
        "password",
        "coins"
    ];

    protected $hidden = [
        "password"
    ];
}