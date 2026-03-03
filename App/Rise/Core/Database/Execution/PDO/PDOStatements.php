<?php

namespace App\Rise\Core\Database\Execution\PDO;
use PDOStatement;
use PDO;

use App\Rise\Core\Database\Execution\PDO\PDOConnection;

class PDOStatements extends PDOStatement {
    public $dbh;

    protected function __construct() {
        $this->setFetchMode(PDO::FETCH_OBJ);
    }
}