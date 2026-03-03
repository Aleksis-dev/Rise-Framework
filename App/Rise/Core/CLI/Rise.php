<?php

namespace App\Rise\Core\CLI;

use App\Rise\Core\Helpers\Files\Searching\SearchFile;
use App\Rise\Core\Database\Execution\PDO\PDOConnection;
use App\Rise\Core\Database\Execution\PDO\PDOStatements;

use App\env;

class Rise {

    public string $defaultDir;

    public function __construct() {
        $this->defaultDir = new SearchFile("")->search();
    }

    public function migrate() {
        $dir = $this->defaultDir . "/Database/Migrations";
        $scannedObjects = scandir($dir);
        $scannedObjects = array_slice($scannedObjects, 2);
        
        foreach ($scannedObjects as $key => $scannedObject) {
            $className = "App\\Database\\Migrations\\" . str_replace(".php", "", $scannedObject);
            $obj = new $className();
            call_user_func([$obj, "up"]);
        }
    }

    public function serve() {
        $file = dirname($this->defaultDir) . '/Public/Index.php';

        shell_exec("php -S localhost:8000 {$file}");
    }

    public function test() {
        $env = new env();
        $dbh = new PDOConnection($env->DB_CONNECTION, $env->DB_NAME, $env->DB_HOST, $env->DB_PORT, $env->DB_USERNAME, $env->DB_PASSWORD);

        $sth = $dbh->prepare("SELECT 1 AS test_value");
        $sth->execute();
        $result = $sth->fetch();
        echo $result->test_value;
    }

    public function __call(string $name, array $args) {
        echo "$name command not found! (use php rise help)";
        exit(1);
    }
}