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
        Schema::table('payments', function (Blueprint $table) {
       
            $table->uuid('member_subscription_id')->nullable();
$table->foreign('member_subscription_id')
    ->references('id')
    ->on('member_subscriptions')
    ->onDelete('set null');

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

             $table->dropForeign(['member_subscription_id']);
        $table->dropColumn('member_subscription_id');
        });
    }
};