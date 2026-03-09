<?php

namespace App\Api\Models;
use App\Rise\Core\Database\Execution\Model\Model;
use App\Rise\Core\Authorization\Tokenable;

class User extends Model {

    use Tokenable;

    protected $fillable = [
        "name",
        "password",
        "coins"
    ];

    protected $hidden = [
        "password"
    ];
}