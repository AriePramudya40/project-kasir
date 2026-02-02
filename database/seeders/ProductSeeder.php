<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- JANGAN LUPA TAMBAHKAN INI

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key sementara
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel
        DB::table('products')->truncate();

        // 3. Hidupkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();

        // 4. Isi data baru
        $data = [
            ['kode'=>'S001', 'nama'=>'Semen Tiga Roda 50kg', 'harga'=>65000, 'stok'=>100],
            ['kode'=>'S002', 'nama'=>'Semen Padang 50kg', 'harga'=>63000, 'stok'=>100],
            ['kode'=>'C001', 'nama'=>'Cat Dulux Putih 5kg', 'harga'=>125000, 'stok'=>20],
            ['kode'=>'C002', 'nama'=>'Cat Avian Kayu Hitam', 'harga'=>45000, 'stok'=>50],
            ['kode'=>'P001', 'nama'=>'Paku Payung 1kg', 'harga'=>15000, 'stok'=>200],
            ['kode'=>'B001', 'nama'=>'Besi Beton 8mm', 'harga'=>42000, 'stok'=>100],
        ];

        DB::table('products')->insert($data);
    }
}