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
        Schema::create('login_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at')->useCurrent();
            $table->timestamp('logout_at')->nullable();
            $table->boolean('is_successful')->default(true);
            $table->string('failure_reason')->nullable();
            $table->string('location_country', 100)->nullable();
            $table->string('location_city', 100)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'login_at']);
            $table->index(['ip_address']);
            $table->index(['login_at']);
            $table->index(['is_successful']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};