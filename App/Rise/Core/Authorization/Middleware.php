<?php

namespace App\Rise\Core\Authorization;

use App\Rise\Core\Database\Execution\PDO\PDOEntry;
use App\Rise\Core\Database\Execution\Queries\QueryExecutor;
use stdClass;

class Middleware {

    public static array $middlewares = [];

    public static function register(string $name, callable $callback) {
        self::$middlewares[$name] = $callback;
    }

    public static function authorize(string $token) {
        $tokenBreak = 0;
        $tokenID = "";
        $cleanToken = "";

        for ($i = 0; $i < strlen($token); $i++) {
            if ($token[$i] == "|") {
                $tokenBreak = $i + 1;
                break;
            }

            $tokenID .= $token[$i];
        }

        for ($i = $tokenBreak; $i < strlen($token); $i++) {
            $cleanToken .= $token[$i];
        }

        $data["token"] = $cleanToken;
        $data["token_id"] = $tokenID;

        $stmt = "SELECT EXISTS (SELECT token FROM access_tokens_table WHERE tokenable_id = :token_id AND token = :token)";

        $dbh = PDOEntry::callOnce();

        $result = QueryExecutor::execute($dbh, $stmt, null, $data);

        return $result;
    }
}