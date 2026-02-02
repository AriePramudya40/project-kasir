<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- TAMBAHAN: TABEL SESSIONS (Yang tadi error) ---
    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });

    // 1. TABEL USERS (Karyawan)
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password')->nullable();
        $table->string('google_id')->nullable();
        $table->enum('role', ['admin', 'kasir'])->default('kasir');
        $table->timestamps();
    });

        // 2. TABEL PRODUK
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->decimal('harga', 15, 2);
            $table->integer('stok');
            $table->timestamps();
        });

        // 3. TABEL PENJUALAN
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->foreignId('user_id')->constrained(); // Kasir
            $table->decimal('subtotal', 15, 2);
            
            // Aturan Diskon
            $table->decimal('diskon', 15, 2)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users'); // ID Admin jika diskon > 10rb
            
            $table->decimal('grand_total', 15, 2);
            $table->enum('status', ['lunas', 'batal'])->default('lunas');
            $table->timestamps();
        });

        // 4. TABEL DETAIL BARANG
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('qty');
            $table->decimal('harga_saat_itu', 15, 2);
            $table->decimal('subtotal_line', 15, 2);
            $table->timestamps();
        });

        // 5. TABEL RETUR
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('tipe', ['dari_customer', 'ke_supplier']);
            $table->foreignId('product_id')->constrained();
            $table->integer('qty');
            $table->text('alasan'); // Rusak/Expired
            $table->boolean('kembali_ke_stok')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
    Schema::dropIfExists('sessions');
    Schema::dropIfExists('returns');
    Schema::dropIfExists('sale_items');
    Schema::dropIfExists('sales');
    Schema::dropIfExists('products');
    Schema::dropIfExists('users');
    }
};