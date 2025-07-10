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
        Schema::create('member_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignUuid('membership_type_id')->constrained('membership_types')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['active', 'expired', 'cancelled', 'suspended'])->default('active');
            $table->boolean('auto_renew')->default(false);
            $table->date('renewal_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['member_id', 'status']);
            $table->index(['end_date']);
            $table->index(['renewal_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_subscriptions');
    }
};