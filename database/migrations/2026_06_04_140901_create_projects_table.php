<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('deadline');
            $table->text('description')->nullable();
            $table->enum('status', ['Planning', 'Active', 'Completed', 'Delayed', 'On Hold'])->default('Planning');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
