<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('nominal', 15, 2);
            $table->string('payment_method'); // cash / online
            $table->string('external_id')->nullable(); // ID dari Xendit
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_payments');
    }
};