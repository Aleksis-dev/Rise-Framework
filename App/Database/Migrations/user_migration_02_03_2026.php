<?php

namespace App\Database\Migrations;

use App\Rise\Core\Database\Schema\SchemaPlanner;
use App\Rise\Core\Database\Table\TableConstructor;

class user_migration_02_03_2026 {
    public function up(): string {
        return SchemaPlanner::createTable("user", function(TableConstructor $table) {
            $table->id()
            ->tinytext("name")
            ->password()
            ->int_unsigned_not_null("coins")
            ->timestamp();
        });
    }

    public function down(): string {
        return SchemaPlanner::dropTable("user");
    }
}