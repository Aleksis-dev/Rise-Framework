<?php

namespace App;
use stdClass;

class env extends stdClass {
    public function __construct() {
        $path = dirname(__DIR__) . "/.env";
        if (!file_exists($path)) {return;}

        $envConfs = explode("\n", file_get_contents($path));

        foreach ($envConfs as $key => $envConf) {
            if (isset($envConf[0]) && $envConf[0] == "#") {
                continue;
            }

            $arr = explode("=", $envConf);

            if (!isset($arr[1])) {
                continue;
            }

            $this->{trim($arr[0])} = trim($arr[1]);
        }
    }

    public static function createEnv() {
        $envTemplate = file_get_contents(dirname(__DIR__) . "/App/Rise/Core/Templates/ENV_TEMPLATE");
        file_put_contents(dirname(__DIR__) . "/.env", $envTemplate);
    }
};