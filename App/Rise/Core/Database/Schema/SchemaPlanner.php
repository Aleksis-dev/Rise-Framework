<?php

namespace App\Rise\Core\Database\Schema;
use App\Rise\Core\Database\Table\TableConstructor;

class SchemaPlanner {
    public static function createTable(string $tableName, callable $callback) {
        $table = new TableConstructor();

        $tableName = strtolower($tableName);

        $callback($table);

        $str = implode(",\n", $table->getQuery());

        $str = str_replace("[name]", $tableName . "_id", $str);

        $stmt = "CREATE TABLE {$tableName}_table " . "(\n" . $str . ");";

        $str = "\n" . implode("\n", $table->getSpecialQueries());

        $str = str_replace("[table]", "{$tableName}_table", $str);

        $stmt = $stmt . $str;

        return $stmt;
    }  

    public static function dropTable(string $tableName) {
        return "DROP TABLE IF EXISTS {$tableName}_table";
    }
}