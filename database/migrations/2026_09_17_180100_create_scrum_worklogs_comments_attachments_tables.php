<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Work Logs
        Schema::create('issue_work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrum_issue_id')->constrained('scrum_issues')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('hours_spent', 6, 2);
            $table->date('logged_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Comments
        Schema::create('issue_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrum_issue_id')->constrained('scrum_issues')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        // 3. Attachments
        Schema::create('issue_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrum_issue_id')->constrained('scrum_issues')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('filename');
            $table->string('original_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_attachments');
        Schema::dropIfExists('issue_comments');
        Schema::dropIfExists('issue_work_logs');
    }
};
