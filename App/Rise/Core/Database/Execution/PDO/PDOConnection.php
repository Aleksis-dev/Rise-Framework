<?php

namespace App\Rise\Core\Database\Execution\PDO;
use PDO;

class PDOConnection extends PDO {    
    public function __construct(string $driver, string $db, string $host, string $port, string $username = "root", string $password = "") {
        $dsn = "{$driver}:host={$host};port={$port};dbname={$db}";
        parent::__construct($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STATEMENT_CLASS => [PDOStatements::class]
        ]);
    }
}