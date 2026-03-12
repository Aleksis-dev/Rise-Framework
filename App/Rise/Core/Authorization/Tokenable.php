<?php

namespace App\Rise\Core\Authorization;

use App\Rise\Core\Database\Execution\PDO\PDOEntry;
use App\Rise\Core\Database\Execution\Queries\QueryExecutor;
use stdClass;

trait Tokenable {

    protected string $plainTextToken;

    public function createToken() {
        $className = strtolower(basename(get_class($this)));

        $id = $this->{"{$className}_id"};

        $token = bin2hex(random_bytes(100));

        $this->plainTextToken = "{$id}|{$token}";

        $data["class"] = $className;
        $data["id"] = $id;
        $data["token"] = $token;

        $stmt = "INSERT INTO access_tokens_table (tokenable_type, tokenable_id, token) VALUES (:class, :id, :token)";

        $dbh = PDOEntry::callOnce();

        QueryExecutor::execute($dbh, $stmt, null, $data);

        return $this;
    }

    protected function fetchToken() {
        $className = strtolower(basename(get_class($this)));

        $id = $this->{"{$className}_id"};

        $data["class"] = $className;
        $data["id"] = $id;

        $stmt = "SELECT token FROM access_tokens_table WHERE tokenable_type = :class AND tokenable_id = :id";

        $dbh = PDOEntry::callOnce();

        $infusedObj = new StdClass();

        QueryExecutor::execute($dbh, $stmt, $infusedObj, $data);

        return "{$id}|{$infusedObj->token}";
    }

    public function token() {
        if (!isset($this->plainTextToken)) {
            $this->plainTextToken = $this->fetchToken();
        }

        return $this->plainTextToken;
    }
}