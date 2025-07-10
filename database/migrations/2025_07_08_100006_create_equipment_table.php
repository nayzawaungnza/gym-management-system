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
        Schema::create('equipment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('equipment_name', 50);
            $table->string('equipment_type', 50)->nullable();
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('maintenance_interval_days')->default(90);
            $table->string('location', 100)->nullable();
            $table->enum('status', ['operational', 'under_maintenance', 'out_of_service', 'retired'])->default('operational');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status']);
            $table->index(['next_maintenance_date']);
            $table->index(['equipment_type']);
            $table->index(['location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};