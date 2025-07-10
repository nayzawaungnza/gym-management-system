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
        Schema::create('equipment_maintenance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->enum('maintenance_type', ['routine', 'repair', 'inspection', 'calibration']);
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('description');
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('technician_name', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->text('notes')->nullable();
            $table->json('parts_replaced')->nullable();
            $table->boolean('warranty_work')->default(false);
            $table->string('work_order_number', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['equipment_id', 'scheduled_date']);
            $table->index(['status']);
            $table->index(['maintenance_type']);
            $table->index(['scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance');
    }
};
