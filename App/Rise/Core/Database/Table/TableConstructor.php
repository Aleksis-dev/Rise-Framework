<?php

namespace App\Rise\Core\Database\Table;

class TableConstructor {
    private array $tableQuery = [];

    public function foreign(string $key) {
        $this->tableQuery[] = "FOREIGN KEY " . "($key) ";
        return $this;
    }

    public function password(string $passwordName = "password") {
        $this->tableQuery[] = "{$passwordName} TINYTEXT";
        return $this;
    }

    public function references(string $key) {
        $this->tableQuery[array_key_last($this->tableQuery)] .= "REFERENCES [table](" .  $key . ")";
        return $this;
    }

    public function on(string $table) {
        $this->tableQuery[array_key_last($this->tableQuery)] = str_replace("[table]", $table, $this->tableQuery[array_key_last($this->tableQuery)]);
        return $this;
    }

    public function id(string $id = "[name]") {
        $this->tableQuery[] = "$id INT AUTO_INCREMENT";
        $this->tableQuery[] = "PRIMARY KEY ($id)";
        return $this;
    }

    public function timestamp(?string $name = null, ?int $precision = null) {
        if (!isset($name)) {
            $this->tableQuery[] = "created_at DATETIME";
            $this->tableQuery[] = "updated_at DATETIME";
        } else {
            $precision = $precision ? "({$precision})" : "";
            $this->tableQuery[] = "{$name} DATETIME{$precision}";
        }
    }

    public function __call(string $name, array $args) {
        $str = implode(" ", array_slice($args, 1));
        if ($str !== "") {
            $str = "($str)";
        }
        $this->tableQuery[] = $args[0] . " " . strtoupper(str_replace("_", " ", $name)) . $str;
        return $this;
    }

    public function getQuery() {
        return $this->tableQuery;
    }
}