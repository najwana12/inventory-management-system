<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_in', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('supplier_id');
        });

        Schema::table('goods_out', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('goods_in', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('goods_out', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
