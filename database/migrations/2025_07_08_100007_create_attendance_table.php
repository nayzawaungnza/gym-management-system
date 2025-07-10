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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('member_id')->constrained('members')->onDelete('cascade');
            $table->datetime('check_in_time');
            $table->datetime('check_out_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('check_in_method', ['manual', 'qr_code', 'rfid', 'biometric'])->default('manual');
            $table->string('location', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['member_id', 'check_in_time']);
            $table->index(['check_in_time']);
            $table->index(['check_in_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};