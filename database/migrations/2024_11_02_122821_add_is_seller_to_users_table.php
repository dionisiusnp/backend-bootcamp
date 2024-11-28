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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_seller')->default(false);
            $table->string('address');
            $table->boolean('is_active')->default(true);
            $table->string('inactive_reason')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_seller');
            $table->dropColumn('address');
            $table->dropColumn('is_active');
            $table->dropColumn('inactive_reason');
            $table->dropSoftDeletes();
        });
    }
};