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
        Schema::create('trainers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('trainer_id', 20)->unique();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('phone', 15)->nullable();
            $table->string('specialization', 100)->nullable();
            $table->json('certifications')->nullable();
            $table->date('hire_date');
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['trainer_id']);
            $table->index(['email']);
            $table->index(['is_active']);
            $table->index(['hire_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};