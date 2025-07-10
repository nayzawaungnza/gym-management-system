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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignUuid('membership_type_id')->nullable()->constrained('membership_types')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->datetime('payment_date');
            $table->foreignUuid('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->string('transaction_id', 100)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('description')->nullable();
            $table->string('receipt_number', 50)->nullable();
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['payment_date']);
            $table->index(['status']);
            $table->index(['member_id']);
            $table->index(['transaction_id']);
            $table->index(['receipt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};