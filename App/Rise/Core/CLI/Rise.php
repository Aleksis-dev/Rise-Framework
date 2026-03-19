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
        $file = dirname($this->defaultDir) . '/Public';

        shell_exec("php -S localhost:8000 -t {$file}");
    }

    public function __call(string $name, array $args) {
        echo "$name command not found! (use php rise help)";
        exit(1);
    }

    protected function readTemplate(string $fileName) {
        $templateDir = $this->defaultDir . "/Rise/Core/Templates";
        $file = $templateDir . "/{$fileName}";
        return file_get_contents($file);
    }

    protected function processTemplate(string $templateName, array $replaceData, string $placeDir) {
        $template = $this->readTemplate($templateName);
        $template = str_replace(array_keys($replaceData), array_values($replaceData), $template);

        file_put_contents($placeDir, $template);
    }


    public function createAll(string $name) {
        $this->createController($name);
        $this->createModel($name);
        $this->createMigration($name);
    }

    public function createController(string $name) {
        $nameUCFirst = ucfirst($name);
        $placeDir = $this->defaultDir . "/Api/Controllers/{$nameUCFirst}Controller.php";

        $replaceData = [
            "[DEPENDENCIES]" => implode("\n", [
                "use App\\Api\\Models\\{$nameUCFirst};",
                "use App\\Rise\\Core\\Requests\\Request;"
            ]),
            "[CONTROLLER_NAME]" => "{$nameUCFirst}Controller",
            "[CONTROLLER_OBJECT_NAME]" => "{$nameUCFirst}",
            "[CONTROLLER_OBJECT_NAME_CAMEL_CASE]" => "{$name}"
        ];

        $this->processTemplate("CONTROLLER_TEMPLATE", $replaceData, $placeDir);
    }

    public function createModel(string $name) {
        $nameUCFirst = ucfirst($name);

        $placeDir = $this->defaultDir . "/Api/Models/{$nameUCFirst}.php";

        $traits = [];

        $replaceData = [
            "[DEPENDENCIES]" => implode("\n", []),
            "[MODEL_NAME]" => "{$nameUCFirst}",
            "[TRAITS]" => ($traits !== [] ? "use " : "") . implode(", ", $traits) . ($traits !== [] ? ";" : "")
        ];

        $this->processTemplate("MODEL_TEMPLATE", $replaceData, $placeDir);
    }

    public function createMigration(string $name) {
        $migrationName = "{$name}_migration_" . date("d_m_y");
        $placeDir = $this->defaultDir . "/Database/Migrations/{$migrationName}.php";

        $replaceData = [
            "[MIGRATION_NAME]" => "{$migrationName}",
            "[MIGRATION_MODEL]" => "\"{$name}\""
        ];

        $this->processTemplate("MIGRATION_TEMPLATE", $replaceData, $placeDir);
    }
}