<?php

namespace App\Rise\Core\CLI;

use App\Rise\Core\Helpers\Files\Searching\SearchFile;

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

    public function __call(string $name, array $args) {
        echo "$name command not found! (use php rise help)";
        exit(1);
    }
}