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

    public static function execute(PDOConnection $dbh, string $stmt, ?string $className = null, bool $single = false, array $data = []) {
        try {
            $dbh->beginTransaction();
            $sth = $dbh->prepare($stmt);
            $sth->execute($data);

            if (isset($className)) {
                if ($single) {
                    $result["result"] = $sth->fetchObject($className);
                } else {
                    $result["result"] = $sth->fetchAll(PDO::FETCH_CLASS, $className);
                }
            } else {
                $result["result"] = $single
                ? $sth->fetch(PDO::FETCH_ASSOC)
                : $sth->fetchAll(PDO::FETCH_ASSOC);
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