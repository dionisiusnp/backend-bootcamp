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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->integer('total_payment')->default(0);
            $table->boolean('is_delivery')->default(true);
            $table->boolean('is_payment')->default(false);
            $table->boolean('is_accept')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
