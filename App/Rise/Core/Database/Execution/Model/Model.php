<?php

namespace App\Rise\Core\Database\Execution\Model;
use App\Rise\Core\Database\Execution\PDO\PDOEntry;
use App\Rise\Core\Database\Execution\Queries\QueryExecutor;
use stdClass;

class Model extends stdClass {

    protected $fillable;

    public static function all() {
        $class = get_called_class();

        $className = strtolower(basename($class));

        $table = "{$className}_table";

        $stmt = "SELECT * FROM {$table}";

        $dbh = new PDOEntry()->dbh;

        $queryResponse = QueryExecutor::execute($dbh, $stmt);

        if (isset($queryResponse["error"])) {
            echo json_encode($queryResponse["error"]);
            http_response_code(500);
            exit(1);
        }

        return $queryResponse["result"];
    }

    public static function create(array $data) {
        $class = get_called_class();
        $initializedObject = new $class();

        $fillable = $initializedObject->fillable ?? [];

        $fillable = array_fill_keys($fillable, true);
        
        $array_diff_keys = array_diff_key($data, $fillable);

        if ($array_diff_keys) {
            $error;
            foreach ($array_diff_keys as $key => $array_diff_key) {
                $error[] = "{$key} is not a fillable property field!";
            }
            echo json_encode(["errors" => $error]);
            exit(1);
        }

        if (isset($data["password"])) {
            $data["password"] = password_hash($data["password"], PASSWORD_ARGON2ID);
        }

        $valueNames = implode(",", array_keys($data));
        $placeholders = ":" . implode(",:", array_keys($data));

        $className = strtolower(basename($class));

        $table = "{$className}_table";

        $stmt = "INSERT INTO {$table} ($valueNames) VALUES ($placeholders)";

        $dbh = new PDOEntry()->dbh;

        $queryResponse = QueryExecutor::execute($dbh, $stmt, null, $data);

        if (isset($queryResponse["error"])) {
            echo json_encode($queryResponse["error"]);
            http_response_code(500);
            exit(1);
        }

        $lastInsertId = $queryResponse["lastInsertId"];

        $id = "{$className}_id";

        $stmt = "SELECT * FROM {$table} WHERE {$id} = {$lastInsertId}";

        $queryResponse = QueryExecutor::execute($dbh, $stmt, $initializedObject);

        if (isset($queryResponse["error"])) {
            echo json_encode($queryResponse["error"]);
            http_response_code(500);
            exit(1);
        }

        return $initializedObject;
    }
}