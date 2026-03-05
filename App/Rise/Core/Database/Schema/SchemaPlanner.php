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

        return "CREATE TABLE {$tableName}_table " . "(\n" . $str . ")";
    }  

    public static function dropTable(string $tableName) {
        return "DROP TABLE IF EXISTS {$tableName}_table";
    }
}