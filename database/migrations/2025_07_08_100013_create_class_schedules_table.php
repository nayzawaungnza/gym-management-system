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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_id')->constrained('gym_classes')->onDelete('cascade');
            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignUuid('trainer_id')->nullable()->constrained('trainers')->onDelete('set null');
            $table->string('room', 50)->nullable();
            $table->integer('max_capacity');
            $table->integer('current_registrations')->default(0);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['class_id', 'class_date', 'start_time'], 'unique_class_schedule');
            $table->index(['class_date', 'start_time']);
            $table->index(['trainer_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
