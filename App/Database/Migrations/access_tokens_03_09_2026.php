<?php

namespace App\Database\Migrations;

use App\Rise\Core\Database\Schema\SchemaPlanner;
use App\Rise\Core\Database\Table\TableConstructor;

class access_tokens_03_09_2026 {
    public function up(): string {
        return SchemaPlanner::createTable("access_tokens", function (TableConstructor $table) {
            $table->id()
            ->morph("tokenable")
            ->tinytext("token");
        });
    }

    public function down(): string {
        return SchemaPlanner::dropTable("access_tokens");
    }
}