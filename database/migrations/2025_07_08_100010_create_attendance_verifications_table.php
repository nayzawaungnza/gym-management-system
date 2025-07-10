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
        Schema::create('attendance_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignUuid('member_id')->constrained('members')->onDelete('cascade');
            $table->enum('verification_method', ['qr_code', 'rfid', 'biometric', 'photo', 'manual']);
            $table->json('verification_data')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('device_info')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('qr_token')->nullable();
            $table->string('rfid_code')->nullable();
            $table->string('biometric_hash')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->foreignUuid('flagged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('flagged_at')->nullable();
            $table->string('flag_reason')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['verification_status']);
            $table->index(['is_flagged']);
            $table->index(['verification_method']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_verifications');
    }
};