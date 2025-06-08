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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('commission_value', 10, 2)->default(0);
            $table->unsignedBigInteger('seller_id')->nullable();

            $table->foreign('seller_id', 'sales_seller_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('sales_seller_id_foreign');

            $table->dropColumn('commission_value');
            $table->dropColumn('seller_id');
        });
    }
};
