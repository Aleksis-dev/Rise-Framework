<?php

namespace App\Rise\Core\Database\Execution\Queries;

use App\Rise\Core\Database\Execution\PDO\PDOConnection;
use PDO;
use PDOException;
use stdClass;

class QueryExecutor {

    public static function execNonTransactional(PDOConnection $dbh, string $stmt) {
        try {
            $sth = $dbh->prepare($stmt);
            $sth->execute();
            $result["result"] = $sth->fetchAll();
            if (strtok($stmt, " ") == "INSERT") {
                $result["lastInsertId"] = $dbh->lastInsertId();
            }
            return $result;
        } catch (PDOException $e) {
            $result["error"] = ["error" => $e->getMessage()];
        }

        return $result;
    }

    public static function execute(PDOConnection $dbh, string $stmt, ?object $infusedObject = null, array $data = []) {
        try {
            $dbh->beginTransaction();
            $sth = $dbh->prepare($stmt);
            $sth->execute($data);
            if (isset($infusedObject)) {
                $sth->setFetchMode(PDO::FETCH_INTO, $infusedObject);
                $result["result"] = $sth->fetch();
            } else {
                $result["result"] = $sth->fetchAll();
            }
            if (strtok($stmt, " ") == "INSERT") {
                $result["lastInsertId"] = $dbh->lastInsertId();
            }
            $dbh->commit();
            return $result;
        } catch (PDOException $e) {
            if ($dbh->inTransaction()) {
                $dbh->rollBack();
            }
            $result["error"] = ["error" => $e->getMessage()];
        }

        return $result;
    }
}