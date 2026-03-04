<?php

namespace App\Rise\Core\Database\Execution\Model;
use stdClass;

class Model extends stdClass {

    protected $fillable;

    public static function create(array $data) {
        $class = get_called_class();
        $initializedClass = new $class();

        $fillable = $initializedClass->fillable ?? [];

        $fillable = array_fill_keys($fillable, true);
        
        $array_diff_keys = array_diff_key($data, $fillable);

        if ($array_diff_keys) {
            echo "\nModel Creation Exception: \n\n";
            foreach ($array_diff_keys as $key => $array_diff_key) {
                echo "{$key} is not a fillable property field!\n";
            }
            echo "\nend of stacktrace.\n\n";
            exit(1);
        }

        foreach ($data as $key => $data_sing) {
            if ($key == "password") {
                $initializedClass->{$key} = password_hash($data_sing, PASSWORD_ARGON2ID);
                continue;
            }
            $initializedClass->{$key} = $data_sing;
        }

        return $initializedClass;
    }
}