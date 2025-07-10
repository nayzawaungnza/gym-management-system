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
        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('membership_type_id')->nullable()->constrained('membership_types')->onDelete('set null');
            $table->string('member_id', 20)->unique();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('phone', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            $table->date('join_date');
            $table->date('membership_start_date')->nullable();
            $table->date('membership_end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->string('profile_photo')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->json('fitness_goals')->nullable();
            $table->string('preferred_workout_time', 50)->nullable();
            $table->string('referral_source', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['member_id']);
            $table->index(['email']);
            $table->index(['status']);
            $table->index(['membership_end_date']);
            $table->index(['join_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};