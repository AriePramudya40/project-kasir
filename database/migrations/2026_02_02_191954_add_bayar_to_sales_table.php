<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('sales', function (Blueprint $table) {
        // Kita tambah kolom 'bayar' setelah kolom 'grand_total'
        $table->decimal('bayar', 15, 2)->default(0)->after('grand_total');
    });
}

public function down()
{
    Schema::table('sales', function (Blueprint $table) {
        $table->dropColumn('bayar');
    });
}
};
