<?php

namespace App\Rise\Core\Database\Execution\Queries;

use App\Rise\Core\Database\Execution\PDO\PDOConnection;
use PDOException;

class QueryExecutor {
    public static function execute(PDOConnection $dbh, string $stmt) {
        try {
            $sth = $dbh->prepare($stmt);
            $sth->execute();
            $result = $sth->fetch();
            return $result;
        } catch (PDOException $e) {
            return $e;
        }
    }
}