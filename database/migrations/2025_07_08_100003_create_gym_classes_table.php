<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gym_classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trainer_id')->nullable()->constrained('trainers')->onDelete('set null');
            $table->string('class_name', 50);
            $table->text('description')->nullable();
            $table->string('class_type', 50)->nullable();
            $table->enum('schedule_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->integer('max_capacity');
            $table->integer('current_capacity')->default(0);
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('room', 50)->nullable();
            $table->text('equipment_needed')->nullable();
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['schedule_day', 'start_time']);
            $table->index(['trainer_id']);
            $table->index(['is_active']);
            $table->index(['class_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_classes');
    }
};
