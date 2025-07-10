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
        Schema::create('class_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignUuid('class_id')->constrained('gym_classes')->onDelete('cascade');
            $table->datetime('registration_date');
            $table->date('class_date');
            $table->enum('status', ['registered', 'attended', 'cancelled', 'no_show'])->default('registered');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['member_id', 'class_id', 'class_date'], 'unique_member_class_date');
            $table->index(['class_date']);
            $table->index(['status']);
            $table->index(['payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_registrations');
    }
};
