<?php

namespace App\Rise\Core\CLI;

use App\Rise\Core\Helpers\Files\Searching\SearchFile;
use App\Rise\Core\Database\Execution\PDO\PDOEntry;
use App\Rise\Core\Database\Execution\Queries\QueryExecutor;
use App\Rise\Core\Database\Execution\PDO\PDOConnection;

class Rise {
    private string $defaultDir;
    private PDOConnection $dbh;

    public function __construct() {
        $this->defaultDir = new SearchFile("")->search();
        $this->dbh = new PDOEntry()->dbh;
    }

    public function migrate() {
        $dir = $this->defaultDir . "/Database/Migrations";
        $scannedObjects = scandir($dir);
        $scannedObjects = array_slice($scannedObjects, 2);

        echo "Running Migrations...\n\n";
        
        foreach ($scannedObjects as $key => $scannedObject) {
            $scannedName = str_replace(".php", "", $scannedObject);
            $className = "App\\Database\\Migrations\\" . $scannedName;
            $obj = new $className();

            $stmt = call_user_func([$obj, "up"]);
            $queryResponse = QueryExecutor::execNonTransactional($this->dbh, $stmt);

            if (isset($queryResponse["error"])) {
                print_r($queryResponse["error"]);
                exit(1);
            }

            $response = "---- {$scannedName} migrated successfully.\n";
            echo $response;
        }

        echo "\nMigrations successfully completed.";
    }

    public function dropTables() {
        $dir = $this->defaultDir . "/Database/Migrations";
        $scannedObjects = scandir($dir);
        $scannedObjects = array_slice($scannedObjects, 2);

        echo "Dropping Tables...\n\n";

        foreach ($scannedObjects as $key => $scannedObject) {
            $scannedName = str_replace(".php", "", $scannedObject);
            $className = "App\\Database\\Migrations\\" . $scannedName;
            $obj = new $className();

            $stmt = call_user_func([$obj, "down"]);
            $queryResponse = QueryExecutor::execNonTransactional($this->dbh, $stmt);

            if (isset($queryResponse["error"])) {
                print_r($queryResponse["error"]);
                exit(1);
            }

            $response = "---- {$scannedName} dropped successfully.\n";
            echo $response;
        }

        echo "\nTables dropped successfully.";
    }

    public function serve() {
        $file = dirname($this->defaultDir) . '/Public/Index.php';

        shell_exec("php -S localhost:8000 {$file}");
    }

    public function __call(string $name, array $args) {
        echo "$name command not found! (use php rise help)";
        exit(1);
    }
}