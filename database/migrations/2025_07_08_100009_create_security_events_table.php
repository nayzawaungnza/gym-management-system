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
        Schema::create('security_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('event_type', [
                'login_success', 
                'login_failed', 
                'password_reset', 
                'email_verified', 
                '2fa_enabled', 
                '2fa_disabled', 
                'suspicious_activity'
            ]);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('event_data')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->boolean('is_resolved')->default(false);
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['event_type']);
            $table->index(['risk_level']);
            $table->index(['created_at']);
            $table->index(['is_resolved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};