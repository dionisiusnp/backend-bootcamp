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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_delivery')->default(false)->change();
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id']);
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_delivery')->default(true)->change();
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id']);
            $table->foreignId('payment_method_id')->constrained('payment_methods')->change();
        });
    }
};
