<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scrum_issues', function (Blueprint $table) {
            $table->unsignedSmallInteger('story_points')->nullable()->after('priority');
            $table->decimal('estimated_hours', 6, 2)->nullable()->after('story_points');
            $table->decimal('spent_hours', 6, 2)->default(0.00)->after('estimated_hours');
        });

        // Modify status to include in_review
        DB::statement("ALTER TABLE `scrum_issues` MODIFY COLUMN `status` ENUM('todo', 'in_progress', 'in_review', 'done') NOT NULL DEFAULT 'todo'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `scrum_issues` MODIFY COLUMN `status` ENUM('todo', 'in_progress', 'done') NOT NULL DEFAULT 'todo'");

        Schema::table('scrum_issues', function (Blueprint $table) {
            $table->dropColumn(['story_points', 'estimated_hours', 'spent_hours']);
        });
    }
};
