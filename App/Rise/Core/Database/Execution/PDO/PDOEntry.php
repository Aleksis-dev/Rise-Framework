<?php

namespace App\Rise\Core\Database\Execution\PDO;
use App\Rise\Core\Database\Execution\PDO\PDOConnection;
use App\env;

class PDOEntry {
    public PDOConnection $dbh;
    protected env $env;

    public function __construct() {
        $this->env = new env();
        $this->dbh = new PDOConnection($this->env->DB_CONNECTION, $this->env->DB_NAME, $this->env->DB_HOST, $this->env->DB_PORT, $this->env->DB_USERNAME, $this->env->DB_PASSWORD);
    }

    public static function callOnce() {
        $env = new env();
        return new PDOConnection($env->DB_CONNECTION, $env->DB_NAME, $env->DB_HOST, $env->DB_PORT, $env->DB_USERNAME, $env->DB_PASSWORD);
    }
    
}