<?php

namespace App\Rise\Core\CLI;

use App\Rise\Core\Helpers\Files\Searching\SearchFile;
use App\Rise\Core\Database\Execution\PDO\PDOConnection;
use App\Rise\Core\Database\Execution\Queries\QueryExecutor;
use App\env;


class Rise {
    private string $defaultDir;
    private env $env;
    private PDOConnection $dbh;

    public function __construct() {
        $this->defaultDir = new SearchFile("")->search();
        $this->env = new env();
        $this->dbh = new PDOConnection($this->env->DB_CONNECTION, $this->env->DB_NAME, $this->env->DB_HOST, $this->env->DB_PORT, $this->env->DB_USERNAME, $this->env->DB_PASSWORD);
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
            $result = QueryExecutor::execute($this->dbh, $stmt);

            $response = is_bool($result) ? "---- {$scannedName} migrated successfully.\n" : $result . "\n"; 
            $shouldExit = !is_bool($result);

            echo $response;

            if ($shouldExit) {
                exit(1);
            }
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
            $result = QueryExecutor::execute($this->dbh, $stmt);

            $response = is_bool($result) ? "---- {$scannedName} dropped successfully.\n" : $result . "\n"; 
            $shouldExit = !is_bool($result);

            echo $response;

            if ($shouldExit) {
                exit(1);
            }
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