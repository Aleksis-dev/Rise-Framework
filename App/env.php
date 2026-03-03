<?php

namespace App;
use stdClass;

class env extends stdClass {
    public function __construct() {
        $envConfs = explode("\n", file_get_contents(dirname(__DIR__) . "/.env"));

        foreach ($envConfs as $key => $envConf) {
            if ($envConf[0] == "#") {
                continue;
            }

            $arr = explode("=", $envConf);

            if (!isset($arr[1])) {
                continue;
            }

            $this->{trim($arr[0])} = trim($arr[1]);
        }
    }
};