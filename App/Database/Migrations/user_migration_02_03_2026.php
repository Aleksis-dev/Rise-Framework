<?php

namespace App\Database\Migrations;

use App\Rise\Core\Database\Schema\SchemaPlanner;
use App\Rise\Core\Database\Table\TableConstructor;

class user_migration_02_03_2026 {
    public function up(): void {
        SchemaPlanner::createTable("user", function(TableConstructor $table) {
            $table->id()
            ->float('amount');
        });
    }

    public function down(): void {
        SchemaPlanner::dropTable("user");
    }
}