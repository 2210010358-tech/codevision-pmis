<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestones')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->date('deadline');
            $table->decimal('estimated_hours', 8, 2);
            $table->decimal('actual_hours', 8, 2)->default(0.00);
            $table->integer('progress_percentage')->default(0);
            $table->enum('status', ['To Do', 'In Progress', 'Done'])->default('To Do');
            $table->text('notes')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('commit_hash')->nullable();
            $table->string('commit_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
